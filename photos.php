<?php
session_start();
require_once 'db_connect.php';
require_once 'config.php';

// 1. 선택된 카테고리 확인 (기본값: All)
$currentCategory = isset($_GET['cat']) ? $_GET['cat'] : 'All';

// 2. 페이지네이션 설정
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // 3. 카테고리에 따른 동적 쿼리 생성
    $whereClause = ($currentCategory !== 'All') ? "WHERE category = :category" : "";
    
    // 4. 전체 사진 수 계산
    $countSql = "SELECT COUNT(*) FROM photos $whereClause";
    $totalStmt = $pdo->prepare($countSql);
    if ($currentCategory !== 'All') {
        $totalStmt->bindValue(':category', $currentCategory);
    }
    $totalStmt->execute();
    $totalPhotos = $totalStmt->fetchColumn();
    $totalPages = ceil($totalPhotos / $limit);

    // 5. 사진 목록 + 각 사진의 '댓글 수(comment_count)' 가져오기
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
    $photos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("데이터베이스 오류: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> | Gallery</title>
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="Skyremix Studio">
    <meta property="og:description" content="두 개의 시선, 하나의 기록. Lilis의 사진 갤러리 및 웹 포트폴리오입니다.">
    <meta property="og:image" content="https://lilis.net/og-image.jpg">
    <meta property="og:url" content="https://lilis.net/photos">
    <?php include 'common_head.php'; ?>
    <style>
        html, body { height: 100%; margin: 0; }
        body { display: flex; flex-direction: column; }
        .content-wrapper { flex: 1 0 auto; }
        .gallery-grid .col { padding: 8px; }
        
        .photo-container {
            position: relative; border-radius: 10px; overflow: hidden;
            box-shadow: 0 4px 10px rgba(13, 43, 91, 0.08); transition: all 0.3s ease-in-out;
            cursor: zoom-in; background-color: #000;
        }

        .photo-container.skeleton {
            background: linear-gradient(90deg, var(--bg-secondary) 25%, var(--accent) 50%, var(--bg-secondary) 75%);
            background-size: 200% 100%;
            animation: skeletonPulse 1.5s ease-in-out infinite;
        }
        @keyframes skeletonPulse {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .photo-img { 
            width: 100%; height: auto; aspect-ratio: 1 / 1; object-fit: cover; 
            opacity: 0; 
            transition: opacity 0.8s ease-out, transform 0.5s ease; 
        }
        .photo-img.loaded { opacity: 1; }
        
        .photo-container:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 15px 30px rgba(13, 43, 91, 0.15); }
        .photo-container:hover .photo-img { transform: scale(1.1); }
        .photo-overlay {
            position: absolute; bottom: 0; left: 0; width: 100%; padding: 15px;
            background: linear-gradient(0deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.6) 50%, rgba(0,0,0,0) 100%);
            color: #fff; opacity: 0; transition: opacity 0.3s ease-in-out; z-index: 2;
        }
        .photo-container:hover .photo-overlay { opacity: 1; }
        .overlay-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .overlay-camera { font-size: 0.8rem; color: rgba(255,255,255,0.8); margin-bottom: 5px; }
        .exif-badge-group { display: flex; flex-wrap: wrap; gap: 4px; }
        .exif-badge { font-size: 0.7rem; color: #fff; background-color: rgba(255,255,255,0.15); padding: 3px 6px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); }
        
        .subtle-delete-btn { position: absolute; top: 10px; right: 10px; padding: 2px 7px; font-size: 0.7rem; opacity: 0; z-index: 3; transition: opacity 0.3s ease; }
        .subtle-edit-btn { position: absolute; top: 10px; left: 10px; padding: 4px 8px; font-size: 0.7rem; opacity: 0; z-index: 3; transition: opacity 0.3s ease; }
        .photo-container:hover .subtle-delete-btn, .photo-container:hover .subtle-edit-btn { opacity: 1; }
        
        .modal-backdrop.show { opacity: 0.95; }
        .lightbox-exif { font-size: 0.85rem; color: #ccc; letter-spacing: 0.5px; }
        
        .lightbox-stats { font-family: 'Azeret Mono', monospace; font-size: 0.95rem; color: #ffeb3b; }
        .lightbox-stats .stat-item { display: inline-flex; align-items: center; gap: 6px; }
        .lightbox-stats .stat-icon { opacity: 0.8; font-size: 1.05rem; }
        
        .lightbox-nav-btn {
            position: absolute; top: 50%; transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.3); border: none; color: white;
            font-size: 2.5rem; padding: 10px 20px; border-radius: 5px;
            cursor: pointer; opacity: 0.6; transition: all 0.3s ease; z-index: 1055;
        }
        .lightbox-nav-btn:hover { opacity: 1; background: rgba(0, 0, 0, 0.7); }
        .lightbox-prev { left: 10px; }
        .lightbox-next { right: 10px; }
        
        @media (max-width: 768px) { .lightbox-nav-btn { display: none; } }
    </style>
</head>
<body>

<div class="content-wrapper">
    <?php include 'navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold fs-1" style="color: var(--text-main); letter-spacing: -1px;">Gallery</h2>
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <div>
                    <a href="upload" class="btn btn-dark btn-sm me-2 shadow-sm" style="font-weight: bold; border-radius: 30px; padding: 6px 15px;">사진 추가+</a>
                    <a href="logout" class="btn btn-sm btn-outline-secondary shadow-sm" style="border-radius: 30px; padding: 6px 15px;">관리자 로그아웃</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="photos" class="btn btn-sm <?= ($currentCategory == 'All') ? 'btn-dark' : 'btn-outline-dark' ?> rounded-pill px-3">All</a>
            <a href="photos?cat=General" class="btn btn-sm <?= ($currentCategory == 'General') ? 'btn-dark' : 'btn-outline-dark' ?> rounded-pill px-3">General</a>
            <a href="photos?cat=Landscape" class="btn btn-sm <?= ($currentCategory == 'Landscape') ? 'btn-dark' : 'btn-outline-dark' ?> rounded-pill px-3">Landscape</a>
            <a href="photos?cat=Portrait" class="btn btn-sm <?= ($currentCategory == 'Portrait') ? 'btn-dark' : 'btn-outline-dark' ?> rounded-pill px-3">Portrait</a>
            <a href="photos?cat=Street" class="btn btn-sm <?= ($currentCategory == 'Street') ? 'btn-dark' : 'btn-outline-dark' ?> rounded-pill px-3">Street</a>
            <a href="photos?cat=B%26W" class="btn btn-sm <?= ($currentCategory == 'B&W') ? 'btn-dark' : 'btn-outline-dark' ?> rounded-pill px-3">B&W</a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 gallery-grid g-0">
            <?php if (empty($photos)): ?>
                <div class="col-12 text-center py-5"><p class="text-muted fs-5">해당 카테고리에 업로드된 사진이 없습니다.</p></div>
            <?php else: ?>
                <?php foreach ($photos as $photo): ?>
                    <div class="col">
                        <?php
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
                        ?>
                        <div class="photo-container skeleton" 
                             data-id="<?= $photo['id'] ?>"
                             data-img="<?= htmlspecialchars($photo['s3_url']) ?>" 
                             data-title="<?= htmlspecialchars($photo['title']) ?>"
                             data-camera="<?= htmlspecialchars($photo['camera_model']) ?>"
                             data-aperture="<?= htmlspecialchars($photo['aperture']) ?>"
                             data-shutter="<?= htmlspecialchars($photo['shutter_speed']) ?>"
                             data-iso="<?= htmlspecialchars($photo['iso']) ?>"
                             data-focal="<?= htmlspecialchars($photo['focal_length']) ?>"
                             data-exif="<?= htmlspecialchars($exifString) ?>"
                             data-category="<?= htmlspecialchars($photo['category'] ?? 'General') ?>"
                             data-likes="<?= $photo['likes'] ? (int)$photo['likes'] : 0 ?>"
                             data-views="<?= $photo['views'] ? (int)$photo['views'] : 0 ?>"
                             data-comments="<?= (int)$photo['comment_count'] ?>">
                            
                            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                                <button type="button" class="btn btn-dark btn-sm rounded-pill subtle-edit-btn edit-action-trigger">⚙️ 수정</button>

                                <form action="delete_process.php" method="POST" onsubmit="return confirm('정말 이 사진을 삭제하시겠습니까?');" class="subtle-delete-btn delete-form">
                                    <input type="hidden" name="id" value="<?= $photo['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm rounded-circle p-0" style="width: 24px; height: 24px; line-height: 1;"><span style="font-size: 1.1rem; vertical-align: top;">&times;</span></button>
                                </form>
                            <?php endif; ?>

                            <img src="<?= htmlspecialchars($photo['s3_url']) ?>" 
                                 class="photo-img" 
                                 alt="<?= htmlspecialchars($photo['title']) ?>" 
                                 loading="lazy"
                                 onload="this.classList.add('loaded'); this.closest('.photo-container').classList.remove('skeleton');">
                            
                            <div class="photo-overlay">
                                <h5 class="overlay-title"><?= htmlspecialchars($photo['title']) ?></h5>
                                <?php if (!empty($photo['camera_model'])): ?><div class="overlay-camera">📷 <?= htmlspecialchars($photo['camera_model']) ?></div><?php endif; ?>
                                <div class="exif-badge-group">
                                    <?php if (!empty($focalFormatted)): ?><span class="exif-badge"><?= $focalFormatted ?></span><?php endif; ?>
                                    <?php if (!empty($photo['aperture'])): ?><span class="exif-badge"><?= htmlspecialchars($photo['aperture']) ?></span><?php endif; ?>
                                    <?php if (!empty($photo['shutter_speed'])): ?><span class="exif-badge"><?= htmlspecialchars($photo['shutter_speed']) ?>s</span><?php endif; ?>
                                    <?php if (!empty($photo['iso'])): ?><span class="exif-badge">ISO <?= htmlspecialchars($photo['iso']) ?></span><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div id="scrollTrigger" style="height: 20px; width: 100%; margin-top: 20px;"></div>
            <div id="loadingSpinner" class="text-center py-4 d-none">
                <div class="spinner-border text-secondary" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 0.25rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center position-relative p-0">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1060;"></button>
        <button id="lightboxPrevBtn" class="lightbox-nav-btn lightbox-prev">&#10094;</button>
        <button id="lightboxNextBtn" class="lightbox-nav-btn lightbox-next">&#10095;</button>
        <img src="" id="lightboxImage" class="img-fluid rounded shadow" alt="Enlarged Photo" style="max-height: 80vh; object-fit: contain;">
        
        <div class="mt-3">
            <div id="lightboxCaption" class="text-white fw-bold fs-5 mb-1"></div>
            
            <div class="d-flex justify-content-center gap-4 mb-2 lightbox-stats">
                <span class="stat-item" title="Views"><span class="stat-icon">👁️</span> <span id="lbViews">0</span></span>
                <span class="stat-item" title="Likes"><span class="stat-icon">❤️</span> <span id="lbLikes">0</span></span>
                <span class="stat-item" title="Comments"><span class="stat-icon">💬</span> <span id="lbComments">0</span></span>
            </div>
            
            <div id="lightboxExif" class="lightbox-exif mb-2"></div>
            <a href="#" id="lightboxDetailLink" class="btn btn-sm btn-outline-light rounded-pill px-3 mt-2" style="font-family: 'Azeret Mono', monospace; font-size: 0.85rem;">
                View Details & Comments &rarr;
            </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="editModalLabel">사진 정보 수정</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm">
        <input type="hidden" name="id" id="editPhotoId">
        <div class="modal-body">
            <div class="mb-3">
                <label for="editTitle" class="form-label fw-bold">사진 제목</label>
                <input type="text" class="form-control" id="editTitle" name="title" required>
            </div>
            <div class="mb-3">
                <label for="editCategory" class="form-label fw-bold text-primary">카테고리</label>
                <select class="form-select" id="editCategory" name="category">
                    <option value="General">General (기본)</option>
                    <option value="Landscape">Landscape (풍경)</option>
                    <option value="Portrait">Portrait (인물)</option>
                    <option value="Street">Street (스트릿)</option>
                    <option value="B&W">B&W (흑백)</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="editCamera" class="form-label text-muted">카메라 모델</label>
                <input type="text" class="form-control" id="editCamera" name="camera_model">
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label for="editAperture" class="form-label text-muted">조리개</label>
                    <input type="text" class="form-control" id="editAperture" name="aperture">
                </div>
                <div class="col-6 mb-3">
                    <label for="editShutter" class="form-label text-muted">셔터 스피드</label>
                    <input type="text" class="form-control" id="editShutter" name="shutter_speed">
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label for="editIso" class="form-label text-muted">ISO</label>
                    <input type="text" class="form-control" id="editIso" name="iso">
                </div>
                <div class="col-6 mb-3">
                    <label for="editFocal" class="form-label text-muted">초점거리</label>
                    <input type="text" class="form-control" id="editFocal" name="focal_length">
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">취소</button>
          <button type="submit" class="btn btn-primary btn-sm">수정 완료</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lightboxModalEl = document.getElementById('lightboxModal');
        const lightboxModal = new bootstrap.Modal(lightboxModalEl);
        
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCaption = document.getElementById('lightboxCaption');
        const lightboxExif = document.getElementById('lightboxExif');
        const lightboxDetailLink = document.getElementById('lightboxDetailLink');
        const lbViews = document.getElementById('lbViews');
        const lbLikes = document.getElementById('lbLikes');
        const lbComments = document.getElementById('lbComments');
        
        let photoContainersArray = [];
        let currentIndex = 0;
        let isModalOpen = false;

        lightboxModalEl.addEventListener('shown.bs.modal', () => isModalOpen = true);
        lightboxModalEl.addEventListener('hidden.bs.modal', () => isModalOpen = false);

        function updateLightbox(index) {
            if (index < 0) index = photoContainersArray.length - 1;
            if (index >= photoContainersArray.length) index = 0;
            currentIndex = index;

            const container = photoContainersArray[currentIndex];
            const photoId = container.getAttribute('data-id');
            
            lightboxImage.src = container.getAttribute('data-img');
            lightboxCaption.textContent = container.getAttribute('data-title');
            lightboxExif.textContent = container.getAttribute('data-exif');
            lightboxDetailLink.href = 'photo_detail?id=' + photoId;
            
            lbLikes.textContent = container.getAttribute('data-likes');
            lbComments.textContent = container.getAttribute('data-comments');
            
            const viewKey = 'viewed_photo_' + photoId;
            let currentViews = parseInt(container.getAttribute('data-views')) || 0;
            
            if (!sessionStorage.getItem(viewKey)) {
                fetch('view_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ photo_id: photoId })
                });
                sessionStorage.setItem(viewKey, 'true');
                currentViews++;
                container.setAttribute('data-views', currentViews);
            }
            lbViews.textContent = currentViews;
        }

        const editModalEl = document.getElementById('editModal');
        let editModal = null;
        if (editModalEl) {
            editModal = new bootstrap.Modal(editModalEl);
            const editForm = document.getElementById('editForm');
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(editForm);
                fetch('edit_process.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) { alert('성공적으로 수정되었습니다.'); location.reload(); }
                    else { alert('수정 실패: ' + data.message); }
                });
            });
        }

        // 💡 새 사진이 로드될 때마다 이벤트를 걸어주는 함수
        function bindEventsToContainers(containers) {
            containers.forEach(container => {
                const index = photoContainersArray.length;
                photoContainersArray.push(container);
                
                container.addEventListener('click', function(e) {
                    if(e.target.closest('.delete-form') || e.target.closest('.edit-action-trigger')) return;
                    updateLightbox(index);
                    lightboxModal.show();
                });

                if (editModalEl) {
                    const editBtn = container.querySelector('.edit-action-trigger');
                    if (editBtn) {
                        editBtn.addEventListener('click', function() {
                            document.getElementById('editPhotoId').value = container.getAttribute('data-id');
                            document.getElementById('editTitle').value = container.getAttribute('data-title');
                            document.getElementById('editCamera').value = container.getAttribute('data-camera');
                            document.getElementById('editAperture').value = container.getAttribute('data-aperture');
                            document.getElementById('editShutter').value = container.getAttribute('data-shutter');
                            document.getElementById('editIso').value = container.getAttribute('data-iso');
                            document.getElementById('editFocal').value = container.getAttribute('data-focal');
                            editModal.show();
                        });
                    }
                }
            });
        }

        // 1. 초기 렌더링된 사진들 바인딩
        bindEventsToContainers(Array.from(document.querySelectorAll('.photo-container')));

        // 2. 라이트박스 네비게이션 키/스와이프 동작
        document.getElementById('lightboxPrevBtn').addEventListener('click', () => updateLightbox(currentIndex - 1));
        document.getElementById('lightboxNextBtn').addEventListener('click', () => updateLightbox(currentIndex + 1));
        
        document.addEventListener('keydown', function(e) {
            if (!isModalOpen) return;
            if (e.key === 'ArrowLeft') updateLightbox(currentIndex - 1);
            if (e.key === 'ArrowRight') updateLightbox(currentIndex + 1);
        });
        
        let touchstartX = 0; let touchendX = 0;
        lightboxModalEl.addEventListener('touchstart', e => touchstartX = e.changedTouches[0].screenX);
        lightboxModalEl.addEventListener('touchend', e => { touchendX = e.changedTouches[0].screenX; handleSwipe(); });
        function handleSwipe() {
            const threshold = 50;
            if (touchstartX - touchendX > threshold) updateLightbox(currentIndex + 1);
            if (touchendX - touchstartX > threshold) updateLightbox(currentIndex - 1);
        }

        // 3. 💡 무한 스크롤(Infinite Scroll) 로직
        const galleryGrid = document.querySelector('.gallery-grid');
        const scrollTrigger = document.getElementById('scrollTrigger');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        let currentPage = 1;
        let currentCategory = new URLSearchParams(window.location.search).get('cat') || 'All';
        let isLoading = false;
        let hasMorePhotos = scrollTrigger ? true : false;

        if (scrollTrigger) {
            // 화면 바닥에 닿기 300px 전부터 미리 로드 시작
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !isLoading && hasMorePhotos) {
                    loadMorePhotos();
                }
            }, { rootMargin: "300px" }); 
            
            observer.observe(scrollTrigger);
        }

        function loadMorePhotos() {
            isLoading = true;
            loadingSpinner.classList.remove('d-none');
            currentPage++;

            fetch(`fetch_photos.php?cat=${encodeURIComponent(currentCategory)}&page=${currentPage}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.html.trim() !== '') {
                    // 서버에서 받은 HTML을 텍스트가 아닌 실제 요소(DOM)로 변환
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    const newCols = Array.from(tempDiv.children);
                    
                    const newContainers = [];
                    newCols.forEach(col => {
                        galleryGrid.appendChild(col);
                        newContainers.push(col.querySelector('.photo-container'));
                    });
                    
                    // 새로 붙은 사진들에 라이트박스 및 스크립트 연결
                    bindEventsToContainers(newContainers);
                    
                    hasMorePhotos = data.has_more;
                    
                    // 더 이상 불러올 사진이 없다면 스크롤 감지기 삭제
                    if (!hasMorePhotos && scrollTrigger) {
                        scrollTrigger.remove(); 
                    }
                } else {
                    hasMorePhotos = false;
                    if (scrollTrigger) scrollTrigger.remove();
                }
            })
            .catch(err => console.error('Fetch error:', err))
            .finally(() => {
                isLoading = false;
                loadingSpinner.classList.add('d-none');
            });
        }
    });
</script>
</body>
</html>