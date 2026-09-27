<?php
$adminPageTitle = 'Website Settings';
require_once __DIR__ . '/includes/admin-header.php';

$settings = get_settings($pdo);
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    if (isset($_POST['update_info'])) {
        $siteName = trim($_POST['site_name'] ?? '');
        $slogan = trim($_POST['slogan'] ?? '');
        $phonePrimary = trim($_POST['phone_primary'] ?? '');
        $phoneSecondary = trim($_POST['phone_secondary'] ?? '');
        $whatsapp = preg_replace('/[^0-9]/', '', $_POST['whatsapp'] ?? ''); // digits only, e.g. 255712370777
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');
        $tiktok = trim($_POST['tiktok'] ?? '');
        $workingHours = trim($_POST['working_hours'] ?? '');

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $stmt = $pdo->prepare("UPDATE settings SET site_name=?, slogan=?, phone_primary=?, phone_secondary=?, whatsapp=?, email=?, address=?, instagram=?, tiktok=?, working_hours=? WHERE id=1");
            $stmt->execute([$siteName, $slogan, $phonePrimary, $phoneSecondary, $whatsapp, $email, $address, $instagram, $tiktok, $workingHours]);
            $success = 'Website information updated successfully.';
        }
    }

    if (isset($_POST['update_logo'])) {
        if (!empty($_FILES['logo']['name'])) {
            $uploaded = handle_image_upload($_FILES['logo'], __DIR__ . '/../uploads/logo', $settings['logo']);
            if ($uploaded) {
                $stmt = $pdo->prepare("UPDATE settings SET logo=? WHERE id=1");
                $stmt->execute([$uploaded]);
                $success = 'Logo updated successfully.';
            } else {
                $error = 'Logo upload failed. Please use a JPG, PNG or WEBP image.';
            }
        } else {
            $error = 'Please choose a logo file to upload.';
        }
    }

    if (isset($_POST['delete_logo'])) {
        if ($settings['logo'] && strpos($settings['logo'], 'uploads/') === 0 && file_exists(__DIR__ . '/../' . $settings['logo'])) {
            @unlink(__DIR__ . '/../' . $settings['logo']);
        }
        $stmt = $pdo->prepare("UPDATE settings SET logo=NULL WHERE id=1");
        $stmt->execute();
        $success = 'Logo removed.';
    }

    // Refresh settings after update
    $settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
}
?>

<h3 class="fw-bold mb-1">Website Settings</h3>
<p class="text-muted mb-4">Only administrators can update the site logo, contact details, social links and office location shown on every page.</p>

<?php if ($success): ?><div class="alert alert-success alert-auto-dismiss"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card card-pcpoa p-4">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-image me-2"></i>Site Logo</h6>
            <div class="text-center mb-3">
                <?php if ($settings['logo']): ?>
                    <img src="<?= BASE_URL . e($settings['logo']) ?>" class="img-fluid rounded-4 border" style="max-height:150px;">
                <?php else: ?>
                    <p class="text-muted">No logo uploaded.</p>
                <?php endif; ?>
            </div>
            <form method="post" enctype="multipart/form-data" class="form-pcpoa mb-2">
                <?= csrf_field() ?>
                <input type="file" name="logo" class="form-control mb-2" accept="image/*" required>
                <button type="submit" name="update_logo" class="btn btn-brand-lime w-100">Upload / Update Logo</button>
            </form>
            <?php if ($settings['logo']): ?>
            <form method="post" class="confirm-delete-form">
                <?= csrf_field() ?>
                <button type="submit" name="delete_logo" class="btn btn-outline-danger btn-sm w-100">Delete Logo</button>
            </form>
            <?php endif; ?>
            <small class="text-muted d-block mt-2">The logo appears top-left on every page. Only admins can change or delete it.</small>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-pcpoa p-4">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-gear me-2"></i>Site Information</h6>
            <form method="post" class="form-pcpoa">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slogan</label>
                        <input type="text" name="slogan" class="form-control" value="<?= e($settings['slogan']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Primary Phone</label>
                        <input type="text" name="phone_primary" class="form-control" value="<?= e($settings['phone_primary']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Secondary Phone</label>
                        <input type="text" name="phone_secondary" class="form-control" value="<?= e($settings['phone_secondary']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp Number (international format, no +)</label>
                        <input type="text" name="whatsapp" class="form-control" placeholder="255712370777" value="<?= e($settings['whatsapp']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= e($settings['email']) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Office Location / Address <span class="text-muted">(shown on every page)</span></label>
                        <input type="text" name="address" class="form-control" value="<?= e($settings['address']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram Username</label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" name="instagram" class="form-control" value="<?= e($settings['instagram']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TikTok Username</label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" name="tiktok" class="form-control" value="<?= e($settings['tiktok']) ?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Working Hours</label>
                        <input type="text" name="working_hours" class="form-control" value="<?= e($settings['working_hours']) ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="update_info" class="btn btn-brand-lime">Save Website Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
