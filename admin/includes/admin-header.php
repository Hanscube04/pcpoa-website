<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();
$settings = get_settings($pdo);
$adminPageTitle = $adminPageTitle ?? 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($adminPageTitle) ?> - PC Poa Admin</title>
<link rel="icon" href="<?= BASE_URL . ($settings['logo'] ?? 'uploads/logo/logo.jpeg') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
<style>
    body { background: #f4f7f5; }
    .admin-topbar { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.06); padding: 14px 0; }
    .admin-logo { height: 42px; }
</style>
</head>
<body>

<div class="admin-topbar">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="d-flex align-items-center gap-2 text-decoration-none">
            <img src="<?= BASE_URL . ($settings['logo'] ?? 'uploads/logo/logo.jpeg') ?>" class="admin-logo" alt="PC Poa Logo">
            <span class="fw-bold text-dark">PC Poa Admin</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="<?= BASE_URL ?>index.php" target="_blank" class="btn btn-brand-outline btn-sm"><i class="fa-solid fa-globe"></i> View Site</a>
            <span class="fw-semibold"><i class="fa-solid fa-user-shield me-1"></i><?= e($_SESSION['user_name'] ?? 'Admin') ?></span>
            <a href="<?= BASE_URL ?>logout.php" class="btn btn-brand btn-sm">Logout</a>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        <div class="col-lg-2">
            <?php include __DIR__ . '/admin-sidebar.php'; ?>
        </div>
        <div class="col-lg-10">
