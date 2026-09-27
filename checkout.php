<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Checkout - PC Poa';

$items = cart_items($pdo);
if (empty($items)) {
    redirect('cart.php');
}

if (!is_logged_in()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    redirect('login.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $address = trim($_POST['delivery_address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($address === '' || $phone === '') {
        $error = 'Please provide your delivery address and phone number.';
    } elseif (!preg_match('/^[0-9+\s\-()]{7,20}$/', $phone)) {
        $error = 'Please enter a valid phone number.';
    } else {
        // SECURITY: re-check stock right before committing the order, so a
        // customer can never order more than what's actually available -
        // even if they added items to the cart a while ago.
        $stockCheckStmt = $pdo->prepare("SELECT stock, name FROM products WHERE id = ?");
        $insufficient = [];
        foreach ($items as $item) {
            $stockCheckStmt->execute([$item['product']['id']]);
            $current = $stockCheckStmt->fetch();
            if (!$current || (int)$current['stock'] < $item['quantity']) {
                $insufficient[] = $current['name'] ?? $item['product']['name'];
            }
        }

        if (!empty($insufficient)) {
            $error = 'Sorry, the following item(s) no longer have enough stock: ' . implode(', ', $insufficient) . '. Please update your cart.';
        } else {
            $total = cart_total($pdo);
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_address, phone) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $total, $address, $phone]);
                $orderId = $pdo->lastInsertId();

                $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stockStmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");

                foreach ($items as $item) {
                    $itemStmt->execute([$orderId, $item['product']['id'], $item['quantity'], $item['product']['price'], $item['subtotal']]);
                    $stockStmt->execute([$item['quantity'], $item['product']['id']]);
                }

                $pdo->commit();
                cart_clear();
                redirect('customer/orders.php?placed=1');
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Something went wrong while placing your order. Please try again.';
            }
        }
    }
}

$total = cart_total($pdo);
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">Checkout</h1>
        <p class="mb-0">Confirm your delivery details to place your order.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post" class="form-pcpoa">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Delivery Address</label>
                        <textarea name="delivery_address" class="form-control" rows="3" required><?= e($_POST['delivery_address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required value="<?= e($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="alert alert-info small">Payment: our first version supports manual confirmation (cash on collection or bank transfer). Our admin will contact you to confirm payment method.</div>
                    <button type="submit" class="btn btn-brand-lime btn-lg w-100">Place Order</button>
                </form>
            </div>
            <div class="col-lg-5">
                <div class="card card-pcpoa p-4">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span><?= e($item['product']['name']) ?> x<?= (int)$item['quantity'] ?></span>
                            <span><?= money($item['subtotal']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span><span><?= money($total) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
