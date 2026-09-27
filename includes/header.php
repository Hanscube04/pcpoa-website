<?php
require_once __DIR__ . '/functions.php';
$settings = get_settings($pdo);
$logoPath = BASE_URL . ($settings['logo'] ?? 'uploads/logo/logo.jpeg');
$pageTitle = $pageTitle ?? 'PC Poa - Computer Sales & Repair';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="PC Poa - Professional computer and laptop sales, repair and consultancy in Kariakoo, Dar es Salaam.">
<link rel="icon" href="<?= $logoPath ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>

<!-- Top info bar: office location tag shown on every page -->
<div class="top-info-bar">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <div class="top-info-item">
            <i class="fa-solid fa-location-dot"></i>
            <span><?= e($settings['address'] ?? 'Agrey Street, Kariakoo, near Mkombozi Bank, Dar es Salaam') ?></span>
        </div>
        <div class="d-flex flex-wrap gap-3">
            <a href="tel:<?= e($settings['phone_primary'] ?? '') ?>" class="top-info-item text-decoration-none">
                <i class="fa-solid fa-phone"></i><span><?= e($settings['phone_primary'] ?? '') ?></span>
            </a>
            <a href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" class="top-info-item text-decoration-none">
                <i class="fa-brands fa-whatsapp"></i><span>WhatsApp</span>
            </a>
            <a href="https://instagram.com/<?= e($settings['instagram'] ?? 'pcpoa_official') ?>" target="_blank" class="top-info-item text-decoration-none">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="https://tiktok.com/@<?= e($settings['tiktok'] ?? 'pcpoa') ?>" target="_blank" class="top-info-item text-decoration-none">
                <i class="fa-brands fa-tiktok"></i>
            </a>
        </div>
    </div>
</div>

<!-- Main navigation -->
<nav class="navbar navbar-expand-lg main-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>index.php">
            <img src="<?= $logoPath ?>" alt="<?= e($settings['site_name'] ?? 'PC Poa') ?> Logo" class="site-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>gallery/index.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>delivery.php">Delivery</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>contact.php">Contact</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <li class="nav-item"><a class="btn btn-brand-outline btn-sm ms-lg-2" href="<?= BASE_URL ?>admin/dashboard.php"><i class="fa-solid fa-gauge"></i> Admin</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-brand-outline btn-sm ms-lg-2" href="<?= BASE_URL ?>customer/dashboard.php"><i class="fa-solid fa-user"></i> My Account</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="btn btn-brand btn-sm ms-lg-2" href="<?= BASE_URL ?>logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-brand-outline btn-sm ms-lg-2" href="<?= BASE_URL ?>login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-brand btn-sm ms-lg-2" href="<?= BASE_URL ?>register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
