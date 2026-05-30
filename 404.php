<?php require_once 'config.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> | 404 Not Found</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="content-wrapper" style="min-height: 100vh; display: flex; flex-direction: column;">
    <?php include 'navbar.php'; ?>

    <main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <h1 class="display-1 fw-bold mb-3" style="font-family: 'Azeret Mono', monospace; color: var(--text-main); letter-spacing: -3px; font-size: 6rem;">404</h1>
            
            <h3 class="h4 mb-4" style="color: var(--text-main);">이곳엔 아무것도 없네요.</h3>
            
            <p class="lead mb-5" style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.8;">
                찾으시는 페이지의 주소가 잘못 입력되었거나,<br>
                현재는 삭제되어 사라진 공간입니다.<br>
                다시 빛을 찾아 갤러리로 돌아가 볼까요?
            </p>
            
            <a href="photos" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm" style="font-weight: 600; letter-spacing: 0.5px;">
                갤러리로 돌아가기 &rarr;
            </a>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>