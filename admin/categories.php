<?php
$adminPageTitle = 'Categories';
require_once __DIR__ . '/includes/admin-header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    csrf_verify();
    $name = trim($_POST['category_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
        $stmt->execute([$name, $desc]);
        redirect('admin/categories.php?saved=1');
    }
}

// SECURITY: deleting is a state-changing action, so it must be a POST
// request with a valid CSRF token - not a plain GET link, which could be
// triggered by a hidden image/link on another site (CSRF) or by a crawler.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    csrf_verify();
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([(int)$_POST['delete_category']]);
    redirect('admin/categories.php?deleted=1');
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count FROM categories c ORDER BY category_name")->fetchAll();
?>

<h3 class="fw-bold mb-4">Manage Categories</h3>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success alert-auto-dismiss">Category added.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success alert-auto-dismiss">Category deleted.</div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-pcpoa p-4">
            <h6 class="fw-bold mb-3">Add New Category</h6>
            <form method="post" class="form-pcpoa">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="category_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" name="add_category" class="btn btn-brand-lime w-100">Add Category</button>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-pcpoa p-4">
            <table class="table align-middle">
                <thead><tr><th>Name</th><th>Products</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><?= e($c['category_name']) ?><br><small class="text-muted"><?= e($c['description']) ?></small></td>
                        <td><?= (int)$c['product_count'] ?></td>
                        <td>
                            <form method="post" class="confirm-delete-form d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="delete_category" value="<?= (int)$c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger confirm-delete-btn"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
