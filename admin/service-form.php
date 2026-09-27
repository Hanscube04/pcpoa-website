<?php
$adminPageTitle = 'Service Form';
require_once __DIR__ . '/includes/admin-header.php';

$id = (int)($_GET['id'] ?? 0);
$service = ['id'=>0,'service_name'=>'','description'=>'','price'=>'','image'=>'','status'=>'active'];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) $service = $found;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name = trim($_POST['service_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if ($name === '') {
        $error = 'Please provide a service name.';
    } else {
        $imagePath = $service['image'];
        if (!empty($_FILES['image']['name'])) {
            $uploaded = handle_image_upload($_FILES['image'], __DIR__ . '/../uploads/services', $imagePath);
            if ($uploaded) $imagePath = $uploaded;
        }

        if ($service['id']) {
            $stmt = $pdo->prepare("UPDATE services SET service_name=?, description=?, price=?, image=?, status=? WHERE id=?");
            $stmt->execute([$name, $description, $price, $imagePath, $status, $service['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO services (service_name, description, price, image, status) VALUES (?,?,?,?,?)");
            $stmt->execute([$name, $description, $price, $imagePath, $status]);
        }
        redirect('admin/services.php?saved=1');
    }
}
?>

<h3 class="fw-bold mb-4"><?= $service['id'] ? 'Edit Service' : 'Add New Service' ?></h3>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<div class="card card-pcpoa p-4">
    <form method="post" enctype="multipart/form-data" class="form-pcpoa">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Service Name</label>
                <input type="text" name="service_name" class="form-control" required value="<?= e($service['service_name']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Estimated Price</label>
                <input type="text" name="price" class="form-control" placeholder="e.g. From TZS 25,000" value="<?= e($service['price']) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= e($service['description']) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Service Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if ($service['image']): ?>
                    <img src="../<?= e($service['image']) ?>" width="80" class="mt-2 rounded">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" <?= $service['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="hidden" <?= $service['status'] === 'hidden' ? 'selected' : '' ?>>Hidden</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-brand-lime">Save Service</button>
                <a href="services.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
