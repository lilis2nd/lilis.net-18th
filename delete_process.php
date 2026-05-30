<?php
session_start();

// 1. 관리자 권한 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("권한이 없습니다.");
}

require 'vendor/autoload.php';
require 'db_connect.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        // 2. DB에서 삭제할 사진의 S3 URL 가져오기
        $stmt = $pdo->prepare("SELECT s3_url FROM photos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $photo = $stmt->fetch();

        if ($photo) {
            $s3Url = $photo['s3_url'];
            
            // 💡 [수정] 어떤 형태의 S3 URL이든 정확하게 'uploads/...' 키 값만 추출하도록 변경
            $keyPosition = strpos($s3Url, 'uploads/');
            if ($keyPosition !== false) {
                $s3Key = substr($s3Url, $keyPosition);
            } else {
                $parsedUrl = parse_url($s3Url, PHP_URL_PATH);
                $s3Key = ltrim($parsedUrl, '/');
            }

            // 3. AWS S3에서 실제 파일 삭제
            $s3 = new S3Client([
                'version' => 'latest',
                'region'  => $_ENV['AWS_REGION'],
                'credentials' => [
                    'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                    'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
                ]
            ]);

            try {
                $s3->deleteObject([
                    'Bucket' => $_ENV['AWS_BUCKET'],
                    'Key'    => $s3Key
                ]);
            } catch (AwsException $e) {
                // S3 파일이 이미 없더라도 DB 데이터는 지울 수 있게 에러 무시 또는 로그 기록
                error_log("S3 Delete Error: " . $e->getMessage());
            }

            // 4. DB에서 사진 정보 완전 삭제
            $delStmt = $pdo->prepare("DELETE FROM photos WHERE id = :id");
            $delStmt->execute([':id' => $id]);
        }
        
        // 5. 완료 후 갤러리로 돌아가기
        header("Location: photos");
        exit;

    } catch (PDOException $e) {
        die("데이터베이스 에러: " . $e->getMessage());
    }
} else {
    header("Location: photos");
    exit;
}