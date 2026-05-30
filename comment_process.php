<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo_id = isset($_POST['photo_id']) ? (int)$_POST['photo_id'] : 0;
    $author = trim($_POST['author']);
    $content = trim($_POST['content']);

    if ($photo_id > 0 && !empty($author) && !empty($content)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (photo_id, author, content) VALUES (:photo_id, :author, :content)");
            $stmt->execute([
                ':photo_id' => $photo_id,
                ':author' => $author,
                ':content' => $content
            ]);
        } catch (PDOException $e) {
            die("댓글 등록 실패: " . $e->getMessage());
        }
    }
    // 등록 완료 후 다시 원래 사진의 상세 페이지로 이동
    header("Location: photo_detail?id=" . $photo_id);
    exit;
} else {
    header("Location: photos");
    exit;
}
?>