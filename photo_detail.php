<?php
// photo_detail.php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$photo_id = $_GET['id'] ?? null;
$photo = null;

if ($photo_id) {
    try {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
        $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        $stmt = $pdo->prepare("SELECT * FROM photos WHERE id = ?");
        $stmt->execute([$photo_id]);
        $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
}

// 잘못된 접근 처리
if (!$photo) {
    echo "<script>alert('존재하지 않는 사진입니다.'); location.href='photos.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?> - Lilis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    </nav>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <a href="photos.php" class="btn btn-outline-secondary mb-4">&larr; 갤러리로 돌아가기</a>
            
            <img src="<?= htmlspecialchars($photo['s3_url'], ENT_QUOTES, 'UTF-8') ?>" 
                 class="detail-photo-img shadow" 
                 alt="<?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 style="color: var(--primary-dark);"><?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                <button class="btn btn-sm btn-danger">삭제</button>
            </div>

            <div class="metadata-box mb-5">
                <div class="row">
                    <div class="col-md-4 metadata-item">
                        <strong>업로드:</strong> <?= date('Y-m-d H:i', strtotime($photo['uploaded_at'])) ?>
                    </div>
                    <div class="col-md-4 metadata-item">
                        <strong>카메라:</strong> <?= htmlspecialchars($photo['camera_model'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="col-md-4 metadata-item">
                        <strong>조리개:</strong> f/<?= htmlspecialchars($photo['aperture'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="col-md-4 metadata-item">
                        <strong>셔터스피드:</strong> <?= htmlspecialchars($photo['shutter_speed'], ENT_QUOTES, 'UTF-8') ?>s
                    </div>
                    <div class="col-md-4 metadata-item">
                        <strong>ISO:</strong> <?= htmlspecialchars($photo['iso'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="col-md-4 metadata-item">
                        <strong>초점거리:</strong> <?= htmlspecialchars($photo['focal_length'], ENT_QUOTES, 'UTF-8') ?>mm
                    </div>
                </div>
            </div>

            <hr>

            <div class="mt-5">
                <h4 style="color: var(--primary-dark); mb-4">Comments</h4>
                
                <div class="card mb-3 border-0" style="background-color: #fff;">
                    <div class="card-body">
                        <h6 class="fw-bold" style="color: var(--primary-mint);">Visitor123 <span class="text-muted small fw-normal ms-2">2026-05-27 10:00</span></h6>
                        <p class="card-text">사진 구도가 너무 아름답네요!</p>
                    </div>
                </div>

                <div class="card border-0 mt-4" style="background-color: #fff;">
                    <div class="card-body">
                        <form action="comment_process.php" method="POST">
                            <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="author" placeholder="이름을 입력하세요" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="content" rows="3" placeholder="댓글을 남겨주세요" required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn" style="background-color: var(--primary-dark); color: #fff;">댓글 등록</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>