<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Javascript Fetch API로 보낸 JSON 데이터 받기
    $data = json_decode(file_get_contents('php://input'), true);
    $photo_id = isset($data['photo_id']) ? (int)$data['photo_id'] : 0;

    if ($photo_id > 0) {
        try {
            // 1. 기존 좋아요 수에 +1 하기 (NULL일 경우 0으로 간주 후 +1)
            $updateStmt = $pdo->prepare("UPDATE photos SET likes = COALESCE(likes, 0) + 1 WHERE id = :id");
            $updateStmt->execute([':id' => $photo_id]);

            // 2. 업데이트된 최신 좋아요 수 다시 가져오기
            $selectStmt = $pdo->prepare("SELECT likes FROM photos WHERE id = :id");
            $selectStmt->execute([':id' => $photo_id]);
            $new_likes = $selectStmt->fetchColumn();

            echo json_encode(['success' => true, 'likes' => (int)$new_likes]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'DB 에러가 발생했습니다.']);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
?>