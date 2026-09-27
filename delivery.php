<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Delivery - PC Poa';
$settings = get_settings($pdo);
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">Delivery Information</h1>
        <p class="mb-0">How PC Poa gets your order to you.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-7">
                <h4 class="fw-bold mb-4">Our Delivery Process</h4>
                <div class="d-flex flex-column gap-3">
                    <?php
                    $steps = [
                        'Customer selects a product' => 'Browse the shop and choose the product you need.',
                        'Customer places an order' => 'Add to cart and check out with your delivery address.',
                        'PC Poa confirms the order' => 'Our team calls or WhatsApps you to confirm details and payment.',
                        'Product is prepared' => 'We package your product carefully for transport.',
                        'Product is delivered' => 'Our rider or courier partner delivers to your address.',
                        'Customer receives the product' => 'You inspect and confirm receipt of your order.',
                    ];
                    $i = 1;
                    foreach ($steps as $title => $desc): ?>
                    <div class="d-flex gap-3">
                        <div class="service-icon-badge flex-shrink-0"><?= $i ?></div>
                        <div>
                            <h6 class="fw-bold mb-1"><?= e($title) ?></h6>
                            <p class="text-muted mb-0 small"><?= e($desc) ?></p>
                        </div>
                    </div>
                    <?php $i++; endforeach; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-pcpoa p-4">
                    <h5 class="fw-bold mb-3">Delivery Details</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0"><strong>Delivery Areas:</strong> Dar es Salaam and upcountry via courier partners.</li>
                        <li class="list-group-item px-0"><strong>Delivery Charges:</strong> Calculated based on location; confirmed by our team at order confirmation.</li>
                        <li class="list-group-item px-0"><strong>Expected Time:</strong> Same-day within Dar es Salaam city, 2-4 days upcountry.</li>
                        <li class="list-group-item px-0"><strong>Payment Methods:</strong> Cash on collection, bank transfer, mobile money.</li>
                        <li class="list-group-item px-0"><strong>Delivery Contact:</strong> <?= e($settings['phone_primary'] ?? '') ?></li>
                    </ul>
                    <a href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" class="btn btn-brand mt-3">Ask About Delivery on WhatsApp</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
