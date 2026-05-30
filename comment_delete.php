<?php
session_start();
require_once 'db_connect.php';

// 관리자만 삭제할 수 있도록 권한 철저히 확인
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("권한이 없습니다.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    $photo_id = isset($_POST['photo_id']) ? (int)$_POST['photo_id'] : 0;

    if ($comment_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
            $stmt->execute([':id' => $comment_id]);
        } catch (PDOException $e) {
            die("댓글 삭제 실패: " . $e->getMessage());
        }
    }
    // 삭제 후 다시 상세 페이지로 복귀
    header("Location: photo_detail?id=" . $photo_id);
    exit;
}
?>