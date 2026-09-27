<?php
$adminPageTitle = 'Repair Requests';
require_once __DIR__ . '/includes/admin-header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_repair'])) {
    csrf_verify();
    $id = (int)$_POST['repair_id'];
    $status = $_POST['status'];
    $cost = $_POST['repair_cost'] !== '' ? (float)$_POST['repair_cost'] : null;
    $completedDate = $status === 'Completed' ? date('Y-m-d H:i:s') : null;

    $stmt = $pdo->prepare("UPDATE repair_requests SET status=?, repair_cost=?, completed_date=COALESCE(?, completed_date) WHERE id=?");
    $stmt->execute([$status, $cost, $completedDate, $id]);
    redirect('admin/repairs.php?updated=1');
}

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT * FROM repair_requests";
$params = [];
if ($statusFilter !== '') {
    $sql .= " WHERE status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY request_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$repairs = $stmt->fetchAll();

$statuses = ['Pending','Received','Diagnosing','Repairing','Ready for Collection','Completed','Cancelled'];
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold mb-0">Repair Requests</h3>
    <form method="get" class="d-flex gap-2">
        <select name="status" class="form-select form-select-sm auto-submit-select">
            <option value="">All Statuses</option>
            <?php foreach ($statuses as $st): ?>
                <option value="<?= $st ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success alert-auto-dismiss">Repair request updated.</div><?php endif; ?>

<?php if (empty($repairs)): ?>
    <div class="card card-pcpoa p-5 text-center text-muted">No repair requests found.</div>
<?php endif; ?>

<?php foreach ($repairs as $r): ?>
<div class="card card-pcpoa p-4 mb-3">
    <div class="row g-3">
        <div class="col-md-4">
            <h6 class="fw-bold mb-1"><?= e($r['customer_name']) ?></h6>
            <p class="small text-muted mb-1"><?= e($r['phone']) ?> <?= $r['email'] ? '| ' . e($r['email']) : '' ?></p>
            <p class="small mb-0"><strong><?= e($r['device_type']) ?></strong> - <?= e($r['brand']) ?> <?= e($r['model']) ?></p>
        </div>
        <div class="col-md-4">
            <p class="small mb-1"><?= e($r['problem']) ?></p>
            <?php if ($r['additional_info']): ?><p class="small text-muted mb-0">Note: <?= e($r['additional_info']) ?></p><?php endif; ?>
            <small class="text-muted">Requested: <?= date('d M Y', strtotime($r['request_date'])) ?></small>
        </div>
        <div class="col-md-4">
            <form method="post" class="d-flex flex-column gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="repair_id" value="<?= (int)$r['id'] ?>">
                <select name="status" class="form-select form-select-sm">
                    <?php foreach ($statuses as $st): ?>
                        <option value="<?= $st ?>" <?= $r['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="repair_cost" class="form-control form-control-sm" placeholder="Repair cost (TZS)" value="<?= e($r['repair_cost'] ?? '') ?>">
                <button type="submit" name="update_repair" class="btn btn-brand btn-sm">Update</button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
