<?php
session_start();
require_once 'config.php';
require_once 'db_connect.php';

// 1. 관리자 권한 철저히 확인 (비로그인 접근 차단)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo "<script>alert('접근 권한이 없습니다.'); location.href='login';</script>";
    exit;
}

// 2. 갤러리 전체 통계 데이터 가져오기
try {
    $stats = [];
    $stats['photos'] = $pdo->query("SELECT COUNT(*) FROM photos")->fetchColumn();
    $stats['views'] = $pdo->query("SELECT SUM(views) FROM photos")->fetchColumn() ?: 0;
    $stats['likes'] = $pdo->query("SELECT SUM(likes) FROM photos")->fetchColumn() ?: 0;
    $stats['comments'] = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();

    // 3. 최근 달린 댓글 15개 가져오기 (어떤 사진에 달렸는지 제목도 조인해서 가져옴)
    $stmt = $pdo->query("
        SELECT c.*, p.title AS photo_title 
        FROM comments c 
        JOIN photos p ON c.photo_id = p.id 
        ORDER BY c.created_at DESC 
        LIMIT 15
    ");
    $recent_comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("데이터베이스 에러: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> | Admin Dashboard</title>
    <?php include 'common_head.php'; ?>
    <style>
        .stat-card {
            background-color: var(--white);
            border: 1px solid var(--accent);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            box-shadow: var(--shadow-subtle);
            transition: transform 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-title { font-size: 0.9rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: var(--text-main); font-family: 'Azeret Mono', monospace; }
        .stat-icon { font-size: 1.5rem; margin-bottom: 10px; opacity: 0.8; }
        
        .comment-list-card {
            background-color: var(--white);
            border: 1px solid var(--accent);
            border-radius: 16px;
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
        }
        .comment-item {
            border-bottom: 1px solid var(--bg-secondary);
            padding: 20px;
            transition: background-color 0.2s ease;
        }
        .comment-item:last-child { border-bottom: none; }
        .comment-item:hover { background-color: var(--bg-primary); }
    </style>
</head>
<body>

<div class="content-wrapper" style="min-height: 100vh; display: flex; flex-direction: column;">
    <?php include 'navbar.php'; ?>

    <main class="container my-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold fs-2 m-0" style="color: var(--text-main); letter-spacing: -1px;">Dashboard</h2>
                <p class="text-muted mt-1 mb-0">Skyremix Studio 전체 통계 및 관리</p>
            </div>
            <div>
                <a href="upload" class="btn btn-dark rounded-pill px-4 shadow-sm fw-bold">사진 업로드 🚀</a>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">📷</div>
                    <div class="stat-title">Total Photos</div>
                    <div class="stat-number"><?= number_format($stats['photos']) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">👁️</div>
                    <div class="stat-title">Total Views</div>
                    <div class="stat-number"><?= number_format($stats['views']) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">❤️</div>
                    <div class="stat-title">Total Likes</div>
                    <div class="stat-number" style="color: #ff4757;"><?= number_format($stats['likes']) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">💬</div>
                    <div class="stat-title">Comments</div>
                    <div class="stat-number"><?= number_format($stats['comments']) ?></div>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mb-3" style="color: var(--text-main);">최근 댓글 모아보기</h4>
        <div class="comment-list-card mb-5">
            <?php if (empty($recent_comments)): ?>
                <div class="text-center py-5 text-muted">아직 작성된 댓글이 없습니다.</div>
            <?php else: ?>
                <?php foreach ($recent_comments as $comment): ?>
                    <div class="comment-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1 gap-2">
                                <span class="badge bg-dark" style="font-family: 'Azeret Mono', monospace;"><?= htmlspecialchars($comment['photo_title']) ?></span>
                                <strong style="color: var(--text-main);"><?= htmlspecialchars($comment['author']) ?></strong>
                                <span class="text-muted small"><?= date('Y.m.d H:i', strtotime($comment['created_at'])) ?></span>
                            </div>
                            <p class="mb-0 text-secondary" style="font-size: 0.95rem;">
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </p>
                        </div>
                        <div class="d-flex gap-2 text-nowrap">
                            <a href="photo_detail?id=<?= $comment['photo_id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">작품 보기</a>
                            
                            <form action="comment_delete.php" method="POST" onsubmit="return confirm('이 댓글을 정말 삭제하시겠습니까?');" style="margin: 0;">
                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                <input type="hidden" name="photo_id" value="<?= $comment['photo_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">삭제</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>