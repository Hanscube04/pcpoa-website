<?php
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.status='active'");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    require_once __DIR__ . '/../includes/functions.php';
    $pageTitle = 'Product Not Found - PC Poa';
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="container py-5 text-center"><h3>Product not found.</h3><a href="../shop.php" class="btn btn-brand mt-3">Back to Shop</a></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = $product['name'] . ' - PC Poa';
$related = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND status='active' LIMIT 4");
$related->execute([$product['category_id'], $product['id']]);
$relatedProducts = $related->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="../shop.php">Shop</a></li>
                <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
            </ol>
        </nav>
        <div class="row g-5">
            <div class="col-lg-6">
                <img src="../<?= e($product['image'] ?: 'images/hero-macbook.jpg') ?>" class="img-fluid rounded-4 shadow-sm w-100" alt="<?= e($product['name']) ?>">
            </div>
            <div class="col-lg-6">
                <span class="badge bg-secondary-subtle text-dark mb-2"><?= e($product['category_name'] ?? 'Uncategorized') ?></span>
                <h2 class="fw-bold"><?= e($product['name']) ?></h2>
                <p class="text-muted mb-2">Brand: <strong><?= e($product['brand']) ?></strong></p>
                <span class="badge <?= $product['stock'] > 0 ? 'badge-instock' : 'badge-outofstock' ?> mb-3"><?= $product['stock'] > 0 ? 'In Stock (' . (int)$product['stock'] . ' available)' : 'Out of Stock' ?></span>
                <h3 class="price-tag mb-3"><?= money($product['price']) ?></h3>
                <p><?= nl2br(e($product['description'])) ?></p>

                <?php if (!empty($product['specifications'])): ?>
                <h6 class="fw-bold mt-4">Specifications</h6>
                <ul class="list-group list-group-flush mb-4">
                    <?php foreach (explode('|', $product['specifications']) as $spec): ?>
                        <?php $spec = trim($spec); if ($spec === '') continue; ?>
                        <li class="list-group-item px-0"><i class="fa-solid fa-check text-success me-2"></i><?= e($spec) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <?php if ($product['stock'] > 0): ?>
                <form action="../cart.php" method="post" class="d-flex gap-3 align-items-center">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                    <div class="qty-stepper d-flex align-items-center border rounded-3">
                        <button type="button" class="btn btn-sm" data-action="dec">-</button>
                        <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" class="form-control border-0 text-center" style="width:60px;">
                        <button type="button" class="btn btn-sm" data-action="inc">+</button>
                    </div>
                    <button type="submit" class="btn btn-brand-outline btn-lg"><i class="fa-solid fa-cart-plus me-2"></i>Add to Cart</button>
                    <button type="submit" name="buy_now" value="1" class="btn btn-brand btn-lg">Order Now</button>
                </form>
                <?php else: ?>
                    <p class="text-danger fw-bold">This product is currently out of stock.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($relatedProducts)): ?>
        <h4 class="fw-bold mt-5 mb-4">Related Products</h4>
        <div class="row g-4">
            <?php foreach ($relatedProducts as $rp): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card card-pcpoa">
                    <img src="../<?= e($rp['image'] ?: 'images/hero-macbook.jpg') ?>" class="card-img-top" alt="<?= e($rp['name']) ?>">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1"><?= e($rp['name']) ?></h6>
                        <p class="price-tag mb-2"><?= money($rp['price']) ?></p>
                        <a href="product-details.php?id=<?= (int)$rp['id'] ?>" class="btn btn-brand-outline btn-sm w-100">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
