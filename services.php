<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Our Services - PC Poa';
$services = $pdo->query("SELECT * FROM services WHERE status='active' ORDER BY id ASC")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">What We Fix</h1>
        <p class="mb-0">All about making your PC feel brand new.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($services as $s): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card card-pcpoa">
                    <img src="<?= e($s['image'] ?: 'images/what-we-fix.jpeg') ?>" class="card-img-top" alt="<?= e($s['service_name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-2"><?= e($s['service_name']) ?></h6>
                        <p class="small text-muted mb-2 flex-grow-1"><?= nl2br(e($s['description'])) ?></p>
                        <?php if (!empty($s['price'])): ?>
                            <p class="price-tag mb-3"><?= e($s['price']) ?></p>
                        <?php endif; ?>
                        <a href="repair/request.php?service=<?= urlencode($s['service_name']) ?>" class="btn btn-brand btn-sm w-100">Request Service</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($services)): ?>
                <p class="text-center text-muted">No services available at the moment. Please check back soon.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container text-center">
        <h3 class="fw-bold mb-2">Don't see your problem listed?</h3>
        <p class="text-muted mb-4">We fix all PC problems &mdash; from water damage to failed USB ports. Reach out and describe your issue.</p>
        <a href="repair/request.php" class="btn btn-brand-lime btn-lg">Submit a Repair Request</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
