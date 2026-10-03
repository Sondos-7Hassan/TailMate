<?php require_once __DIR__ . '/../config/config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Adopt - TailMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #FFF6EC;
            color: #5C3A21;
            font-family: Arial, sans-serif;
        }
        .howto-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }
        .howto-card {
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
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .section-title i {
            color: #E7A769;
            font-size: 2.5rem;
        }
        .step-box {
            background: #FFF6EC;
            border-left: 5px solid #E7A769;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        .step-box:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(231, 167, 105, 0.2);
        }
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #E7A769, #d49659);
            color: white;
            border-radius: 50%;
            font-size: 1.5rem;
            font-weight: bold;
            margin-right: 20px;
            float: left;
        }
        .step-box h3 {
            color: #5C3A21;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .step-box p {
            margin-bottom: 0;
            line-height: 1.8;
        }
        .tips-box {
            background: linear-gradient(135deg, #E7A769, #d49659);
            color: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .tips-box h3 {
            color: white;
            margin-bottom: 20px;
        }
        .tips-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .tips-box li {
            margin-bottom: 10px;
            line-height: 1.6;
        }
        .btn-custom {
            background: linear-gradient(135deg, #E7A769, #d49659);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(231, 167, 105, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>

    <div class="howto-container">
        <div class="howto-card">
            <h1 class="section-title">
                <i class="bi bi-info-square"></i> How to Adopt
            </h1>
            <p class="lead mb-4">
                Adopting a pet is a wonderful and rewarding experience! Follow these simple steps to find your perfect companion 
                and give them the loving home they deserve.
            </p>

            <!-- Step 1 -->
            <div class="step-box">
                <span class="step-number">1</span>
                <div>
                    <h3><i class="bi bi-search"></i> Browse Available Pets</h3>
                    <p>
                        Explore our collection of cats and dogs waiting for their forever homes. Use our filters to find pets 
                        based on age, size, gender, and location. Each pet profile includes photos, personality traits, and health information.
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- Step 2 -->
            <div class="step-box">
                <span class="step-number">2</span>
                <div>
                    <h3><i class="bi bi-clipboard2-heart"></i> Complete the Adoption Application</h3>
                    <p>
                        Once you've found a pet you'd like to adopt, click on "Apply to Adopt" and fill out our adoption form. 
                        We'll ask about your living situation, experience with pets, and why you'd like to adopt. This helps us 
                        ensure the best match between you and your future pet.
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- Step 3 -->
            <div class="step-box">
                <span class="step-number">3</span>
                <div>
                    <h3><i class="bi bi-chat-dots"></i> Application Review</h3>
                    <p>
                        Our team will review your application within 2-3 business days. We may contact you for additional 
                        information or to schedule a phone interview. We want to make sure this is the right fit for both you and the pet.
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- Step 4 -->
            <div class="step-box">
                <span class="step-number">4</span>
                <div>
                    <h3><i class="bi bi-house-check"></i> Home Visit (If Required)</h3>
                    <p>
                        For certain pets, we may arrange a home visit to ensure your living environment is suitable. This is a 
                        friendly check to make sure your home is safe and ready for your new family member.
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- Step 5 -->
            <div class="step-box">
                <span class="step-number">5</span>
                <div>
                    <h3><i class="bi bi-people"></i> Meet & Greet</h3>
                    <p>
                        If your application is approved, we'll schedule a meeting with the pet. This gives you a chance to interact 
                        and make sure the connection is there. Bring your family members so everyone can meet their potential new friend!
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- Step 6 -->
            <div class="step-box">
                <span class="step-number">6</span>
                <div>
                    <h3><i class="bi bi-heart-fill"></i> Finalize Adoption</h3>
                    <p>
                        Once approved, you'll sign the adoption agreement and pay the adoption fee (which covers vaccinations, 
                        spaying/neutering, and medical care). Then it's time to take your new best friend home!
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- Step 7 -->
            <div class="step-box">
                <span class="step-number">7</span>
                <div>
                    <h3><i class="bi bi-house-heart"></i> Welcome Home!</h3>
                    <p>
                        Congratulations! Give your new pet time to adjust to their new environment. We're here to support you 
                        through the transition and beyond. Feel free to reach out if you have any questions or concerns.
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <!-- Tips Section -->
        <div class="tips-box">
            <h3><i class="bi bi-lightbulb"></i> Preparation Tips</h3>
            <ul>
                <li><strong>Pet-proof your home:</strong> Remove hazards, secure trash cans, and create a safe space for your new pet.</li>
                <li><strong>Get supplies ready:</strong> Food, water bowls, bed, toys, collar, leash, and litter box (for cats).</li>
                <li><strong>Find a veterinarian:</strong> Schedule a checkup within the first week of adoption.</li>
                <li><strong>Be patient:</strong> It may take time for your new pet to adjust. Give them love and understanding.</li>
                <li><strong>Establish routine:</strong> Pets thrive on consistency. Set regular feeding, walking, and playtime schedules.</li>
                <li><strong>Budget accordingly:</strong> Consider ongoing costs like food, medical care, grooming, and supplies.</li>
            </ul>
        </div>

        <!-- Call to Action -->
        <div class="howto-card text-center">
            <h2 style="color: #5C3A21; margin-bottom: 20px;">Ready to Start Your Adoption Journey?</h2>
            <p class="lead mb-4">Browse our available pets and find your perfect match today!</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="<?= BASE_URL ?>/pages/dogs.php" class="btn btn-custom btn-lg">
                    <i class="bi bi-🐕"></i> View Dogs
                </a>
                <a href="<?= BASE_URL ?>/pages/cats.php" class="btn btn-custom btn-lg">
                    <i class="bi bi-🐈"></i> View Cats
                </a>
                <a href="<?= BASE_URL ?>/pages/contact.php" class="btn btn-lg btn-outline-secondary">
                    <i class="bi bi-question-circle"></i> Have Questions?
                </a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
