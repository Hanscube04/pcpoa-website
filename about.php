<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Us - PC Poa';
$settings = get_settings($pdo);
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-banner text-center">
    <div class="container">
        <h1 class="fw-bold">About PC Poa</h1>
        <p class="mb-0">Who we are, what we stand for, and why customers trust us.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="section-eyebrow">Who We Are</span>
                <h2 class="section-title mb-3">A professional computer &amp; laptop sales and repair center</h2>
                <p>We are a professional computer and laptop sales and repair center committed to providing reliable, affordable and fast technology solutions to our customers. PC Poa provides customers with quality computer products and professional technical services in Kariakoo, Dar es Salaam.</p>
                <p>Any brand, any make, any issue &mdash; relax, we've got you covered. We are committed to restoring your favorite laptop back to life and are your destination for fast, reliable repair services.</p>
                <div class="location-chip"><i class="fa-solid fa-location-dot"></i> <?= e($settings['address'] ?? '') ?></div>
            </div>
            <div class="col-lg-6">
                <img src="images/who-we-are.jpeg" class="img-fluid rounded-4 shadow-sm" alt="Who We Are">
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-pcpoa p-4 h-100">
                    <i class="fa-solid fa-bullseye fs-2 mb-3" style="color:var(--pcpoa-green);"></i>
                    <h5 class="fw-bold">Our Mission</h5>
                    <p class="text-muted mb-0">To provide reliable, affordable and fast computer sales, repair and consultancy services to individuals, companies and organizations across Tanzania.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-pcpoa p-4 h-100">
                    <i class="fa-solid fa-eye fs-2 mb-3" style="color:var(--pcpoa-green);"></i>
                    <h5 class="fw-bold">Our Vision</h5>
                    <p class="text-muted mb-0">To be the most trusted computer sales and repair brand in East Africa, known for quality, speed and honesty.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-pcpoa p-4 h-100">
                    <i class="fa-solid fa-heart fs-2 mb-3" style="color:var(--pcpoa-green);"></i>
                    <h5 class="fw-bold">Our Core Values</h5>
                    <p class="text-muted mb-0">Integrity, fast service, technical excellence, and genuine care for every customer that walks through our door.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-lg-2">
                <img src="images/consultancy.jpeg" class="img-fluid rounded-4 shadow-sm" alt="Computer Consultancy">
            </div>
            <div class="col-lg-6 order-lg-1">
                <span class="section-eyebrow">Computer Consultancy</span>
                <h2 class="section-title mb-3">We help you choose the right PC</h2>
                <p>We are professionals who help individuals, companies and organizations optimize their use of computer systems. We work with clients to assess their needs, recommend solutions, and provide support and guidance in implementing and maintaining those solutions.</p>
                <p>Tell us more about what you intend to do with a computer and we will recommend you the most suitable PC for you to purchase.</p>
                <a href="contact.php" class="btn btn-brand">Talk to Our Consultants</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container text-center">
        <span class="section-eyebrow">Since 2013</span>
        <h2 class="section-title mb-3">Experience you can trust</h2>
        <p class="section-subtitle mx-auto mb-4">Mafundi wabobezi wa computer Tanzania tangu mwaka 2013. Problem solving has been our foundation essential skill since 2016.</p>
        <div class="row justify-content-center g-4">
            <div class="col-md-3 col-6">
                <h2 class="fw-bold" style="color:var(--pcpoa-green);">10+</h2>
                <p class="text-muted mb-0">Years of Experience</p>
            </div>
            <div class="col-md-3 col-6">
                <h2 class="fw-bold" style="color:var(--pcpoa-green);">1000+</h2>
                <p class="text-muted mb-0">Devices Repaired</p>
            </div>
            <div class="col-md-3 col-6">
                <h2 class="fw-bold" style="color:var(--pcpoa-green);">All Brands</h2>
                <p class="text-muted mb-0">Laptops &amp; Desktops</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
