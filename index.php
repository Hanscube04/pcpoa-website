<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'PC Poa - Computer Sales & Laptop Repair in Dar es Salaam';
$settings = get_settings($pdo);

$featuredProducts = $pdo->query("SELECT * FROM products WHERE status='active' ORDER BY created_at DESC LIMIT 4")->fetchAll();
$featuredServices = $pdo->query("SELECT * FROM services WHERE status='active' ORDER BY created_at DESC LIMIT 4")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="hero-badge"><i class="fa-solid fa-award me-1"></i> Trusted computer experts since 2013</span>
                <h1>Consult with us, buy with us, <span style="color:#8fd400;">repair with us.</span></h1>
                <p class="lead mb-4">PC Poa is a professional computer and laptop sales &amp; repair center in Kariakoo, Dar es Salaam. Any brand, any make, any issue &mdash; relax, we've got you covered.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="shop.php" class="btn btn-brand-lime btn-lg"><i class="fa-solid fa-cart-shopping me-2"></i>Shop Now</a>
                    <a href="repair/request.php" class="btn btn-outline-light btn-lg"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Request Repair</a>
                </div>
                <div class="location-chip mt-4">
                    <i class="fa-solid fa-location-dot"></i> <?= e($settings['address'] ?? '') ?>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-5 mt-lg-0">
                <img src="images/hero-macbook.jpg" alt="Laptop repair at PC Poa" class="img-fluid rounded-4 shadow">
            </div>
        </div>
    </div>
</section>

<!-- WHO WE ARE -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="images/who-we-are.jpeg" alt="Who We Are - PC Poa" class="img-fluid rounded-4 shadow-sm">
            </div>
            <div class="col-lg-6">
                <span class="section-eyebrow">Who We Are</span>
                <h2 class="section-title mb-3">Your preferred laptop repair center</h2>
                <p class="section-subtitle">We are the best and your preferred laptop repair center. Any brand, any make, any issue &mdash; relax, we've got you covered. PC Poa is a professional repair group in Kariakoo, Dar es Salaam, committed to restoring your favorite laptop back to life. We are your destination for fast, reliable repair services.</p>
                <a href="about.php" class="btn btn-brand mt-2">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-eyebrow">Why People Trust Us</span>
            <h2 class="section-title">Why Choose PC Poa</h2>
            <p class="section-subtitle mx-auto">Simply because we have an open record of solving most complex technical issues on time. We quickly and effectively troubleshoot your computer issues, because problem solving has been our foundation since 2016.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="why-item flex-column text-center">
                    <i class="fa-solid fa-bolt fs-4"></i>
                    <h6 class="mt-2 mb-1">Fast Turnaround</h6>
                    <p class="small text-muted mb-0">Quick and effective diagnosis and repair.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="why-item flex-column text-center">
                    <i class="fa-solid fa-shield-halved fs-4"></i>
                    <h6 class="mt-2 mb-1">Trusted Experts</h6>
                    <p class="small text-muted mb-0">Mafundi wabobezi wa computer since 2013.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="why-item flex-column text-center">
                    <i class="fa-solid fa-tags fs-4"></i>
                    <h6 class="mt-2 mb-1">Fair Pricing</h6>
                    <p class="small text-muted mb-0">Affordable, transparent service pricing.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="why-item flex-column text-center">
                    <i class="fa-solid fa-comments fs-4"></i>
                    <h6 class="mt-2 mb-1">Free Consultancy</h6>
                    <p class="small text-muted mb-0">We recommend the best PC for your needs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED SERVICES -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">What We Fix</span>
                <h2 class="section-title mb-0">Featured Services</h2>
            </div>
            <a href="services.php" class="btn btn-brand-outline">View All Services</a>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredServices as $s): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card card-pcpoa">
                    <img src="<?= e($s['image'] ?: 'images/what-we-fix.jpeg') ?>" class="card-img-top" alt="<?= e($s['service_name']) ?>">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2"><?= e($s['service_name']) ?></h6>
                        <p class="small text-muted mb-3"><?= e(mb_strimwidth($s['description'] ?? '', 0, 90, '...')) ?></p>
                        <a href="services.php" class="btn btn-brand btn-sm w-100">Request Service</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FEATURED PRODUCTS -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Shop</span>
                <h2 class="section-title mb-0">Featured Products</h2>
            </div>
            <a href="shop.php" class="btn btn-brand-outline">View All Products</a>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredProducts as $p): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card card-pcpoa">
                    <img src="<?= e($p['image'] ?: 'images/hero-macbook.jpg') ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
                    <div class="card-body">
                        <span class="badge <?= $p['stock'] > 0 ? 'badge-instock' : 'badge-outofstock' ?> mb-2"><?= $p['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?></span>
                        <h6 class="fw-bold mb-1"><?= e($p['name']) ?></h6>
                        <p class="small text-muted mb-2"><?= e($p['brand']) ?></p>
                        <p class="price-tag mb-3"><?= money($p['price']) ?></p>
                        <a href="products/product-details.php?id=<?= (int)$p['id'] ?>" class="btn btn-brand btn-sm w-100">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5">
    <div class="container">
        <div class="p-5 rounded-4 text-center text-white" style="background:linear-gradient(135deg, var(--pcpoa-dark-green), var(--pcpoa-green));">
            <h3 class="fw-bold mb-2">Got a computer problem? Let's fix it today.</h3>
            <p class="mb-4">Visit us at Agrey Street, Kariakoo, near Mkombozi Bank &mdash; or reach us on WhatsApp.</p>
            <a href="repair/request.php" class="btn btn-brand-lime btn-lg">Submit a Repair Request</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
