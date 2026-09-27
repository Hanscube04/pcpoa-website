<?php
$adminPageTitle = 'Dashboard';
require_once __DIR__ . '/includes/admin-header.php';

$counts = [
    'customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
    'products'  => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'orders'    => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'repairs'   => $pdo->query("SELECT COUNT(*) FROM repair_requests")->fetchColumn(),
    'messages'  => $pdo->query("SELECT COUNT(*) FROM messages WHERE status='unread'")->fetchColumn(),
];

$recentRepairs = $pdo->query("SELECT * FROM repair_requests ORDER BY request_date DESC LIMIT 5")->fetchAll();
$recentOrders = $pdo->query("SELECT o.*, u.full_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY order_date DESC LIMIT 5")->fetchAll();
?>

<h3 class="fw-bold mb-4">Dashboard Overview</h3>

<div class="row g-4 mb-4">
    <div class="col-lg col-md-4 col-6">
        <div class="dash-stat-card">
            <i class="fa-solid fa-users fs-3 mb-2"></i>
            <h3 class="fw-bold mb-0"><?= (int)$counts['customers'] ?></h3>
            <p class="mb-0 small">Customers</p>
        </div>
    </div>
    <div class="col-lg col-md-4 col-6">
        <div class="dash-stat-card">
            <i class="fa-solid fa-laptop fs-3 mb-2"></i>
            <h3 class="fw-bold mb-0"><?= (int)$counts['products'] ?></h3>
            <p class="mb-0 small">Products</p>
        </div>
    </div>
    <div class="col-lg col-md-4 col-6">
        <div class="dash-stat-card">
            <i class="fa-solid fa-bag-shopping fs-3 mb-2"></i>
            <h3 class="fw-bold mb-0"><?= (int)$counts['orders'] ?></h3>
            <p class="mb-0 small">Orders</p>
        </div>
    </div>
    <div class="col-lg col-md-4 col-6">
        <div class="dash-stat-card">
            <i class="fa-solid fa-screwdriver-wrench fs-3 mb-2"></i>
            <h3 class="fw-bold mb-0"><?= (int)$counts['repairs'] ?></h3>
            <p class="mb-0 small">Repair Requests</p>
        </div>
    </div>
    <div class="col-lg col-md-4 col-6">
        <div class="dash-stat-card">
            <i class="fa-solid fa-envelope fs-3 mb-2"></i>
            <h3 class="fw-bold mb-0"><?= (int)$counts['messages'] ?></h3>
            <p class="mb-0 small">Unread Messages</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card card-pcpoa p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Recent Repair Requests</h6>
                <a href="repairs.php" class="small">View all</a>
            </div>
            <?php if (empty($recentRepairs)): ?>
                <p class="text-muted small mb-0">No repair requests yet.</p>
            <?php else: ?>
            <table class="table table-sm align-middle">
                <tbody>
                <?php foreach ($recentRepairs as $r): ?>
                    <tr>
                        <td><?= e($r['customer_name']) ?><br><small class="text-muted"><?= e($r['device_type']) ?> - <?= e($r['brand']) ?></small></td>
                        <td><span class="status-badge status-pending"><?= e($r['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-pcpoa p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Recent Orders</h6>
                <a href="orders.php" class="small">View all</a>
            </div>
            <?php if (empty($recentOrders)): ?>
                <p class="text-muted small mb-0">No orders yet.</p>
            <?php else: ?>
            <table class="table table-sm align-middle">
                <tbody>
                <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td>#<?= (int)$o['id'] ?> - <?= e($o['full_name'] ?? 'Guest') ?></td>
                        <td><?= money($o['total_amount']) ?></td>
                        <td><span class="status-badge status-progress"><?= e($o['order_status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
