<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Contact Us - PC Poa';
$settings = get_settings($pdo);
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = 'Please fill in your name, email and message.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        $success = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">Contact PC Poa</h1>
        <p class="mb-0">We'd love to hear from you. Reach out any time.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <h4 class="fw-bold mb-4">Get in Touch</h4>
                <div class="d-flex gap-3 mb-3">
                    <div class="service-icon-badge flex-shrink-0"><i class="fa-solid fa-location-dot"></i></div>
                    <div><h6 class="fw-bold mb-1">Office Location</h6><p class="text-muted mb-0"><?= e($settings['address'] ?? '') ?></p></div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div class="service-icon-badge flex-shrink-0"><i class="fa-solid fa-phone"></i></div>
                    <div><h6 class="fw-bold mb-1">Phone</h6><p class="text-muted mb-0"><?= e($settings['phone_primary'] ?? '') ?> / <?= e($settings['phone_secondary'] ?? '') ?></p></div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div class="service-icon-badge flex-shrink-0"><i class="fa-solid fa-envelope"></i></div>
                    <div><h6 class="fw-bold mb-1">Email</h6><p class="text-muted mb-0"><?= e($settings['email'] ?? '') ?></p></div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div class="service-icon-badge flex-shrink-0"><i class="fa-solid fa-clock"></i></div>
                    <div><h6 class="fw-bold mb-1">Working Hours</h6><p class="text-muted mb-0"><?= e($settings['working_hours'] ?? '') ?></p></div>
                </div>
                <div class="d-flex gap-3">
                    <a href="https://instagram.com/<?= e($settings['instagram'] ?? '') ?>" target="_blank" class="btn btn-brand-outline"><i class="fa-brands fa-instagram"></i> Instagram</a>
                    <a href="https://tiktok.com/@<?= e($settings['tiktok'] ?? '') ?>" target="_blank" class="btn btn-brand-outline"><i class="fa-brands fa-tiktok"></i> TikTok</a>
                </div>

                <div class="ratio ratio-4x3 mt-4 rounded-4 overflow-hidden shadow-sm">
                    <iframe src="https://www.google.com/maps?q=<?= urlencode($settings['address'] ?? 'Kariakoo, Dar es Salaam') ?>&output=embed" style="border:0;" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
            <div class="col-lg-7">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-auto-dismiss">Thank you! Your message has been sent. We'll get back to you soon.</div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>
                <form method="post" class="form-pcpoa card card-pcpoa p-4">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required value="<?= e($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="<?= e($_POST['subject'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" required><?= e($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-brand-lime btn-lg w-100">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
