<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Shopping Cart - PC Poa';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);

    if ($action === 'add' && $productId > 0) {
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        cart_add($productId, $qty);
        if (isset($_POST['buy_now'])) {
            redirect('cart.php');
        }
    } elseif ($action === 'update' && $productId > 0) {
        cart_update($productId, (int)($_POST['quantity'] ?? 1));
    } elseif ($action === 'remove' && $productId > 0) {
        cart_remove($productId);
    } elseif ($action === 'clear') {
        cart_clear();
    }
    redirect('cart.php');
}

$items = cart_items($pdo);
$total = cart_total($pdo);

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">Shopping Cart</h1>
        <p class="mb-0">Review your items before checkout.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (empty($items)): ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-cart-shopping fs-1 text-muted mb-3"></i>
                <h5>Your cart is empty</h5>
                <a href="shop.php" class="btn btn-brand mt-3">Continue Shopping</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): $p = $item['product']; ?>
                    <tr>
                        <td class="d-flex align-items-center gap-3">
                            <img src="<?= e($p['image'] ?: 'images/hero-macbook.jpg') ?>" width="60" height="60" style="object-fit:cover;border-radius:8px;">
                            <span><?= e($p['name']) ?></span>
                        </td>
                        <td><?= money($p['price']) ?></td>
                        <td style="width:140px;">
                            <form method="post" class="d-flex gap-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" class="form-control form-control-sm" style="width:70px;">
                                <button class="btn btn-sm btn-brand-outline" type="submit">Update</button>
                            </form>
                        </td>
                        <td class="fw-bold"><?= money($item['subtotal']) ?></td>
                        <td>
                            <form method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">
            <a href="shop.php" class="btn btn-brand-outline">Continue Shopping</a>
            <div class="text-end">
                <h5>Total: <span class="price-tag"><?= money($total) ?></span></h5>
                <a href="checkout.php" class="btn btn-brand-lime btn-lg mt-2">Proceed to Checkout</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
