<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?error=required');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사진 업로드 - Lilis Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm" style="border-top: 4px solid var(--accent);">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4">새 사진 업로드</h3>
                    <form action="upload_process.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">사진 제목</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label fw-bold">카테고리</label>
                            <select class="form-select" id="category" name="category">
                                <option value="General">General (기본)</option>
                                <option value="Landscape">Landscape (풍경)</option>
                                <option value="Portrait">Portrait (인물)</option>
                                <option value="Street">Street (스트릿)</option>
                                <option value="B&W">B&W (흑백)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="photo" class="form-label fw-semibold">사진 파일 (JPG, PNG, HEIC)</label>
                            <input class="form-control" type="file" id="photo" name="photo" accept="image/jpeg, image/png, image/webp, .heic, .HEIC" required>
                            <div class="form-text mt-2">아이폰의 HEIC 파일은 서버에서 자동으로 JPG로 변환됩니다.</div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="photos.php" class="btn btn-outline-secondary">CANCEL</a>
                            <button type="submit" class="btn btn-dark shadow-sm">UPLOAD</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>