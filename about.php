<?php require_once 'config.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<main class="container my-5 text-center d-flex align-items-center justify-content-center" style="min-height: 60vh;">
    <div>
        <h1 class="display-4 fw-bold" style="color: var(--primary-dark);">About Me</h1>
        <div class="my-4">
            <span style="font-size: 4rem;">🚧</span>
        </div>
        <h3 class="h4" style="color: var(--accent-pink);">페이지 준비 중입니다</h3>
        <p class="lead text-muted mt-3">
            더 나은 콘텐츠를 위해 현재 페이지를 공사 중입니다.<br>
            관련된 소개 내용은 곧 추가될 예정입니다.
        </p>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>