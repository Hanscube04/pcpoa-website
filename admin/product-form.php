<?php
$adminPageTitle = 'Product Form';
require_once __DIR__ . '/includes/admin-header.php';

$id = (int)($_GET['id'] ?? 0);
$product = ['id'=>0,'name'=>'','brand'=>'','category_id'=>'','description'=>'','specifications'=>'','price'=>'','stock'=>'','image'=>'','status'=>'active'];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $product = $found;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
    $description = trim($_POST['description'] ?? '');
    $specifications = trim($_POST['specifications'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $status = $_POST['status'] ?? 'active';

    if ($name === '' || $price <= 0) {
        $error = 'Please provide a product name and a valid price.';
    } else {
        $imagePath = $product['image'];
        if (!empty($_FILES['image']['name'])) {
            $uploaded = handle_image_upload($_FILES['image'], __DIR__ . '/../uploads/products', $imagePath);
            if ($uploaded) $imagePath = $uploaded;
        }

        if ($product['id']) {
            $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, brand=?, description=?, specifications=?, price=?, stock=?, image=?, status=? WHERE id=?");
            $stmt->execute([$categoryId, $name, $brand, $description, $specifications, $price, $stock, $imagePath, $status, $product['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, brand, description, specifications, price, stock, image, status) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$categoryId, $name, $brand, $description, $specifications, $price, $stock, $imagePath, $status]);
        }
        redirect('admin/products.php?saved=1');
    }
}
?>

<h3 class="fw-bold mb-4"><?= $product['id'] ? 'Edit Product' : 'Add New Product' ?></h3>

<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<div class="card card-pcpoa p-4">
    <form method="post" enctype="multipart/form-data" class="form-pcpoa">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required value="<?= e($product['name']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" class="form-control" value="<?= e($product['brand']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Price (TZS)</label>
                <input type="number" name="price" class="form-control" required value="<?= e((string)$product['price']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Stock Quantity</label>
                <input type="number" name="stock" class="form-control" required value="<?= e((string)$product['stock']) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= e($product['description']) ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Specifications (separate each with a "|")</label>
                <textarea name="specifications" class="form-control" rows="2" placeholder="Intel Core i5 | 8GB RAM | 256GB SSD | 14 inch | Windows 11"><?= e($product['specifications']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Product Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if ($product['image']): ?>
                    <img src="../<?= e($product['image']) ?>" width="80" class="mt-2 rounded">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="hidden" <?= $product['status'] === 'hidden' ? 'selected' : '' ?>>Hidden</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-brand-lime">Save Product</button>
                <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
