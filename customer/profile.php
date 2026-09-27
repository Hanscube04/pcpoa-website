<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$pageTitle = 'My Profile - PC Poa';

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if ($fullName === '' || $phone === '') {
        $error = 'Name and phone are required.';
    } elseif (!preg_match('/^[0-9+\s\-()]{7,20}$/', $phone)) {
        $error = 'Please enter a valid phone number.';
    } else {
        if ($newPassword !== '') {
            if (strlen($newPassword) < 6) {
                $error = 'New password must be at least 6 characters.';
            } else {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE users SET full_name=?, phone=?, password=? WHERE id=?");
                $upd->execute([$fullName, $phone, $hash, $userId]);
            }
        } else {
            $upd = $pdo->prepare("UPDATE users SET full_name=?, phone=? WHERE id=?");
            $upd->execute([$fullName, $phone, $userId]);
        }

        if (!$error) {
            $_SESSION['user_name'] = $fullName;
            $success = 'Profile updated successfully.';
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php include __DIR__ . '/includes/sidebar.php'; ?></div>
            <div class="col-lg-9">
                <h3 class="fw-bold mb-4">My Profile</h3>
                <?php if ($success): ?><div class="alert alert-success alert-auto-dismiss"><?= e($success) ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <div class="card card-pcpoa p-4">
                    <form method="post" class="form-pcpoa">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" required value="<?= e($user['full_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" required value="<?= e($user['phone']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">New Password (leave blank to keep current)</label>
                                <input type="password" name="new_password" class="form-control">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-brand-lime">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
