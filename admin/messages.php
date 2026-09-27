<?php
$adminPageTitle = 'Messages';
require_once __DIR__ . '/includes/admin-header.php';

// SECURITY: marking-read/delete are state-changing actions, so both must be
// CSRF-protected POST requests rather than bare GET links.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    csrf_verify();
    $pdo->prepare("UPDATE messages SET status='read' WHERE id = ?")->execute([(int)$_POST['mark_read']]);
    redirect('admin/messages.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    csrf_verify();
    $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([(int)$_POST['delete_message']]);
    redirect('admin/messages.php?deleted=1');
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>

<h3 class="fw-bold mb-4">Customer Messages</h3>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success alert-auto-dismiss">Message deleted.</div><?php endif; ?>

<?php if (empty($messages)): ?>
    <div class="card card-pcpoa p-5 text-center text-muted">No messages received yet.</div>
<?php endif; ?>

<?php foreach ($messages as $m): ?>
<div class="card card-pcpoa p-4 mb-3 <?= $m['status'] === 'unread' ? 'border-start border-4' : '' ?>" style="<?= $m['status'] === 'unread' ? 'border-color:var(--pcpoa-green)!important;' : '' ?>">
    <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
        <div>
            <h6 class="fw-bold mb-1"><?= e($m['name']) ?> <?php if ($m['status'] === 'unread'): ?><span class="badge bg-success ms-2">New</span><?php endif; ?></h6>
            <p class="small text-muted mb-0"><?= e($m['email']) ?> <?= $m['phone'] ? '| ' . e($m['phone']) : '' ?></p>
        </div>
        <small class="text-muted"><?= date('d M Y, H:i', strtotime($m['created_at'])) ?></small>
    </div>
    <?php if ($m['subject']): ?><p class="fw-semibold mb-1"><?= e($m['subject']) ?></p><?php endif; ?>
    <p class="mb-3"><?= nl2br(e($m['message'])) ?></p>
    <div class="d-flex gap-2">
        <a href="mailto:<?= e($m['email']) ?>" class="btn btn-sm btn-brand-outline">Reply by Email</a>
        <?php if ($m['status'] === 'unread'): ?>
            <form method="post" class="d-inline">
                <?= csrf_field() ?>
                <input type="hidden" name="mark_read" value="<?= (int)$m['id'] ?>">
                <button type="submit" class="btn btn-sm btn-brand">Mark as Read</button>
            </form>
        <?php endif; ?>
        <form method="post" class="confirm-delete-form d-inline">
            <?= csrf_field() ?>
            <input type="hidden" name="delete_message" value="<?= (int)$m['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </div>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
