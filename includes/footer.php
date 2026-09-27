<?php $settings = get_settings($pdo); ?>
<!-- Footer -->
<footer class="site-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <img src="<?= BASE_URL . ($settings['logo'] ?? 'uploads/logo/logo.jpeg') ?>" alt="PC Poa Logo" class="footer-logo mb-3">
                <p class="text-light-50"><?= e($settings['slogan'] ?? 'Consult with us, buy with us, repair with us.') ?></p>
                <div class="d-flex gap-3 fs-4">
                    <a href="https://instagram.com/<?= e($settings['instagram'] ?? 'pcpoa_official') ?>" target="_blank" class="text-white"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://tiktok.com/@<?= e($settings['tiktok'] ?? 'pcpoa') ?>" target="_blank" class="text-white"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" class="text-white"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="footer-heading">Quick Links</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= BASE_URL ?>index.php">Home</a></li>
                    <li><a href="<?= BASE_URL ?>about.php">About Us</a></li>
                    <li><a href="<?= BASE_URL ?>services.php">Services</a></li>
                    <li><a href="<?= BASE_URL ?>shop.php">Shop</a></li>
                    <li><a href="<?= BASE_URL ?>gallery/index.php">Gallery</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="footer-heading">Account</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= BASE_URL ?>login.php">Login</a></li>
                    <li><a href="<?= BASE_URL ?>register.php">Register</a></li>
                    <li><a href="<?= BASE_URL ?>repair/request.php">Request Repair</a></li>
                    <li><a href="<?= BASE_URL ?>contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="footer-heading">Visit Our Office</h6>
                <p class="mb-1"><i class="fa-solid fa-location-dot me-2"></i><?= e($settings['address'] ?? '') ?></p>
                <p class="mb-1"><i class="fa-solid fa-phone me-2"></i><?= e($settings['phone_primary'] ?? '') ?> / <?= e($settings['phone_secondary'] ?? '') ?></p>
                <p class="mb-1"><i class="fa-solid fa-envelope me-2"></i><?= e($settings['email'] ?? '') ?></p>
                <p class="mb-0"><i class="fa-solid fa-clock me-2"></i><?= e($settings['working_hours'] ?? '') ?></p>
            </div>
        </div>
        <hr class="border-light-50 mt-4">
        <p class="text-center mb-0 small">&copy; <?= date('Y') ?> PC Poa. All rights reserved. | Mafundi wabobezi wa computer Tanzania tangu mwaka 2013.</p>
    </div>
</footer>

<a href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" class="whatsapp-float" title="Chat with us on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>js/script.js"></script>
</body>
</html>
