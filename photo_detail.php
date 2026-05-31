<?php
session_start();
// photo_detail.php
require 'vendor/autoload.php';
require_once 'db_connect.php';

$photo_id = $_GET['id'] ?? null;
$photo = null;

if ($photo_id) {
    try {
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

// 💡 [추가] 이전/다음 사진 ID 가져오기
$stmt_prev = $pdo->prepare("SELECT id FROM photos WHERE id > ? ORDER BY id ASC LIMIT 1");
$stmt_prev->execute([$photo_id]);
$prev_photo = $stmt_prev->fetch(PDO::FETCH_ASSOC);

$stmt_next = $pdo->prepare("SELECT id FROM photos WHERE id < ? ORDER BY id DESC LIMIT 1");
$stmt_next->execute([$photo_id]);
$next_photo = $stmt_next->fetch(PDO::FETCH_ASSOC);

// DB에서 좋아요 값이 NULL이면 0으로 처리
$like_count = $photo['likes'] ? (int)$photo['likes'] : 0;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?> - Skyremix Studio</title>
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?> - Skyremix Studio">
    <meta property="og:description" content="Skyremix Studio에 업로드된 작품입니다. 클릭해서 감상해 보세요.">
    <meta property="og:image" content="<?= htmlspecialchars($photo['s3_url'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="https://lilis.net/photo_detail?id=<?= $photo['id'] ?>">
    <?php include 'common_head.php'; ?>
    
    <style>
        /* 하트 버튼 애니메이션 */
        .btn-like {
            border: 2px solid #ff4757;
            color: #ff4757;
            background-color: transparent;
            font-weight: 700;
            transition: all 0.2s ease-in-out;
            font-family: 'Azeret Mono', monospace;
        }
        .btn-like:hover {
            background-color: #ffeff0;
            color: #ff4757;
        }
        .btn-like.liked {
            background-color: #ff4757;
            color: #fff;
            border-color: #ff4757;
        }
        /* 클릭할 때 뿅! 하는 효과 */
        .btn-like:active {
            transform: scale(0.9);
        }
        .heart-icon {
            display: inline-block;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-like.liked .heart-icon {
            transform: scale(1.2);
        }
    </style>
</head>
<body>

<div class="content-wrapper" style="min-height: 100vh; display: flex; flex-direction: column;">
    <?php include 'navbar.php'; ?>

    <main class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-4 gap-3">
                    <a href="photos" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm" style="font-weight: 600;">&larr; Gallery</a>
                    
                    <div class="btn-group shadow-sm" style="border-radius: 50px;">
                        <a href="<?= $prev_photo ? 'photo_detail?id='.$prev_photo['id'] : '#' ?>" class="btn btn-outline-dark px-4 <?= $prev_photo ? '' : 'disabled' ?>" style="border-top-left-radius: 50px; border-bottom-left-radius: 50px; font-weight: 600;">&larr; Prev</a>
                        <a href="<?= $next_photo ? 'photo_detail?id='.$next_photo['id'] : '#' ?>" class="btn btn-outline-dark px-4 <?= $next_photo ? '' : 'disabled' ?>" style="border-top-right-radius: 50px; border-bottom-right-radius: 50px; font-weight: 600;">Next &rarr;</a>
                    </div>
                </div>
                
                <div class="text-center mb-4">
                    <img src="<?= htmlspecialchars($photo['s3_url'], ENT_QUOTES, 'UTF-8') ?>" 
                         class="img-fluid rounded shadow" 
                         alt="<?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?>"
                         style="max-height: 75vh; object-fit: contain; width: auto;">
                </div>
                
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 mt-4 gap-3">
                    <h2 class="m-0" style="color: var(--text-main); font-weight: 700;"><?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <div class="photo-actions">
                        <button id="shareButton" class="share-btn" title="사진 공유하기">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                        </button>
                        
                        <button id="likeBtn" class="btn btn-like rounded-pill px-4 shadow-sm" data-id="<?= $photo['id'] ?>">
                            <span class="heart-icon me-1">♡</span> 
                            <span id="likeCount"><?= $like_count ?></span>
                        </button>
                    </div>
                </div>

                <div class="metadata-box mb-5 mt-4">
                    <div class="row">
                        <div class="col-md-4 metadata-item">
                            <?php if (!empty($photo['taken_at'])): ?>
                                <strong>촬영일:</strong> <?= date('Y-m-d H:i', strtotime($photo['taken_at'])) ?>
                            <?php else: ?>
                                <strong>업로드:</strong> <?= date('Y-m-d H:i', strtotime($photo['uploaded_at'])) ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 metadata-item">
                            <strong>카메라:</strong> <?= htmlspecialchars($photo['camera_model'], ENT_QUOTES, 'UTF-8') ?: '-' ?>
                        </div>
                        <div class="col-md-4 metadata-item">
                            <strong>조리개:</strong> <?= $photo['aperture'] ? htmlspecialchars($photo['aperture'], ENT_QUOTES, 'UTF-8') : '-' ?>
                        </div>
                        <div class="col-md-4 metadata-item">
                            <strong>셔터스피드:</strong> <?= $photo['shutter_speed'] ? htmlspecialchars($photo['shutter_speed'], ENT_QUOTES, 'UTF-8') . 's' : '-' ?>
                        </div>
                        <div class="col-md-4 metadata-item">
                            <strong>ISO:</strong> <?= htmlspecialchars($photo['iso'], ENT_QUOTES, 'UTF-8') ?: '-' ?>
                        </div>
                        <div class="col-md-4 metadata-item">
                            <strong>초점거리:</strong> <?= $photo['focal_length'] ? round(floatval($photo['focal_length']), 1) . 'mm' : '-' ?>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mt-5">
                    <h4 style="color: var(--text-main); margin-bottom: 1.5rem; font-weight: 700;">Comments</h4>
                    
                    <?php
                    $stmt_comments = $pdo->prepare("SELECT * FROM comments WHERE photo_id = ? ORDER BY created_at ASC");
                    $stmt_comments->execute([$photo_id]);
                    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if (empty($comments)): ?>
                        <p class="text-muted mb-4 px-2" style="font-size: 0.95rem;">아직 작성된 댓글이 없습니다. 첫 번째 감상평을 남겨주세요!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="card mb-3 border-0" style="background-color: #fff; box-shadow: var(--shadow-subtle); border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold m-0" style="color: var(--text-main);">
                                            <?= htmlspecialchars($comment['author'], ENT_QUOTES, 'UTF-8') ?>
                                            <span class="text-muted small fw-normal ms-2"><?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?></span>
                                        </h6>
                                        
                                        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                                            <form action="comment_delete.php" method="POST" onsubmit="return confirm('이 댓글을 정말 삭제하시겠습니까?');" style="margin: 0;">
                                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.75rem; border-radius: 20px;">삭제</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <p class="card-text mb-0 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                                        <?= nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="card border-0 mt-4" style="background-color: transparent;">
                        <div class="card-body p-0">
                            <form action="comment_process.php" method="POST">
                                <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                                    <label for="url_website_check">Website</label>
                                    <input type="text" id="url_website_check" name="url_website_check" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="author" placeholder="이름 (닉네임)" required style="max-width: 250px;">
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" name="content" rows="3" placeholder="사진에 대한 감상을 남겨주세요." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-dark rounded-pill px-4" style="font-weight: 600;">댓글 등록</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeBtn = document.getElementById('likeBtn');
    const likeCountSpan = document.getElementById('likeCount');
    const heartIcon = document.querySelector('.heart-icon');
    const photoId = likeBtn.getAttribute('data-id');
    const storageKey = 'liked_photo_' + photoId;

    // 페이지 로드 시 이미 누른 적이 있는지 확인 (브라우저 LocalStorage 확인)
    if (localStorage.getItem(storageKey) === 'true') {
        likeBtn.classList.add('liked');
        heartIcon.textContent = '♥';
    }

    likeBtn.addEventListener('click', function() {
        // 이미 누른 경우 클릭 무시 (어뷰징 방지)
        if (localStorage.getItem(storageKey) === 'true') {
            alert('이미 좋아요를 누르신 작품입니다. 감사합니다! 😊');
            return;
        }

        // 백엔드(like_process.php)로 비동기 POST 전송
        fetch('like_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ photo_id: photoId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 숫자 업데이트
                likeCountSpan.textContent = data.likes;
                // 버튼 스타일 변경 (빨간색으로 채우기)
                likeBtn.classList.add('liked');
                heartIcon.textContent = '♥';
                // 브라우저에 "나 이거 좋아요 눌렀음" 기록 남기기
                localStorage.setItem(storageKey, 'true');
            } else {
                alert('오류가 발생했습니다: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('네트워크 오류가 발생했습니다.');
        });
    });
});

</script>
<script>
document.getElementById('shareButton').addEventListener('click', async () => {
    // 공유할 데이터 설정
    const shareData = {
        title: '<?= addslashes($photo['title'] ?? '무제') ?> - Skyremix Studio',
        text: 'Skyremix Studio에서 이 사진을 감상해보세요.',
        url: window.location.href // 현재 페이지 주소
    };

    // 1. 모바일 환경 (Web Share API 지원 시)
    if (navigator.share) {
        try {
            await navigator.share(shareData);
        } catch (err) {
            console.log('공유가 취소되었거나 에러가 발생했습니다.', err);
        }
    } 
    // 2. PC 환경 (클립보드 복사 폴백)
    else {
        try {
            await navigator.clipboard.writeText(shareData.url);
            alert('🔗 사진 링크가 클립보드에 복사되었습니다!');
        } catch (err) {
            alert('주소 복사에 실패했습니다. 브라우저 주소창의 링크를 복사해주세요.');
        }
    }
});
</script>
</body>
</html>