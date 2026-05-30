<?php
session_start();
require_once 'db_connect.php';

$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentCategory = isset($_GET['cat']) ? $_GET['cat'] : 'All';
$offset = ($page - 1) * $limit;

$whereClause = ($currentCategory !== 'All') ? "WHERE category = :category" : "";

$selectSql = "
    SELECT p.*, 
           (SELECT COUNT(*) FROM comments c WHERE c.photo_id = p.id) AS comment_count 
    FROM photos p 
    $whereClause 
    ORDER BY p.uploaded_at DESC 
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($selectSql);
if ($currentCategory !== 'All') {
    $stmt->bindValue(':category', $currentCategory);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$html = '';

foreach ($photos as $photo) {
    $focalFormatted = !empty($photo['focal_length']) ? round(floatval($photo['focal_length']), 1) . 'mm' : '';

    $exifDetails = [];
    if (!empty($photo['camera_model'])) $exifDetails[] = "📷 " . $photo['camera_model'];
    if (!empty($focalFormatted)) $exifDetails[] = $focalFormatted;
    if (!empty($photo['aperture'])) $exifDetails[] = $photo['aperture'];
    if (!empty($photo['shutter_speed'])) $exifDetails[] = $photo['shutter_speed'] . "s";
    if (!empty($photo['iso'])) $exifDetails[] = "ISO " . $photo['iso'];
    
    if (!empty($photo['taken_at'])) {
        $exifDetails[] = "📅 " . date('Y.m.d', strtotime($photo['taken_at']));
    } else {
        $exifDetails[] = "📅 " . date('Y.m.d', strtotime($photo['uploaded_at']));
    }
    $exifString = implode('   |   ', $exifDetails);

    $likes = $photo['likes'] ? (int)$photo['likes'] : 0;
    $views = $photo['views'] ? (int)$photo['views'] : 0;
    $comments = (int)$photo['comment_count'];

    $adminHtml = '';
    if ($is_admin) {
        $adminHtml = '
            <button type="button" class="btn btn-dark btn-sm rounded-pill subtle-edit-btn edit-action-trigger">⚙️ 수정</button>
            <form action="delete_process.php" method="POST" onsubmit="return confirm(\'정말 이 사진을 삭제하시겠습니까?\');" class="subtle-delete-btn delete-form">
                <input type="hidden" name="id" value="' . $photo['id'] . '">
                <button type="submit" class="btn btn-danger btn-sm rounded-circle p-0" style="width: 24px; height: 24px; line-height: 1;"><span style="font-size: 1.1rem; vertical-align: top;">&times;</span></button>
            </form>
        ';
    }

    $cameraBadge = !empty($photo['camera_model']) ? '<div class="overlay-camera">📷 ' . htmlspecialchars($photo['camera_model']) . '</div>' : '';
    
    $exifBadges = '';
    if (!empty($focalFormatted)) $exifBadges .= '<span class="exif-badge">' . $focalFormatted . '</span>';
    if (!empty($photo['aperture'])) $exifBadges .= '<span class="exif-badge">' . htmlspecialchars($photo['aperture']) . '</span>';
    if (!empty($photo['shutter_speed'])) $exifBadges .= '<span class="exif-badge">' . htmlspecialchars($photo['shutter_speed']) . 's</span>';
    if (!empty($photo['iso'])) $exifBadges .= '<span class="exif-badge">ISO ' . htmlspecialchars($photo['iso']) . '</span>';

    $html .= '
    <div class="col">
        <div class="photo-container skeleton" 
             data-id="' . $photo['id'] . '"
             data-img="' . htmlspecialchars($photo['s3_url']) . '" 
             data-title="' . htmlspecialchars($photo['title']) . '"
             data-camera="' . htmlspecialchars($photo['camera_model']) . '"
             data-aperture="' . htmlspecialchars($photo['aperture']) . '"
             data-shutter="' . htmlspecialchars($photo['shutter_speed']) . '"
             data-iso="' . htmlspecialchars($photo['iso']) . '"
             data-focal="' . htmlspecialchars($photo['focal_length']) . '"
             data-exif="' . htmlspecialchars($exifString) . '"
             data-category="' . htmlspecialchars($photo['category'] ?? 'General') . '"
             data-likes="' . $likes . '"
             data-views="' . $views . '"
             data-comments="' . $comments . '">
            
            ' . $adminHtml . '

            <img src="' . htmlspecialchars($photo['s3_url']) . '" 
                 class="photo-img" 
                 alt="' . htmlspecialchars($photo['title']) . '" 
                 loading="lazy"
                 onload="this.classList.add(\'loaded\'); this.closest(\'.photo-container\').classList.remove(\'skeleton\');">
            
            <div class="photo-overlay">
                <h5 class="overlay-title">' . htmlspecialchars($photo['title']) . '</h5>
                ' . $cameraBadge . '
                <div class="exif-badge-group">
                    ' . $exifBadges . '
                </div>
            </div>
        </div>
    </div>';
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'html' => $html,
    'has_more' => count($photos) === $limit
]);
?>