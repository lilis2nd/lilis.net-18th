<?php
// 현재 접속 중인 파일명 가져오기
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// 세션이 아직 시작되지 않았다면 안전하게 시작 (네비게이션 바에서 관리자 여부를 체크하기 위함)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm" style="padding: 1rem 0;">
        <div class="container">    
        <span class="navbar-brand fw-bold fs-4 m-0 d-flex align-items-center">
            <a href="/" style="color: inherit; text-decoration: none;">Skyremix Studio</a>
            <a href="login" class="secret-login-link" title="Admin Login" style="display:inline-block; width:8px; height:20px; text-decoration:none; cursor:pointer; margin-left:4px;"></a>
        </span>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'index') ? 'active' : '' ?>" href="index">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'about') ? 'active' : '' ?>" href="about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'photos') ? 'active' : '' ?>" href="photos">Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'tools') ? 'active' : '' ?>" href="tools">Tools</a>
                </li>
                
                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <li class="nav-item ms-lg-4">
                    <a class="nav-link <?= ($currentPage == 'dashboard') ? 'active' : '' ?>" href="dashboard" style="color: #ff4757 !important; font-weight: 700;">Dashboard ⚙️</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>