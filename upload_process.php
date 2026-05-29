<?php
session_start();
// 로그인 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("권한이 없습니다.");
}

require 'vendor/autoload.php';
require 'db_connect.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'General';
    $file = $_FILES['photo'];

    // 1. 파일 업로드 에러 체크
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("파일 업로드 중 에러가 발생했습니다. (Error Code: " . $file['error'] . ")");
    }

    $tmpPath = $file['tmp_name'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // --- HEIC 초간단 서버 변환 로직 (heif-convert 명령어 사용) ---
    if ($extension === 'heic') {
        $newTmpPath = $tmpPath . '_converted.jpg';
        
        // 리눅스의 heif-convert 명령어를 PHP에서 직접 실행
        $command = "heif-convert " . escapeshellarg($tmpPath) . " " . escapeshellarg($newTmpPath) . " 2>&1";
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            // 변환 성공 시, 원본 경로를 생성된 JPG 경로로 교체
            $tmpPath = $newTmpPath;
            $extension = 'jpg';
        } else {
            // 변환 실패 시 에러 출력
            die("서버 HEIC 변환 실패: " . implode(" ", $output));
        }
    }
    // --- 여기까지 ---
    
    // 2. EXIF 메타데이터 자동 추출 (JPEG/TIFF 형식이 아닐 경우 에러 방지를 위해 @ 사용)
    $exif = @exif_read_data($tmpPath);
    
    $cameraMake = $exif['Make'] ?? null;
    $cameraModel = $exif['Model'] ?? null;
    $aperture = $exif['COMPUTED']['ApertureFNumber'] ?? null;
    $shutterSpeed = $exif['ExposureTime'] ?? null;
    $iso = $exif['ISOSpeedRatings'] ?? null;
    
    // 초점 거리(Focal Length) 계산
    $focalLength = null;
    if (isset($exif['FocalLength'])) {
        $parts = explode('/', $exif['FocalLength']);
        if (count($parts) == 2 && $parts[1] != 0) {
            $focalLength = ($parts[0] / $parts[1]) . 'mm';
        } else {
            $focalLength = $exif['FocalLength'] . 'mm';
        }
    }

    // --- [여기부터 수정] 촬영 날짜(DateTimeOriginal) 추출 및 MySQL 포맷 변환 ---
    $takenAt = null;
    if (!empty($exif['DateTimeOriginal'])) {
        // EXIF 날짜 포맷(YYYY:MM:DD)을 PHP가 인식할 수 있게 변환
        $dateStr = str_replace(':', '-', substr($exif['DateTimeOriginal'], 0, 10)) . substr($exif['DateTimeOriginal'], 10);
        $takenAt = date('Y-m-d H:i:s', strtotime($dateStr));
    } elseif (!empty($exif['DateTime'])) {
        $dateStr = str_replace(':', '-', substr($exif['DateTime'], 0, 10)) . substr($exif['DateTime'], 10);
        $takenAt = date('Y-m-d H:i:s', strtotime($dateStr));
    }
    // --- [여기까지] ---

    // S3 업로드 시 사용할 기본 MIME 타입 설정
    $mimeType = mime_content_type($tmpPath);

    // S3 업로드 시 사용할 기본 MIME 타입 설정
    $mimeType = mime_content_type($tmpPath);

    // --- [새 기능] 이미지 용량 최적화(WebP, 리사이징) 및 워터마크 로직 시작 ---
    try {
        $image = new Imagick($tmpPath);
        
        // 1. 가로폭 2560px 리사이징 (종횡비 유지)
        $maxWidth = 2560;
        $imageWidth = $image->getImageWidth();
        if ($imageWidth > $maxWidth) {
            $image->scaleImage($maxWidth, 0); // 세로 0: 자동 비율
            $imageWidth = $maxWidth; 
        }

        // 2. 워터마크 폰트 크기를 동적 계산 (가로폭의 약 3%)
        $fontSize = max(20, $imageWidth * 0.03); 
        $draw = new ImagickDraw();
        $draw->setFontSize($fontSize);
        $draw->setFontWeight(800); 
        $draw->setFillColor(new ImagickPixel('rgba(255, 255, 255, 0.65)')); 
        $draw->setGravity(Imagick::GRAVITY_SOUTHEAST); 
        
        // 워터마크 텍스트 입력
        $watermarkText = "Skyremix Studio";
        $image->annotateImage($draw, 30, 30, 0, $watermarkText);
        
        // 3. WebP 포맷 변환 및 화질 압축
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality(85);
        
        // 4. 최적화된 이미지를 임시 파일에 덮어쓰고 확장자/MIME 타입 변경
        $image->writeImage($tmpPath);
        $extension = 'webp';
        $mimeType = 'image/webp';
        
        // 메모리 정리
        $draw->clear();
        $draw->destroy();
        $image->clear();
        $image->destroy();
    } catch (Exception $e) {
        // 최적화에 실패하더라도 전체 업로드가 멈추지 않도록 에러만 기록
        error_log("이미지 최적화/워터마크 삽입 실패: " . $e->getMessage());
    }
    // --- [새 기능] 이미지 용량 최적화 로직 끝 ---
    
    // 3. AWS S3에 파일 업로드 설정
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $_ENV['AWS_REGION'],
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ]
    ]);

    // 확장자가 webp로 업데이트된 고유 파일명 생성
    $newFileName = uniqid('img_') . '.' . $extension;
    $bucket = $_ENV['AWS_BUCKET'];

    try {
        // S3로 파일 전송!
        $result = $s3->putObject([
            'Bucket'      => $bucket,
            'Key'         => 'uploads/' . $newFileName,
            'SourceFile'  => $tmpPath,
            'ContentType' => $mimeType, // webp로 업데이트된 MIME 타입 적용
        ]);

        $imageUrl = $result->get('ObjectURL');

        // 4. DB에 사진 정보 및 추출한 EXIF 데이터 저장 (taken_at 추가)
        $sql = "INSERT INTO photos (title, s3_url, camera_model, aperture, shutter_speed, iso, focal_length, category, taken_at) 
                VALUES (:title, :s3_url, :model, :aperture, :shutter, :iso, :focal, :category, :taken_at)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title'    => $title,
            ':s3_url'   => $imageUrl,
            ':model'    => $cameraModel,
            ':aperture' => $aperture,
            ':shutter'  => $shutterSpeed,
            ':iso'      => $iso,
            ':focal'    => $focalLength,
            ':category' => $category,
            ':taken_at' => $takenAt // 촬영 날짜 바인딩
        ]);

        // 5. 성공 시 갤러리 메인으로 이동
        header("Location: photos");
        exit;

    } catch (AwsException $e) {
        die("AWS S3 업로드 실패: " . $e->getMessage());
    } catch (PDOException $e) {
        die("데이터베이스 저장 실패: " . $e->getMessage());
    }
} else {
    header("Location: upload");
    exit;
}