<?php $current = basename($_SERVER['SCRIPT_NAME']); ?>
<div class="dash-sidebar">
    <div class="text-center mb-3 px-3">
        <i class="fa-solid fa-circle-user fs-1"></i>
        <p class="mb-0 fw-bold"><?= e($_SESSION['user_name'] ?? 'Customer') ?></p>
        <small class="text-light-50">Customer Account</small>
    </div>
    <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
    <a href="profile.php" class="<?= $current === 'profile.php' ? 'active' : '' ?>"><i class="fa-solid fa-user me-2"></i>My Profile</a>
    <a href="orders.php" class="<?= $current === 'orders.php' ? 'active' : '' ?>"><i class="fa-solid fa-bag-shopping me-2"></i>My Orders</a>
    <a href="repairs.php" class="<?= $current === 'repairs.php' ? 'active' : '' ?>"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Repair Requests</a>
    <a href="../contact.php"><i class="fa-solid fa-headset me-2"></i>Contact PC Poa</a>
    <a href="../logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
</div>
