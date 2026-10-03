<?php require_once __DIR__ . '/../config/config.php'; ?><!-- Admin Sidebar -->
<div class="sidebar">
    <h2>Dashboard</h2>
    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <i class="bi bi-speedometer2"></i> Overview
    </a>
    <a href="<?= BASE_URL ?>/admin/users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
        <i class="bi bi-people"></i> Users
    </a>
    <a href="<?= BASE_URL ?>/admin/availablepets.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'availablepets.php' ? 'active' : ''; ?>">
        <i class="bi bi-collection"></i> Pets
    </a>
    <a href="<?= BASE_URL ?>/admin/requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'active' : ''; ?>">
        <i class="bi bi-file-earmark-text"></i> Requests
    </a>
    <a href="<?= BASE_URL ?>/admin/adoption_history.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'adoption_history.php' ? 'active' : ''; ?>">
        <i class="bi bi-heart-fill"></i> Adoption History
    </a>
    <a href="<?= BASE_URL ?>/admin/create_pet.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'create_pet.php' ? 'active' : ''; ?>">
        <i class="bi bi-plus-circle"></i> Add Pet
    </a>
    <a href="<?= BASE_URL ?>/pages/notifications.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">
        <i class="bi bi-chat-dots"></i> Messages
    </a>
    <a href="<?= BASE_URL ?>/auth/logout.php">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>

<style>
    .sidebar {
        width: 250px;
        height: 100vh;
        background-color: #5C3A21;
        color: white;
        padding: 15px;
        position: fixed;
        top: 0;
        left: 0;
        overflow-y: auto;
    }
    .sidebar h2 {
        color: #E7A769;
        margin-bottom: 30px;
        text-align: center;
        border-bottom: 2px solid #E7A769;
        padding-bottom: 15px;
    }
    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        margin: 10px 0;
        padding: 12px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .sidebar a:hover {
        background-color: #E7A769;
        color: #5C3A21;
        transform: translateX(5px);
    }
    .sidebar a.active {
        background-color: #E7A769;
        color: #5C3A21;
        font-weight: bold;
    }
    .sidebar a i {
        margin-right: 10px;
        width: 20px;
        display: inline-block;
    }
</style>
