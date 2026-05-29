<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => '권한이 없습니다.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title']);
    // [추가됨] 카테고리 값 수신
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'General';
    $camera_model = trim($_POST['camera_model']);
    $aperture = trim($_POST['aperture']);
    $shutter_speed = trim($_POST['shutter_speed']);
    $iso = trim($_POST['iso']);
    $focal_length = trim($_POST['focal_length']);

    if ($id <= 0 || empty($title)) {
        echo json_encode(['success' => false, 'message' => '필수 값이 누락되었습니다.']);
        exit;
    }

    try {
        // [수정됨] 카테고리(category) 업데이트 쿼리 추가
        $sql = "UPDATE photos SET 
                    title = :title, 
                    category = :category,
                    camera_model = :camera_model, 
                    aperture = :aperture, 
                    shutter_speed = :shutter_speed, 
                    iso = :iso, 
                    focal_length = :focal_length 
                WHERE id = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':category' => $category,
            ':camera_model' => $camera_model,
            ':aperture' => $aperture,
            ':shutter_speed' => $shutter_speed,
            ':iso' => $iso,
            ':focal_length' => $focal_length,
            ':id' => $id
        ]);

        echo json_encode(['success' => true]);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB 오류: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => '잘못된 접근입니다.']);
    exit;
}