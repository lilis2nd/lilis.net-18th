<?php require_once 'config.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> | About</title>
    <meta property="og:type" content="website">
    <meta property="og:title" content="Skyremix Studio | About">
    <meta property="og:description" content="사진가이자 기획자, Lilis의 두 가지 인생 트랙과 이력을 소개합니다.">
    <meta property="og:image" content="https://lilis.net/og-image.jpg">
    <meta property="og:url" content="https://lilis.net/about">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* 두 갈래 타임라인 전용 정렬 스타일 */
        .timeline-section {
            margin-bottom: 2rem;
        }
        .timeline-row {
            display: flex;
            margin-bottom: 1.2rem;
            align-items: baseline;
            gap: 20px; /* 연도와 설명 사이의 띄어쓰기(간격) 강제 고정 */
        }
        .timeline-year {
            font-family: 'Azeret Mono', monospace;
            font-weight: 600;
            color: var(--text-main);
            flex: 0 0 135px; /* 글자 길이에 상관없이 무조건 135px로 칸 너비 고정 */
            font-size: 0.9rem;
        }
        .timeline-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            flex: 1; /* 나머지 빈 공간은 설명글이 가지도록 설정 */
        }
        .intro-box {
            background-color: var(--white);
            border: 1px solid var(--accent);
            border-radius: 16px;
            padding: 3rem;
            box-shadow: var(--shadow-subtle);
        }
        .timeline-title {
            font-family: 'Azeret Mono', monospace;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: block;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<div class="content-wrapper" style="min-height: 100vh; display: flex; flex-direction: column;">
    <?php include 'navbar.php'; ?>

    <main class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                
                <div class="text-center mb-5 mt-4">
                    <h1 class="display-3 fw-bold" style="letter-spacing: -2px; font-family: 'Azeret Mono', monospace;">LILIS</h1>
                    <p class="text-muted fs-6 mt-2" style="letter-spacing: 3px; text-transform: uppercase;">The Parallel Lines of My Life</p>
                </div>

                <div class="intro-box mb-5">
                    <h4 class="fw-bold mb-4" style="color: var(--text-main); letter-spacing: -0.5px;">두 개의 시선, 하나의 기록.</h4>
                    <p class="text-muted mb-0" style="line-height: 1.8; font-size: 1.05rem;">
                        서로 다른 두 개의 궤적을 그리며 살아갑니다.<br><br>
                        이성적이고 논리적인 숫자로 채워지는 '현실의 삶'과, 
                        말을 아낀 채 오직 빛과 프레임으로만 대화하는 '사진가로서의 삶'. 
                        이 어울리지 않는 두 개의 세계는 아이러니하게도 서로에게 완벽한 도피처이자 영감이 되어줍니다.<br><br>
                        이 공간은 그 평행한 두 인생이 만나 남긴 조용한 기록들이며, 
                        제 시선 끝에 머물렀던 찰나의 순간들이 누군가에게 작은 울림이 되기를 희망합니다.
                    </p>
                </div>

                <div class="row g-5 px-2">
                    
                    <div class="col-md-6 timeline-section">
                        <span class="timeline-title text-uppercase">01 / Photography</span>
                        
                        <div class="timeline-row">
                            <div class="timeline-year">2001 - 2026</div>
                            <div class="timeline-desc">Skyremix Studio 운영</div>
                        </div>
                        <div class="timeline-row">
                            <div class="timeline-year">2000</div>
                            <div class="timeline-desc">사진 입문</div>
                        </div>
                    </div>

                    <div class="col-md-6 timeline-section">
                        <span class="timeline-title text-uppercase">02 / Life & Career</span>
                         <div class="timeline-row">
                            <div class="timeline-year">2025.12.15.</div>
                            <div class="timeline-desc">두 아이의 아버지</div>
                        </div>                       
                        <div class="timeline-row">
                            <div class="timeline-year">2022 - Present</div>
                            <div class="timeline-desc">Product Planner</div>
                        </div>
                        <div class="timeline-row">
                            <div class="timeline-year">2018.04.07.</div>
                            <div class="timeline-desc">한 사람의 배우자이자 단짝</div>
                        </div>  
                        <div class="timeline-row">
                            <div class="timeline-year">2017 - 2022</div>
                            <div class="timeline-desc">IT Project Manager, Field Operator</div>
                        </div>
                        <div class="timeline-row">
                            <div class="timeline-year">2012 - 2017</div>
                            <div class="timeline-desc">Technical Writer,Project Manager</div>
                        </div>
                    </div>
                    
                </div>

                <div class="text-center mt-5 pt-5" style="border-top: 1px solid var(--bg-secondary);">
                    <h5 class="fw-bold mb-2" style="font-family: 'Azeret Mono', monospace;">Connect</h5>
                    <p class="text-muted mb-4" style="font-size: 0.9rem;">어느 세계의 이야기든 소통은 언제나 환영합니다.</p>
                    
                    <div>
                        <a href="mailto:lilis@skyremix.com" class="btn btn-outline-dark rounded-pill px-4 py-2 mx-1 shadow-sm">✉️ Email</a>
                        <a href="https://instagram.com/lilis" target="_blank" class="btn btn-dark rounded-pill px-4 py-2 mx-1 shadow-sm">📷 Instagram</a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>