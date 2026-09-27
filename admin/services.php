<?php
$adminPageTitle = 'Services';
require_once __DIR__ . '/includes/admin-header.php';

// SECURITY: delete must be a CSRF-protected POST, not a bare GET link.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    csrf_verify();
    $id = (int)$_POST['delete_service'];
    $img = $pdo->prepare("SELECT image FROM services WHERE id = ?");
    $img->execute([$id]);
    $imgPath = $img->fetchColumn();
    $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
    if ($imgPath && strpos($imgPath, 'uploads/') === 0 && file_exists(__DIR__ . '/../' . $imgPath)) {
        @unlink(__DIR__ . '/../' . $imgPath);
    }
    redirect('admin/services.php?deleted=1');
}

$services = $pdo->query("SELECT * FROM services ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold mb-0">Manage Services</h3>
    <a href="service-form.php" class="btn btn-brand-lime"><i class="fa-solid fa-plus"></i> Add Service</a>
</div>

<?php if (isset($_GET['saved'])): ?><div class="alert alert-success alert-auto-dismiss">Service saved.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success alert-auto-dismiss">Service deleted.</div><?php endif; ?>

<div class="card card-pcpoa p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Image</th><th>Service</th><th>Price</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($services as $s): ?>
                <tr>
                    <td><img src="../<?= e($s['image'] ?: 'images/what-we-fix.jpeg') ?>" width="50" height="50" style="object-fit:cover;border-radius:8px;"></td>
                    <td><?= e($s['service_name']) ?></td>
                    <td><?= e($s['price']) ?></td>
                    <td><span class="status-badge <?= $s['status'] === 'active' ? 'status-done' : 'status-cancelled' ?>"><?= e($s['status']) ?></span></td>
                    <td class="text-nowrap">
                        <a href="service-form.php?id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-brand-outline"><i class="fa-solid fa-pen"></i></a>
                        <form method="post" class="confirm-delete-form d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_service" value="<?= (int)$s['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($services)): ?>
                <tr><td colspan="5" class="text-center text-muted">No services added yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
