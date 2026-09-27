<?php
$adminPageTitle = 'Orders';
require_once __DIR__ . '/includes/admin-header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    csrf_verify();
    $id = (int)$_POST['order_id'];
    $status = $_POST['order_status'];
    $payment = $_POST['payment_status'];
    $pdo->prepare("UPDATE orders SET order_status=?, payment_status=? WHERE id=?")->execute([$status, $payment, $id]);
    redirect('admin/orders.php?updated=1');
}

$orders = $pdo->query("SELECT o.*, u.full_name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC")->fetchAll();
$itemsStmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
?>

<h3 class="fw-bold mb-4">Manage Orders</h3>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success alert-auto-dismiss">Order updated.</div><?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="card card-pcpoa p-5 text-center text-muted">No orders placed yet.</div>
<?php endif; ?>

<?php foreach ($orders as $o): $itemsStmt->execute([$o['id']]); $orderItems = $itemsStmt->fetchAll(); ?>
<div class="card card-pcpoa p-4 mb-3">
    <div class="row g-3 align-items-center">
        <div class="col-md-3">
            <h6 class="fw-bold mb-1">Order #<?= (int)$o['id'] ?></h6>
            <p class="small text-muted mb-0"><?= e($o['full_name'] ?? 'Guest') ?><br><?= e($o['email'] ?? '') ?></p>
            <p class="small text-muted mb-0"><?= date('d M Y, H:i', strtotime($o['order_date'])) ?></p>
        </div>
        <div class="col-md-3">
            <p class="small mb-1"><strong>Address:</strong> <?= e($o['delivery_address']) ?></p>
            <p class="small mb-0"><strong>Phone:</strong> <?= e($o['phone']) ?></p>
        </div>
        <div class="col-md-3">
            <ul class="small mb-0 ps-3">
                <?php foreach ($orderItems as $it): ?>
                    <li><?= e($it['name'] ?? 'Product removed') ?> x<?= (int)$it['quantity'] ?></li>
                <?php endforeach; ?>
            </ul>
            <p class="fw-bold mt-2 mb-0"><?= money($o['total_amount']) ?></p>
        </div>
        <div class="col-md-3">
            <form method="post" class="d-flex flex-column gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                <select name="order_status" class="form-select form-select-sm">
                    <?php foreach (['Pending','Confirmed','Preparing','Out for Delivery','Delivered','Cancelled'] as $st): ?>
                        <option value="<?= $st ?>" <?= $o['order_status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="payment_status" class="form-select form-select-sm">
                    <option value="Unpaid" <?= $o['payment_status'] === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="Paid" <?= $o['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                </select>
                <button type="submit" name="update_order" class="btn btn-brand btn-sm">Update</button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
