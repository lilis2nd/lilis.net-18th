<?php
// 현재 접속 중인 파일명 가져오기 ('.php' 확장자 제외하고 이름만 추출)
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm" style="background-color: var(--primary-dark, #0d2b5b); padding: 1rem 0;">
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
            </ul>
        </div>
    </div>
</nav>