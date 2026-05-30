<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo_id = isset($_POST['photo_id']) ? (int)$_POST['photo_id'] : 0;
    
    // 💡 [핵심] 허니팟 체크 로직
    // 정상적인 사람이라면 화면에 안 보이니 이 칸을 비워뒀을 것입니다.
    // 만약 여기에 무언가 적혀있다면 100% 자동화된 스팸 봇입니다.
    if (!empty($_POST['url_website_check'])) {
        // 봇이 눈치채지 못하도록 에러 메시지 없이 조용히 갤러리 밖으로 내쫓습니다.
        header("Location: photos");
        exit;
    }

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