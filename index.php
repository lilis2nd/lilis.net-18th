<?php
session_start();
require_once 'db_connect.php';
require_once 'config.php';

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
    <meta property="og:type" content="website">
    <meta property="og:title" content="Skyremix Studio">
    <meta property="og:description" content="두 개의 시선, 하나의 기록. Lilis의 사진 갤러리 및 웹 포트폴리오입니다.">
    <meta property="og:image" content="https://lilis.net/og-image.jpg"> <meta property="og:url" content="https://lilis.net">
    <?php include 'common_head.php'; ?>
    <style>
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
        
        /* 갤러리 액자 같은 메인 이미지 */
        .featured-img-container {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            background-color: #000;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .featured-img-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }
        .featured-img {
            width: 100%;
            max-height: 75vh; /* 사진이 너무 커서 잘리지 않게 최대 높이 조정 */
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* 💡 모던한 타이틀 스타일 영역 */
        .modern-title-box {
            margin-top: 3rem;
            text-align: center;
        }
        .modern-title {
            /* 💡 수정됨: Azeret Mono 뒤에 Pretendard를 예비(백업) 폰트로 추가 */
            font-family: 'Azeret Mono', 'Pretendard', sans-serif; 
            font-weight: 800;
            font-size: 2.5rem;
            letter-spacing: -2px;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            word-break: keep-all; /* 한글 텍스트가 단어 중간에 끊기지 않도록 추가 */
        }
        
        .title-divider {
            width: 40px;
            height: 3px;
            background-color: var(--accent);
            margin: 1.5rem auto;
            border-radius: 2px;
        }
        .title-meta {
            color: var(--text-muted);
            font-size: 0.85rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
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

                    <div class="featured-img-container">
                        <a href="photo_detail?id=<?= $photo['id'] ?>" style="display: block; text-decoration: none;">
                            <img src="<?= htmlspecialchars($photo['s3_url']) ?>" class="featured-img" alt="<?= htmlspecialchars($photo['title']) ?>">
                        </a>
                    </div>

                    <div class="modern-title-box">
                        <h1 class="modern-title"><?= htmlspecialchars($photo['title']) ?></h1>
                        <div class="title-divider"></div>
                        <div class="title-meta">
                            LATEST WORK &nbsp;&middot;&nbsp; 
                            <?= !empty($photo['taken_at']) ? date('Y', strtotime($photo['taken_at'])) : date('Y', strtotime($photo['uploaded_at'])) ?>
                        </div>
                    </div>

                    <div class="text-center mt-5 pt-3">
                        <a href="photos" class="btn btn-outline-dark fw-bold px-5 py-2 shadow-sm" style="border-radius: 30px; letter-spacing: 1px;">
                            Enter Gallery &rarr;
                        </a>
                    </div>

                </div>
            </div>
        <?php endif; ?>
    </div>

</div> 

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>