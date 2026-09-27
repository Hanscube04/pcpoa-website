<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Request Repair - PC Poa';

$success = false;
$error = '';
$prefilledService = trim($_GET['service'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $deviceType = trim($_POST['device_type'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $problem = trim($_POST['problem'] ?? '');
    $preferredDate = $_POST['preferred_date'] ?? null;
    $additionalInfo = trim($_POST['additional_info'] ?? '');

    if ($name === '' || $phone === '' || $deviceType === '' || $problem === '') {
        $error = 'Please fill in your name, phone, device type and problem description.';
    } elseif (!preg_match('/^[0-9+\s\-()]{7,20}$/', $phone)) {
        $error = 'Please enter a valid phone number.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO repair_requests
            (user_id, customer_name, phone, email, device_type, brand, model, problem, preferred_date, additional_info)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $name, $phone, $email, $deviceType, $brand, $model, $problem,
            $preferredDate ?: null, $additionalInfo,
        ]);
        $success = true;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">Request a Repair</h1>
        <p class="mb-0">Tell us about your device and we'll get it fixed.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <h5 class="fw-bold">Repair request submitted!</h5>
                        <p class="mb-0">Our team will contact you shortly to confirm the details. You can also reach us directly on WhatsApp for urgent issues.</p>
                    </div>
                    <a href="../index.php" class="btn btn-brand">Back to Home</a>
                <?php else: ?>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post" class="form-pcpoa card card-pcpoa p-4 p-md-5">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="customer_name" class="form-control" required value="<?= e($_POST['customer_name'] ?? ($_SESSION['user_name'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Device Type</label>
                            <select name="device_type" class="form-select" required>
                                <option value="">Select device type</option>
                                <option value="Laptop" <?= ($_POST['device_type'] ?? '') === 'Laptop' ? 'selected' : '' ?>>Laptop</option>
                                <option value="Desktop" <?= ($_POST['device_type'] ?? '') === 'Desktop' ? 'selected' : '' ?>>Desktop</option>
                                <option value="Other" <?= ($_POST['device_type'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control" placeholder="e.g. Dell, HP, Lenovo, Apple" value="<?= e($_POST['brand'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" class="form-control" placeholder="e.g. Latitude 3310" value="<?= e($_POST['model'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Problem Description</label>
                            <textarea name="problem" class="form-control" rows="4" required placeholder="Describe the issue in detail..."><?= e($_POST['problem'] ?? ($prefilledService ? "Service requested: $prefilledService\n" : '')) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Preferred Date</label>
                            <input type="date" name="preferred_date" class="form-control" value="<?= e($_POST['preferred_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Additional Information</label>
                            <input type="text" name="additional_info" class="form-control" value="<?= e($_POST['additional_info'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-brand-lime btn-lg w-100">Submit Repair Request</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
