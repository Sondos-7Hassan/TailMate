<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

$successMessage = '';
$errorMessage = '';

// معالجة إرسال رسالة الاتصال
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $errorMessage = "Name, email, and message are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } else {
        $insert = $connect->prepare("INSERT INTO contact_messages (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($insert->execute([$name, $email, $phone, $message])) {
            $successMessage = "Thank you! Your message has been sent successfully. We'll get back to you soon.";
            // إعادة تعيين الحقول
            $_POST = array();
        } else {
            $errorMessage = "Error sending message. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TailMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #FFF6EC;
            color: #5C3A21;
            font-family: Arial, sans-serif;
        }
        .contact-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }
        .contact-card {
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
        .info-box {
            background: #FFF6EC;
            border-left: 4px solid #E7A769;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-box h5 {
            color: #5C3A21;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-box h5 i {
            color: #E7A769;
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
        .form-control:focus {
            border-color: #E7A769;
            box-shadow: 0 0 0 0.2rem rgba(231, 167, 105, 0.25);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>

    <div class="contact-container">
        <div class="contact-card">
            <h1 class="section-title">
                <i class="bi bi-envelope-heart"></i> Contact Us
            </h1>
            <p class="lead mb-4">We'd love to hear from you! Whether you have questions about adoption, want to volunteer, or just want to say hello, feel free to reach out.</p>

            <?php if ($successMessage): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?php echo $successMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $errorMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <h3 class="mb-4" style="color: #5C3A21;">Send us a Message</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label"><strong>Your Name</strong></label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Your Email</strong></label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Phone Number (Optional)</strong></label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Your Message</strong></label>
                            <textarea name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-custom">
                            <i class="bi bi-send"></i> Send Message
                        </button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h3 class="mb-4" style="color: #5C3A21;">Get in Touch</h3>
                    
                    <div class="info-box">
                        <h5><i class="bi bi-geo-alt"></i> Our Location</h5>
                        <p class="mb-0">123 Pet Street, Animal City<br>Cairo, Egypt</p>
                    </div>

                    <div class="info-box">
                        <h5><i class="bi bi-telephone"></i> Phone</h5>
                        <p class="mb-0">+20 123 456 7890</p>
                    </div>

                    <div class="info-box">
                        <h5><i class="bi bi-envelope"></i> Email</h5>
                        <p class="mb-0">info@tailmate.com<br>support@tailmate.com</p>
                    </div>

                    <div class="info-box">
                        <h5><i class="bi bi-clock"></i> Working Hours</h5>
                        <p class="mb-0">
                            Monday - Friday: 9:00 AM - 6:00 PM<br>
                            Saturday: 10:00 AM - 4:00 PM<br>
                            Sunday: Closed
                        </p>
                    </div>

                    <div class="mt-4">
                        <h5 style="color: #5C3A21; margin-bottom: 15px;">Follow Us</h5>
                        <div class="d-flex gap-3">
                            <a href="#" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
