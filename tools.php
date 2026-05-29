<?php require_once 'config.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> | Web Tools</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .tool-showcase-card {
            background-color: var(--white);
            border: 1px solid var(--accent);
            border-radius: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .tool-showcase-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(44, 42, 41, 0.08);
        }
        .tech-badge {
            background-color: var(--bg-secondary);
            color: var(--text-muted);
            border: 1px solid var(--accent);
            font-weight: 500;
            font-size: 0.8rem;
            padding: 5px 12px;
            font-family: 'Azeret Mono', monospace;
        }
        .version-tag {
            font-family: 'Azeret Mono', monospace;
            font-size: 0.85rem;
            color: var(--text-muted);
            background-color: var(--bg-primary);
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px dashed var(--accent);
        }
    </style>
</head>
<body>

<div class="content-wrapper" style="min-height: 100vh; display: flex; flex-direction: column;">
    <?php include 'navbar.php'; ?>

    <main class="container my-5 flex-grow-1">
        <div class="row mb-5 justify-content-center">
            <div class="col-lg-8 text-center mt-4">
                <h1 class="display-4 fw-bold" style="font-family: 'Azeret Mono', monospace; letter-spacing: -2px; color: var(--text-main);">Web Tools.</h1>
                <p class="text-muted mt-3 fs-6" style="letter-spacing: 1px;">
                    일상의 불편함을 해결하기 위해 직접 기획하고 개발한 웹 프로그램들입니다.
                </p>
            </div>
        </div>

<!--         <div class="row justify-content-center">
            <div class="col-lg-9">
                
                <div class="card tool-showcase-card mb-4 p-4 p-md-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                        <h3 class="fw-bold m-0" style="color: var(--text-main);">도구 이름 (Project Title)</h3>
                        <span class="version-tag mt-2 mt-md-0">v1.0.0</span>
                    </div>
                    <p class="text-muted mb-4" style="line-height: 1.7; font-size: 1.05rem;">
                        여기에 개발하신 첫 번째 웹 프로그램에 대한 설명을 적어주세요. 어떤 문제를 해결하기 위해 만들었는지, 핵심 기능은 무엇인지 간략하게 소개합니다.
                    </p>
                    <div class="mb-5">
                        <span class="badge rounded-pill tech-badge me-2 mb-2">PHP</span>
                        <span class="badge rounded-pill tech-badge me-2 mb-2">MySQL</span>
                        <span class="badge rounded-pill tech-badge me-2 mb-2">Bootstrap 5</span>
                    </div>
                    <div class="text-end">
                        <a href="여기에_연결할_도구_링크_입력" target="_blank" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm" style="font-weight: 600; letter-spacing: 0.5px;">
                            Launch App 🚀
                        </a>
                    </div>
                </div>

                <div class="card tool-showcase-card mb-4 p-4 p-md-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                        <h3 class="fw-bold m-0" style="color: var(--text-main);">이미지 최적화 컨버터</h3>
                        <span class="version-tag mt-2 mt-md-0">v0.9.5 Beta</span>
                    </div>
                    <p class="text-muted mb-4" style="line-height: 1.7; font-size: 1.05rem;">
                        두 번째 툴에 대한 설명 예시입니다. 대용량 이미지를 업로드하면 자동으로 WebP 형식으로 변환하고 EXIF 데이터를 추출해주는 사내용 미니 툴입니다.
                    </p>
                    <div class="mb-5">
                        <span class="badge rounded-pill tech-badge me-2 mb-2">Python</span>
                        <span class="badge rounded-pill tech-badge me-2 mb-2">AWS S3</span>
                        <span class="badge rounded-pill tech-badge me-2 mb-2">Vanilla JS</span>
                    </div>
                    <div class="text-end">
                        <a href="#" class="btn btn-outline-dark rounded-pill px-4 py-2" style="font-weight: 600;">
                            준비 중 (Coming Soon)
                        </a>
                    </div>
                </div>

            </div>
        </div> -->
    </main>

    <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>