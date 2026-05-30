<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $photo_id = isset($data['photo_id']) ? (int)$data['photo_id'] : 0;

    if ($photo_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE photos SET views = COALESCE(views, 0) + 1 WHERE id = :id");
            $stmt->execute([':id' => $photo_id]);
            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false]);
            exit;
        }
    }
}
echo json_encode(['success' => false]);
?>