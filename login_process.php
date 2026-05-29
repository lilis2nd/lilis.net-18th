<?php
session_start();
require_once 'db_connect.php'; // 기존에 만들어둔 DB 연결 파일명이 맞는지 확인해 주세요!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header('Location: login?error=invalid');
        exit;
    }

    try {
        // 1. DB에서 해당 username 조회
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // 2. 유저가 존재하고, 해시화된 비밀번호가 일치하는지 검증
        if ($user && password_verify($password, $user['password_hash'])) {
            // 로그인 성공! 세션에 관리자 정보 저장
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // 세션 탈취 방지를 위해 세션 ID 재발급
            session_regenerate_id(true);

            // 메인 화면으로 이동
            header('Location: index');
            exit;
        } else {
            // 로그인 실패
            header('Location: login?error=invalid');
            exit;
        }
    } catch (PDOException $e) {
        // 에러 발생 시 로그를 남기거나 예외 처리
        error_log("Login Error: " . $e->getMessage());
        header('Location: login?error=invalid');
        exit;
    }
} else {
    // POST 접근이 아닐 경우 튕겨냄
    header('Location: login');
    exit;
}