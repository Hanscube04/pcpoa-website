<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Login - PC Poa';

if (is_logged_in()) {
    redirect(is_admin() ? 'admin/dashboard.php' : 'customer/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    // SECURITY: basic brute-force throttle - after 5 failed attempts within
    // 10 minutes from this session, force a short wait. This does not affect
    // legitimate users who type their password correctly.
    $_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
    $_SESSION['login_first_attempt'] = $_SESSION['login_first_attempt'] ?? time();

    if (time() - $_SESSION['login_first_attempt'] > 600) {
        // Window expired - reset the counter
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_first_attempt'] = time();
    }

    if ($_SESSION['login_attempts'] >= 5) {
        $error = 'Too many login attempts. Please wait a few minutes and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'suspended') {
                $error = 'Your account has been suspended. Please contact PC Poa support.';
            } else {
                unset($_SESSION['login_attempts'], $_SESSION['login_first_attempt']);

                // SECURITY: rotate the session ID on privilege change (login)
                // to prevent session fixation attacks. The cart and any other
                // session data are preserved automatically.
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    redirect('admin/dashboard.php');
                }
                $redirectTo = $_SESSION['redirect_after_login'] ?? 'customer/dashboard.php';
                unset($_SESSION['redirect_after_login']);
                redirect($redirectTo);
            }
        } else {
            $_SESSION['login_attempts']++;
            $error = 'Invalid email or password.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="card card-pcpoa auth-card p-4 p-md-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Welcome Back</h3>
                <p class="text-muted">Login to your PC Poa account</p>
            </div>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post" class="form-pcpoa">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email / Username</label>
                    <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-brand-lime w-100 btn-lg">Login</button>
            </form>
            <p class="text-center mt-4 mb-0 small">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
