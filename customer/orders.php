<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'My Orders - PC Poa';

$userId = $_SESSION['user_id'];
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$orders->execute([$userId]);
$orders = $orders->fetchAll();

$itemsStmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");

require_once __DIR__ . '/../includes/header.php';
$statusClass = function ($status) {
    return match ($status) {
        'Delivered' => 'status-done',
        'Cancelled' => 'status-cancelled',
        'Pending' => 'status-pending',
        default => 'status-progress',
    };
};
?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php include __DIR__ . '/includes/sidebar.php'; ?></div>
            <div class="col-lg-9">
                <h3 class="fw-bold mb-4">My Orders</h3>
                <?php if (isset($_GET['placed'])): ?>
                    <div class="alert alert-success alert-auto-dismiss">Your order has been placed successfully!</div>
                <?php endif; ?>

                <?php if (empty($orders)): ?>
                    <div class="card card-pcpoa p-5 text-center">
                        <p class="text-muted mb-3">You haven't placed any orders yet.</p>
                        <a href="../shop.php" class="btn btn-brand">Go to Shop</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $o): $itemsStmt->execute([$o['id']]); $orderItems = $itemsStmt->fetchAll(); ?>
                    <div class="card card-pcpoa p-4 mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <h6 class="fw-bold mb-1">Order #<?= (int)$o['id'] ?></h6>
                                <small class="text-muted"><?= date('d M Y, H:i', strtotime($o['order_date'])) ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge <?= $statusClass($o['order_status']) ?>"><?= e($o['order_status']) ?></span>
                                <p class="fw-bold mb-0 mt-1"><?= money($o['total_amount']) ?></p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <tbody>
                                <?php foreach ($orderItems as $it): ?>
                                    <tr>
                                        <td style="width:50px;"><img src="../<?= e($it['image'] ?: 'images/hero-macbook.jpg') ?>" width="40" height="40" style="object-fit:cover;border-radius:6px;"></td>
                                        <td><?= e($it['name'] ?? 'Product removed') ?></td>
                                        <td>x<?= (int)$it['quantity'] ?></td>
                                        <td class="text-end"><?= money($it['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
