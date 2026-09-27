<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Register - PC Poa';

if (is_logged_in()) {
    redirect(is_admin() ? 'admin/dashboard.php' : 'customer/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // SECURITY: server-side validation - the HTML5 "type=email" attribute
    // alone can be bypassed, so we re-check format here too.
    if ($fullName === '' || $email === '' || $phone === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[0-9+\s\-()]{7,20}$/', $phone)) {
        $error = 'Please enter a valid phone number.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
            $stmt->execute([$fullName, $email, $phone, $hash]);

            // SECURITY: rotate session ID on privilege change (new login session)
            session_regenerate_id(true);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $fullName;
            $_SESSION['role'] = 'customer';
            redirect('customer/dashboard.php');
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="card card-pcpoa auth-card p-4 p-md-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Create an Account</h3>
                <p class="text-muted">Join PC Poa to track orders and repair requests</p>
            </div>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post" class="form-pcpoa">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required value="<?= e($_POST['full_name'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" required value="<?= e($_POST['phone'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-brand-lime w-100 btn-lg">Register</button>
            </form>
            <p class="text-center mt-4 mb-0 small">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
