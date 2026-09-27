<?php
$adminPageTitle = 'Products';
require_once __DIR__ . '/includes/admin-header.php';

// SECURITY: delete must be a CSRF-protected POST, not a bare GET link.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    csrf_verify();
    $id = (int)$_POST['delete_product'];
    $img = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $img->execute([$id]);
    $imgPath = $img->fetchColumn();
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    if ($imgPath && strpos($imgPath, 'uploads/') === 0 && file_exists(__DIR__ . '/../' . $imgPath)) {
        @unlink(__DIR__ . '/../' . $imgPath);
    }
    redirect('admin/products.php?deleted=1');
}

$products = $pdo->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold mb-0">Manage Products</h3>
    <a href="product-form.php" class="btn btn-brand-lime"><i class="fa-solid fa-plus"></i> Add Product</a>
</div>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success alert-auto-dismiss">Product deleted.</div><?php endif; ?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success alert-auto-dismiss">Product saved successfully.</div><?php endif; ?>

<div class="card card-pcpoa p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><img src="../<?= e($p['image'] ?: 'images/hero-macbook.jpg') ?>" width="50" height="50" style="object-fit:cover;border-radius:8px;"></td>
                    <td><?= e($p['name']) ?><br><small class="text-muted"><?= e($p['brand']) ?></small></td>
                    <td><?= e($p['category_name'] ?? '-') ?></td>
                    <td><?= money($p['price']) ?></td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td><span class="status-badge <?= $p['status'] === 'active' ? 'status-done' : 'status-cancelled' ?>"><?= e($p['status']) ?></span></td>
                    <td class="text-nowrap">
                        <a href="product-form.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-brand-outline"><i class="fa-solid fa-pen"></i></a>
                        <form method="post" class="confirm-delete-form d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_product" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-center text-muted">No products added yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
