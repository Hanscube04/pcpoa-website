<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'My Repair Requests - PC Poa';

$userId = $_SESSION['user_id'];
$repairs = $pdo->prepare("SELECT * FROM repair_requests WHERE user_id = ? ORDER BY request_date DESC");
$repairs->execute([$userId]);
$repairs = $repairs->fetchAll();

require_once __DIR__ . '/../includes/header.php';
$statusClass = function ($status) {
    return match ($status) {
        'Completed' => 'status-done',
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
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h3 class="fw-bold mb-0">My Repair Requests</h3>
                    <a href="../repair/request.php" class="btn btn-brand-lime btn-sm">+ New Request</a>
                </div>

                <?php if (empty($repairs)): ?>
                    <div class="card card-pcpoa p-5 text-center">
                        <p class="text-muted mb-3">You haven't submitted any repair requests yet.</p>
                        <a href="../repair/request.php" class="btn btn-brand">Request a Repair</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($repairs as $r): ?>
                    <div class="card card-pcpoa p-4 mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-1"><?= e($r['device_type']) ?> &mdash; <?= e($r['brand']) ?> <?= e($r['model']) ?></h6>
                                <p class="text-muted small mb-1"><?= e($r['problem']) ?></p>
                                <small class="text-muted">Submitted: <?= date('d M Y', strtotime($r['request_date'])) ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge <?= $statusClass($r['status']) ?>"><?= e($r['status']) ?></span>
                                <?php if ($r['repair_cost']): ?>
                                    <p class="fw-bold mb-0 mt-1"><?= money($r['repair_cost']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
