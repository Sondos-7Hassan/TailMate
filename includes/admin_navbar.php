<?php require_once __DIR__ . '/../config/config.php'; ?><!-- Admin Navbar -->
<div class="navbar">
    <span>Welcome, Admin</span>
    <a href="<?= BASE_URL ?>/auth/logout.php" class="btn btn-sm" style="background-color: #E7A769; color: #5C3A21;">Logout</a>
</div>

<style>
    .navbar {
        background-color: #FFF6EC;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #E7A769;
        margin-bottom: 30px;
    }
    .navbar span {
        font-weight: bold;
        color: #5C3A21;
        font-size: 18px;
    }
</style>
