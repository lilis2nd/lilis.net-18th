<?php
session_start();
require_once 'config.php';

// 이미 로그인된 상태라면 갤러리로 리다이렉트 (Clean URL 적용)
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: photos');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> | Admin Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* 화면 전체를 다크 블루로 덮음 */
        body {
            background-color: #2C2A29;
            background-image: radial-gradient(circle at top right, #4a4745, #1c1b1a);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        
        /* 반투명 유리 질감 (Glassmorphism) 카드 */
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 50px 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
            color: #fff;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card h2 {
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 30px;
        }

        /* 입력 폼 스타일링 */
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 12px;
            padding: 15px;
            font-size: 1rem;
            text-align: center;
            letter-spacing: 1px;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.6);
            color: #fff;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 1px;
        }

        /* 로그인 버튼 */
        .btn-login {
            background-color: #fff;
            color: #0d2b5b;
            font-weight: 800;
            border-radius: 12px;
            padding: 14px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-login:hover {
            background-color: #e2e8f0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,255,255,0.2);
        }

        /* 돌아가기 링크 */
        .back-link {
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-block;
            margin-top: 25px;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #fff;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Skyremix Studio<br><span style="font-size: 1rem; font-weight: 400; opacity: 0.7; letter-spacing: 2px;">AUTHORIZATION</span></h2>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert py-2 mb-4" style="font-size: 0.85rem; border-radius: 10px; background: rgba(220, 53, 69, 0.2); color: #ffb3b3; border: 1px solid rgba(220, 53, 69, 0.3);">
            <?= htmlspecialchars($_GET['error'] == 'invalid' ? '아이디 또는 비밀번호가 올바르지 않습니다.' : '로그인이 필요합니다.') ?>
        </div>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
        <div class="mb-3">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autocomplete="username" autofocus>
        </div>
        <div class="mb-4">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required autocomplete="current-password">
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-login">Unlock</button>
        </div>
    </form>
    
    <a href="/" class="back-link">← Return to Public Site</a>
</div>

</body>
</html>