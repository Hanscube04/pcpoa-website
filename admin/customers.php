<?php
$adminPageTitle = 'Customers';
require_once __DIR__ . '/includes/admin-header.php';

// SECURITY: suspending/activating an account changes state, so it must be a
// CSRF-protected POST, not a bare GET link.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_customer'])) {
    csrf_verify();
    $id = (int)$_POST['toggle_customer'];
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ? AND role='customer'");
    $stmt->execute([$id]);
    $status = $stmt->fetchColumn();
    if ($status) {
        $newStatus = $status === 'active' ? 'suspended' : 'active';
        $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
    }
    redirect('admin/customers.php');
}

$customers = $pdo->query("SELECT u.*,
    (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS order_count,
    (SELECT COUNT(*) FROM repair_requests r WHERE r.user_id = u.id) AS repair_count
    FROM users u WHERE role = 'customer' ORDER BY created_at DESC")->fetchAll();
?>

<h3 class="fw-bold mb-4">Manage Customers</h3>

<div class="card card-pcpoa p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Repairs</th><th>Joined</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($customers as $c): ?>
                <tr>
                    <td><?= e($c['full_name']) ?></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['phone']) ?></td>
                    <td><?= (int)$c['order_count'] ?></td>
                    <td><?= (int)$c['repair_count'] ?></td>
                    <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                    <td><span class="status-badge <?= $c['status'] === 'active' ? 'status-done' : 'status-cancelled' ?>"><?= e($c['status']) ?></span></td>
                    <td>
                        <form method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="toggle_customer" value="<?= (int)$c['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-brand-outline"><?= $c['status'] === 'active' ? 'Suspend' : 'Activate' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($customers)): ?>
                <tr><td colspan="8" class="text-center text-muted">No customers registered yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
