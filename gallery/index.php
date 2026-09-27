<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Video Gallery - PC Poa';

$videos = $pdo->query("SELECT * FROM videos WHERE status='active' ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">Video Gallery</h1>
        <p class="mb-0">Watch PC Poa in action &mdash; repairs, tips, and behind the scenes.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (empty($videos)): ?>
            <p class="text-center text-muted">No videos have been uploaded yet. Please check back soon.</p>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($videos as $v): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card card-pcpoa video-card">
                    <video controls preload="metadata" poster="<?= e($v['thumbnail'] ? '../' . $v['thumbnail'] : '') ?>">
                        <source src="../<?= e($v['video_file']) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1"><?= e($v['title']) ?></h6>
                        <p class="small text-muted mb-0"><?= e($v['description']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
