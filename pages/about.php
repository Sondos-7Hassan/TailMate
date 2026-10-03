<?php require_once __DIR__ . '/../config/config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TailMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #FFF6EC;
            color: #5C3A21;
            font-family: Arial, sans-serif;
        }
        .about-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .about-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(92, 58, 33, 0.1);
            margin-bottom: 30px;
        }
        .section-title {
            color: #5C3A21;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .section-title i {
            color: #E7A769;
            font-size: 2.5rem;
        }
        .feature-box {
            background: #FFF6EC;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            border: 2px solid transparent;
        }
        .feature-box:hover {
            transform: translateY(-5px);
            border-color: #E7A769;
            box-shadow: 0 8px 15px rgba(231, 167, 105, 0.2);
        }
        .feature-box i {
            font-size: 3rem;
            color: #E7A769;
            margin-bottom: 15px;
        }
        .feature-box h4 {
            color: #5C3A21;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .stats-box {
            background: linear-gradient(135deg, #E7A769, #d49659);
            color: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
        }
        .stats-box h2 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stats-box p {
            font-size: 1.1rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>

    <div class="about-container">
        <!-- Hero Section -->
        <div class="about-card">
            <h1 class="section-title">
                <i class="bi bi-info-circle"></i> About TailMate
            </h1>
            <p class="lead">
                TailMate is a dedicated animal adoption platform committed to finding loving homes for dogs and cats in need. 
                We believe every pet deserves a second chance at happiness, and we're here to make that happen.
            </p>
            <p>
                Founded with a passion for animal welfare, TailMate connects compassionate individuals with pets looking for their forever homes. 
                Our mission is to reduce the number of homeless animals and promote responsible pet ownership throughout our community.
            </p>
        </div>

        <!-- Mission & Vision -->
        <div class="row">
            <div class="col-md-6">
                <div class="about-card">
                    <h3 class="section-title">
                        <i class="bi bi-bullseye"></i> Our Mission
                    </h3>
                    <p>
                        To rescue, rehabilitate, and rehome abandoned and neglected animals, providing them with the care, 
                        love, and attention they deserve while educating the community about responsible pet ownership.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="about-card">
                    <h3 class="section-title">
                        <i class="bi bi-eye"></i> Our Vision
                    </h3>
                    <p>
                        A world where every pet has a loving home, and no animal is left behind. We envision communities 
                        where animals are treated with compassion and respect, and where adoption is always the first choice.
                    </p>
                </div>
            </div>
        </div>

        <!-- What We Offer -->
        <div class="about-card">
            <h2 class="section-title">
                <i class="bi bi-stars"></i> What We Offer
            </h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="bi bi-heart-pulse"></i>
                        <h4>Health Screening</h4>
                        <p>All our animals receive complete veterinary checkups, vaccinations, and necessary medical care before adoption.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="bi bi-clipboard-check"></i>
                        <h4>Easy Adoption Process</h4>
                        <p>We've streamlined the adoption process to make it simple and transparent for potential pet parents.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="bi bi-chat-heart"></i>
                        <h4>Post-Adoption Support</h4>
                        <p>Our support doesn't end at adoption. We provide guidance and resources to help you and your new pet adjust.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="about-card">
            <h2 class="section-title">
                <i class="bi bi-graph-up"></i> Our Impact
            </h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-box mb-3">
                        <h2>500+</h2>
                        <p>Animals Rescued</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-box mb-3">
                        <h2>350+</h2>
                        <p>Successful Adoptions</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-box mb-3">
                        <h2>98%</h2>
                        <p>Happy Families</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="about-card text-center">
            <h2 style="color: #5C3A21; margin-bottom: 20px;">Ready to Make a Difference?</h2>
            <p class="lead mb-4">Join our mission to give every animal a loving home. Browse our available pets or get in touch to learn how you can help.</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="<?= BASE_URL ?>/pages/cats.php" class="btn btn-lg" style="background: #E7A769; color: white;">
                    <i class="bi bi-search"></i> Browse Pets
                </a>
                <a href="<?= BASE_URL ?>/pages/contact.php" class="btn btn-lg btn-outline-secondary">
                    <i class="bi bi-envelope"></i> Contact Us
                </a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
