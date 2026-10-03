<?php require_once __DIR__ . '/../config/config.php'; ?><!-- User Navbar -->
<nav class="user-navbar">
    <div class="nav-left">
        <a href="<?= BASE_URL ?>/pages/home.php" class="logo">
            <img src="<?= BASE_URL ?>/assets/images/logo.jpg" alt="TailMate" style="height: 40px;">
        </a>
        <a href="<?= BASE_URL ?>/pages/home.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">Home</a>
        <a href="<?= BASE_URL ?>/pages/about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About</a>
        <a href="<?= BASE_URL ?>/pages/cats.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cats.php' ? 'active' : ''; ?>">Cats</a>
        <a href="<?= BASE_URL ?>/pages/dogs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dogs.php' ? 'active' : ''; ?>">Dogs</a>
        <a href="<?= BASE_URL ?>/pages/howto.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'howto.php' ? 'active' : ''; ?>">How to Adopt</a>
        <a href="<?= BASE_URL ?>/pages/contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a>
    </div>
    <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE_URL ?>/pages/profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="bi bi-person-circle"></i> My Profile
            </a>
            <a href="<?= BASE_URL ?>/pages/notifications.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">
                <i class="bi bi-bell"></i> Messages
            </a>
            <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <a href="<?= BASE_URL ?>/admin/dashboard.php" style="background-color: #E7A769; color: white; padding: 8px 15px; border-radius: 5px;">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/auth/logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/auth/login.php" class="login-btn">Login</a>
            <a href="<?= BASE_URL ?>/auth/register.php" class="register-btn">Register</a>
        <?php endif; ?>
    </div>
</nav>

<style>
    .user-navbar {
        background-color: #5C3A21;
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .user-navbar .nav-left,
    .user-navbar .nav-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .user-navbar a {
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    .user-navbar a:hover {
        background-color: #E7A769;
        color: #5C3A21;
    }
    .user-navbar a.active {
        background-color: #E7A769;
        color: #5C3A21;
    }
    .user-navbar .logout-btn {
        background-color: #dc3545;
        color: white;
    }
    .user-navbar .logout-btn:hover {
        background-color: #c82333;
        color: white;
    }
    .user-navbar .login-btn,
    .user-navbar .register-btn {
        background-color: #E7A769;
        color: #5C3A21;
    }
    .user-navbar .logo {
        padding: 0;
        margin-right: 20px;
    }
    .user-navbar .logo:hover {
        background: none;
    }
</style>
