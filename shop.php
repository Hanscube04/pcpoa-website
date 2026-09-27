<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Shop - PC Poa';

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();

$search = trim($_GET['search'] ?? '');
$categoryId = (int)($_GET['category'] ?? 0);

$sql = "SELECT * FROM products WHERE status='active'";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR brand LIKE ? OR specifications LIKE ?)";
    $like = "%$search%";
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($categoryId > 0) {
    $sql .= " AND category_id = ?";
    $params[] = $categoryId;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">Shop</h1>
        <p class="mb-0">Laptops, desktops, monitors, accessories and more.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <form method="get" class="row g-2 mb-4 form-pcpoa">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by name, brand or specification..." value="<?= e($search) ?>">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $categoryId === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-brand w-100" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            </div>
        </form>

        <div class="row g-4">
            <?php foreach ($products as $p): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card card-pcpoa">
                    <img src="<?= e($p['image'] ?: 'images/hero-macbook.jpg') ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <span class="badge <?= $p['stock'] > 0 ? 'badge-instock' : 'badge-outofstock' ?> mb-2 align-self-start"><?= $p['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?></span>
                        <h6 class="fw-bold mb-1"><?= e($p['name']) ?></h6>
                        <p class="small text-muted mb-2"><?= e($p['brand']) ?></p>
                        <p class="price-tag mb-3"><?= money($p['price']) ?></p>
                        <div class="mt-auto d-flex gap-2">
                            <a href="products/product-details.php?id=<?= (int)$p['id'] ?>" class="btn btn-brand-outline btn-sm flex-fill">Details</a>
                            <?php if ($p['stock'] > 0): ?>
                            <form action="cart.php" method="post" class="flex-fill">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button class="btn btn-brand btn-sm w-100" type="submit"><i class="fa-solid fa-cart-plus"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <p class="text-center text-muted">No products matched your search.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
