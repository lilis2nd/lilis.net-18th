<?php
require_once 'db_connect.php';

// XML 헤더 설정 (이 파일은 HTML이 아니라 XML임을 브라우저와 로봇에게 알림)
header("Content-Type: text/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://lilis.net/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://lilis.net/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>https://lilis.net/photos</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    
    <?php
    try {
        $stmt = $pdo->query("SELECT id, uploaded_at FROM photos ORDER BY uploaded_at DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // 날짜 포맷을 W3C Datetime 형식(ISO 8601)으로 변환
            $date = date('c', strtotime($row['uploaded_at']));
            echo "    <url>\n";
            echo "        <loc>https://lilis.net/photo_detail?id=" . $row['id'] . "</loc>\n";
            echo "        <lastmod>" . $date . "</lastmod>\n";
            echo "        <changefreq>weekly</changefreq>\n";
            echo "        <priority>0.7</priority>\n";
            echo "    </url>\n";
        }
    } catch (PDOException $e) {
        // 에러 발생 시 조용히 넘어감
    }
    ?>
</urlset>