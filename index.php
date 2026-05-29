<?php
session_start();
require_once 'db_connect.php';
require_once 'config.php'; // 공통 설정 파일 (SITE_TITLE 사용)

// 메인 화면에 보여줄 가장 최신 사진 1장 가져오기
try {
    $stmt = $pdo->query("SELECT * FROM photos ORDER BY uploaded_at DESC LIMIT 1");
    $photo = $stmt->fetch();
} catch (PDOException $e) {
    die("데이터베이스 오류: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* 푸터 바닥 고정을 위한 레이아웃 설정 */
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .content-wrapper {
            flex: 1 0 auto;
        }
        
        /* 메인 피처드 이미지 스타일 */
        .featured-img-container {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            background-color: #000;
        }
        .featured-img {
            width: 100%;
            max-height: 70vh;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* 세련된 EXIF 인포 박스 스타일 */
        .exif-info-box {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(13, 43, 91, 0.08);
            padding: 25px;
            border-left: 6px solid var(--text-main);
            margin-top: 30px;
        }
        .exif-item {
            margin-bottom: 20px;
        }
        .exif-label {
            font-size: 0.75rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .exif-value {
            font-size: 1rem;
            font-weight: 700;
            color: #222;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .exif-value span.icon {
            font-size: 1.2rem;
            opacity: 0.8;
        }
        
        /* 비밀 로그인 링크용 스타일 (navbar.php 연동) */
        .secret-login-link {
            display: inline-block;
            width: 8px;
            height: 20px;
            text-decoration: none;
            cursor: pointer;
            margin-left: 4px;
        }
    </style>
</head>
<body>

<div class="content-wrapper">

    <?php include 'navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <?php if (!$photo): ?>
            <div class="row justify-content-center py-5">
                <div class="col-md-8 text-center">
                    <h2 class="fw-bold mb-3">Welcome to Skyremix Studio</h2>
                    <p class="text-muted fs-5 mb-4">아직 등록된 작품이 없습니다. 사진을 업로드해 갤러리를 채워보세요!</p>
                    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                        <a href="upload.php" class="btn btn-primary shadow-sm fw-bold px-4 py-2">첫 사진 올리러 가기</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="text-center mb-4">
                        <span class="badge bg-secondary mb-2" style="letter-spacing: 1px; font-size: 0.75rem; padding: 5px 10px;">LATEST WORK</span>
                        <h1 class="fw-bold display-5 mb-2" style="letter-spacing: -1px;"><?= htmlspecialchars($photo['title']) ?></h1>
                    </div>

                    <div class="featured-img-container mb-4">
                        <img src="<?= htmlspecialchars($photo['s3_url']) ?>" class="featured-img" alt="<?= htmlspecialchars($photo['title']) ?>">
                    </div>

                    <div class="exif-info-box">
                        <h5 class="fw-bold mb-4" style="letter-spacing: -0.5px;">Camera Details</h5>
                        
                        <div class="row">
                            <div class="col-6 col-md-4 exif-item">
                                <span class="exif-label">Camera</span>
                                <div class="exif-value"><span class="icon">📷</span> <?= htmlspecialchars($photo['camera_model']) ?: 'Unknown' ?></div>
                            </div>
                            
                            <div class="col-6 col-md-4 exif-item">
                                <span class="exif-label">Aperture</span>
                                <div class="exif-value"><span class="icon">⭕</span> <?= htmlspecialchars($photo['aperture']) ?: '-' ?></div>
                            </div>
                            
                            <div class="col-6 col-md-4 exif-item">
                                <span class="exif-label">Shutter</span>
                                <div class="exif-value"><span class="icon">⏱️</span> <?= htmlspecialchars($photo['shutter_speed']) ? htmlspecialchars($photo['shutter_speed']) . 's' : '-' ?></div>
                            </div>
                            
                            <div class="col-6 col-md-4 exif-item">
                                <span class="exif-label">ISO</span>
                                <div class="exif-value"><span class="icon">☀️</span> <?= htmlspecialchars($photo['iso']) ?: '-' ?></div>
                            </div>
                            
                            <div class="col-6 col-md-4 exif-item">
                                <span class="exif-label">Focal Length</span>
                                <div class="exif-value">
                                    <span class="icon">🎯</span> 
                                    <?php 
                                        if (!empty($photo['focal_length'])) {
                                            echo round(floatval($photo['focal_length']), 1) . 'mm'; 
                                        } else {
                                            echo '-';
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 exif-item">
                                <span class="exif-label"><?= !empty($photo['taken_at']) ? 'Shot Date' : 'Uploaded' ?></span>
                                <div class="exif-value">
                                    <span class="icon">📅</span> 
                                    <?= !empty($photo['taken_at']) ? date('Y.m.d', strtotime($photo['taken_at'])) : date('Y.m.d', strtotime($photo['uploaded_at'])) ?>
                                </div>
                            </div>
                        </div>
                    </div> <div class="text-center mt-5">
                        <a href="photos" class="btn btn-outline-dark fw-bold px-4 py-2 shadow-sm" style="border-radius: 30px;">
                            View Full Gallery →
                        </a>
                    </div>

                </div>
            </div>
        <?php endif; ?>
    </div>

</div> <?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>