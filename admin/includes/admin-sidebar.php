<?php $current = basename($_SERVER['SCRIPT_NAME']); ?>
<div class="dash-sidebar">
    <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
    <a href="products.php" class="<?= in_array($current, ['products.php','product-form.php']) ? 'active' : '' ?>"><i class="fa-solid fa-laptop me-2"></i>Products</a>
    <a href="categories.php" class="<?= $current === 'categories.php' ? 'active' : '' ?>"><i class="fa-solid fa-tags me-2"></i>Categories</a>
    <a href="services.php" class="<?= in_array($current, ['services.php','service-form.php']) ? 'active' : '' ?>"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Services</a>
    <a href="customers.php" class="<?= $current === 'customers.php' ? 'active' : '' ?>"><i class="fa-solid fa-users me-2"></i>Customers</a>
    <a href="orders.php" class="<?= $current === 'orders.php' ? 'active' : '' ?>"><i class="fa-solid fa-bag-shopping me-2"></i>Orders</a>
    <a href="repairs.php" class="<?= $current === 'repairs.php' ? 'active' : '' ?>"><i class="fa-solid fa-toolbox me-2"></i>Repair Requests</a>
    <a href="messages.php" class="<?= $current === 'messages.php' ? 'active' : '' ?>"><i class="fa-solid fa-envelope me-2"></i>Messages</a>
    <a href="videos.php" class="<?= $current === 'videos.php' ? 'active' : '' ?>"><i class="fa-solid fa-video me-2"></i>Video Gallery</a>
    <a href="settings.php" class="<?= $current === 'settings.php' ? 'active' : '' ?>"><i class="fa-solid fa-gear me-2"></i>Website Settings</a>
</div>
