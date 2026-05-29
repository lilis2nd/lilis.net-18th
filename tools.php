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

<main class="container my-5">
    <div class="row">
        
        <aside class="col-lg-3 mb-4">
            <div class="sticky-top" style="top: 100px;">
                <h4 class="mb-4 pb-2 border-bottom" style="color: var(--text-main);">Tool Index</h4>
                <ul class="nav flex-column custom-toc">
                    <li class="nav-item"><a class="nav-link active" href="#tool-1">유용한 도구 1</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tool-2">유용한 도구 2</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tool-3">유용한 도구 3</a></li>
                </ul>
            </div>
        </aside>

        <div class="col-lg-9">
            <div class="row row-cols-1 row-cols-md-2 g-4">
                
                <div class="col" id="tool-1">
                    <div class="card h-100 tool-card">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--text-main);">유용한 도구 1</h5>
                            <p class="card-text text-muted">이곳에 개인적으로 개발한 첫 번째 유용한 툴의 설명이나 링크가 들어갈 예정입니다.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-end">
                            <button class="btn btn-sm" class="btn btn-dark btn-sm">열기</button>
                        </div>
                    </div>
                </div>

                <div class="col" id="tool-2">
                    <div class="card h-100 tool-card">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--text-main);">유용한 도구 2</h5>
                            <p class="card-text text-muted">추후 추가될 두 번째 툴의 설명입니다.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-end">
                            <button class="btn btn-sm" class="btn btn-dark btn-sm">열기</button>
                        </div>
                    </div>
                </div>

                <div class="col" id="tool-3">
                    <div class="card h-100 tool-card">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--text-main);">유용한 도구 3</h5>
                            <p class="card-text text-muted">세 번째 툴을 위한 예시 공간입니다.</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-end">
                            <button class="btn btn-sm" class="btn btn-dark btn-sm">열기</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>