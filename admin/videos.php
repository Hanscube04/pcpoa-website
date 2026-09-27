<?php
$adminPageTitle = 'Video Gallery';
require_once __DIR__ . '/includes/admin-header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_video'])) {
    csrf_verify();
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || empty($_FILES['video_file']['name'])) {
        $error = 'Please provide a title and select a video file.';
    } else {
        $videoPath = handle_video_upload($_FILES['video_file'], __DIR__ . '/../uploads/videos');
        if (!$videoPath) {
            $error = 'Video upload failed. Allowed formats: mp4, webm, ogg, mov.';
        } else {
            $thumbPath = null;
            if (!empty($_FILES['thumbnail']['name'])) {
                $thumbPath = handle_image_upload($_FILES['thumbnail'], __DIR__ . '/../uploads/videos');
            }
            $stmt = $pdo->prepare("INSERT INTO videos (title, description, video_file, thumbnail, uploaded_by) VALUES (?,?,?,?,?)");
            $stmt->execute([$title, $description, $videoPath, $thumbPath, $_SESSION['user_id']]);
            redirect('admin/videos.php?saved=1');
        }
    }
}

// SECURITY: toggle/delete are state-changing actions, so both must be
// CSRF-protected POST requests rather than bare GET links.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_video'])) {
    csrf_verify();
    $id = (int)$_POST['toggle_video'];
    $stmt = $pdo->prepare("SELECT status FROM videos WHERE id = ?");
    $stmt->execute([$id]);
    $status = $stmt->fetchColumn();
    $newStatus = $status === 'active' ? 'hidden' : 'active';
    $pdo->prepare("UPDATE videos SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
    redirect('admin/videos.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_video'])) {
    csrf_verify();
    $id = (int)$_POST['delete_video'];
    $stmt = $pdo->prepare("SELECT video_file, thumbnail FROM videos WHERE id = ?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
    $pdo->prepare("DELETE FROM videos WHERE id = ?")->execute([$id]);
    if ($v) {
        foreach ([$v['video_file'], $v['thumbnail']] as $f) {
            if ($f && file_exists(__DIR__ . '/../' . $f)) @unlink(__DIR__ . '/../' . $f);
        }
    }
    redirect('admin/videos.php?deleted=1');
}

$videos = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC")->fetchAll();
?>

<h3 class="fw-bold mb-4">Video Gallery Management</h3>
<p class="text-muted mb-4">Videos uploaded here appear on the public Gallery page, where customers can play and view them.</p>

<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success alert-auto-dismiss">Video uploaded successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success alert-auto-dismiss">Video deleted.</div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card card-pcpoa p-4">
            <h6 class="fw-bold mb-3">Upload New Video</h6>
            <form method="post" enctype="multipart/form-data" class="form-pcpoa">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Video File (mp4, webm, ogg, mov)</label>
                    <input type="file" name="video_file" class="form-control" accept="video/*" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thumbnail Image (optional)</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="add_video" class="btn btn-brand-lime w-100">Upload Video</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row g-3">
            <?php foreach ($videos as $v): ?>
            <div class="col-md-6">
                <div class="card card-pcpoa p-3">
                    <video controls style="width:100%;height:160px;object-fit:cover;border-radius:10px;background:#000;">
                        <source src="../<?= e($v['video_file']) ?>" type="video/mp4">
                    </video>
                    <h6 class="fw-bold mt-2 mb-1"><?= e($v['title']) ?></h6>
                    <p class="small text-muted mb-2"><?= e($v['description']) ?></p>
                    <div class="d-flex gap-2">
                        <form method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="toggle_video" value="<?= (int)$v['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-brand-outline"><?= $v['status'] === 'active' ? 'Hide' : 'Show' ?></button>
                        </form>
                        <form method="post" class="confirm-delete-form d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_video" value="<?= (int)$v['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($videos)): ?>
                <p class="text-center text-muted">No videos uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
