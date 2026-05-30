<?php
// db_connect.php
date_default_timezone_set('Asia/Seoul');

// 1. Composer 오토로더 및 phpdotenv 로드
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// 2. .env 파일이 있는 디렉토리 지정 (현재 폴더)
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // .env 파일이 없어도 치명적 에러를 내지 않고 안전하게 로드

// 3. 환경 변수에서 DB 정보 가져오기 (만약 .env 내 키 이름이 다르다면 수정해 주세요!)
$host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
$db      = $_ENV['DB_NAME'] ?? 'lilis_db';
$user    = $_ENV['DB_USER'] ?? 'root';
$pass    = $_ENV['DB_PASS'] ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // 실서비스 환경 보안을 위해 에러 메시지 상세 노출 제한
     throw new \PDOException("Database Connection Failed.", (int)$e->getCode());
}
