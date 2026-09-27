<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'My Dashboard - PC Poa';

$userId = $_SESSION['user_id'];
$orderCount = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$orderCount->execute([$userId]);
$orderCount = $orderCount->fetchColumn();

$repairCount = $pdo->prepare("SELECT COUNT(*) FROM repair_requests WHERE user_id = ?");
$repairCount->execute([$userId]);
$repairCount = $repairCount->fetchColumn();

$pendingRepairs = $pdo->prepare("SELECT COUNT(*) FROM repair_requests WHERE user_id = ? AND status NOT IN ('Completed','Cancelled')");
$pendingRepairs->execute([$userId]);
$pendingRepairs = $pendingRepairs->fetchColumn();

$recentOrders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5");
$recentOrders->execute([$userId]);
$recentOrders = $recentOrders->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <?php include __DIR__ . '/includes/sidebar.php'; ?>
            </div>
            <div class="col-lg-9">
                <h3 class="fw-bold mb-1">Welcome, <?= e($_SESSION['user_name']) ?></h3>
                <p class="text-muted mb-4">Here's a summary of your PC Poa account.</p>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="dash-stat-card">
                            <i class="fa-solid fa-bag-shopping fs-3 mb-2"></i>
                            <h3 class="fw-bold mb-0"><?= (int)$orderCount ?></h3>
                            <p class="mb-0 small">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dash-stat-card">
                            <i class="fa-solid fa-screwdriver-wrench fs-3 mb-2"></i>
                            <h3 class="fw-bold mb-0"><?= (int)$repairCount ?></h3>
                            <p class="mb-0 small">Repair Requests</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dash-stat-card">
                            <i class="fa-solid fa-hourglass-half fs-3 mb-2"></i>
                            <h3 class="fw-bold mb-0"><?= (int)$pendingRepairs ?></h3>
                            <p class="mb-0 small">Repairs In Progress</p>
                        </div>
                    </div>
                </div>

                <div class="card card-pcpoa p-4">
                    <h5 class="fw-bold mb-3">Recent Orders</h5>
                    <?php if (empty($recentOrders)): ?>
                        <p class="text-muted mb-0">You haven't placed any orders yet. <a href="../shop.php">Browse the shop</a></p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                            <?php foreach ($recentOrders as $o): ?>
                                <tr>
                                    <td>#<?= (int)$o['id'] ?></td>
                                    <td><?= date('d M Y', strtotime($o['order_date'])) ?></td>
                                    <td><?= money($o['total_amount']) ?></td>
                                    <td><span class="status-badge status-progress"><?= e($o['order_status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="orders.php" class="btn btn-brand-outline btn-sm">View All Orders</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
