<?php require_once __DIR__ . '/../config/config.php'; ?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Footer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        footer {
            background-color: #E7A769;
            padding: 40px 20px 20px;
            margin-top: 50px;
            color: #5C3A21;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto 30px;
            gap: 30px;
        }

        .footer-column {
            flex: 1 1 200px;
            min-width: 150px;
        }

        .footer-column h4 {
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #5C3A21;
            border-bottom: 2px solid #5C3A21;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-column h4 i {
            font-size: 20px;
        }

        .footer-column a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #5C3A21;
            text-decoration: none;
            margin: 8px 0;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .footer-column a i {
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .footer-column a:hover {
            color: #3e2414;
            padding-left: 5px;
        }

        .footer-column a:hover i {
            transform: translateX(3px);
        }

        .footer-logo {
            flex: 1 1 200px;
            text-align: center;
        }

        .footer-logo img {
            height: 80px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .footer-logo h3 {
            margin: 10px 0 5px;
            font-size: 24px;
            color: #5C3A21;
            font-weight: bold;
        }

        .footer-logo p {
            font-size: 14px;
            color: #5C3A21;
            margin: 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
            padding: 20px 0;
            border-top: 1px solid #5C3A21;
        }

        .social-icons a {
            text-decoration: none;
            color: #5C3A21;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .social-icons a:hover {
            transform: translateY(-5px);
            color: #3e2414;
        }

        .social-icons a i {
            font-size: 32px;
            transition: all 0.3s ease;
        }

        .social-icons a:hover i {
            transform: scale(1.15);
        }

        .social-icons p {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #5C3A21;
            font-size: 14px;
            color: #5C3A21;
        }
    </style>
</head>

<body>

    <footer>
        <div class="footer-content">

            <div class="footer-column">
                <h4><i class="bi bi-info-circle"></i> About</h4>
                <a href="<?= BASE_URL ?>/pages/home.php"><i class="bi bi-house-door"></i> Home</a>
                <a href="<?= BASE_URL ?>/pages/about.php"><i class="bi bi-people"></i> About Us</a>
                <a href="<?= BASE_URL ?>/pages/howto.php"><i class="bi bi-info-square"></i> How to Adopt</a>
            </div>

            <div class="footer-column">
                <h4><i class="bi bi-heart"></i> Find Pets</h4>
                <a href="<?= BASE_URL ?>/pages/dogs.php"><i class="bi bi-🐕"></i> Find a Dog</a>
                <a href="<?= BASE_URL ?>/pages/cats.php"><i class="bi bi-🐈"></i> Find a Cat</a>
                <a href="<?= BASE_URL ?>/pages/adoptionform.php"><i class="bi bi-clipboard-check"></i> Adoption Form</a>
            </div>

            <div class="footer-column">
                <h4><i class="bi bi-headset"></i> Support</h4>
                <a href="<?= BASE_URL ?>/pages/contact.php"><i class="bi bi-envelope"></i> Contact Us</a>
            </div>

            <div class="footer-logo">
                <img src="<?= BASE_URL ?>/assets/images/logo.jpg" alt="TailMate Logo">
                <h3>TailMate</h3>
                <p>Give Them a Second Chance</p>
            </div>

        </div>

        <!-- Social Icons Section -->
        <div class="social-icons">
            <a href="#" target="_blank">
                <i class="bi bi-facebook"></i>
                <p>Facebook</p>
            </a>
            <a href="#" target="_blank">
                <i class="bi bi-instagram"></i>
                <p>Instagram</p>
            </a>
            <a href="#" target="_blank">
                <i class="bi bi-twitter-x"></i>
                <p>Twitter</p>
            </a>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 TailMate. All rights reserved. Made with ❤️ for animals.</p>
        </div>
    </footer>

</body>

</html>