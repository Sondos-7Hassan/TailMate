<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// لو المستخدم داخل بالفعل
if (isset($_SESSION['user_id'])) {
    if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
        header("Location: " . BASE_URL . "/admin/dashboard.php");
    } else {
        header("Location: " . BASE_URL . "/pages/home.php");
    }
    exit;
}

$prefilledEmail = $_GET['email'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['pass'];

    // نجيب بيانات المستخدم بناءً على الإيميل فقط
    $stmt = $connect->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // نتحقق من كلمة السر
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['username']    = $user['name'];
        $_SESSION['profile_pic'] = $user['image'] ?? 'default.png';
        $_SESSION['is_admin']    = $user['is_admin'];

        if ($user['is_admin'] == 1) {
            header("Location: " . BASE_URL . "/admin/dashboard.php");
        } else {
            header("Location: " . BASE_URL . "/pages/home.php");
        }
        exit;
    } else {
        $errors[] = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

body {
    margin:0;
    padding:0;
    font-family: 'Poppins', sans-serif;
    background: #FFF6EC; 
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.form-box {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(5px);
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0px 8px 24px rgba(92, 58, 33, 0.5); 
    color: #5C3A21; 
}

.form-box h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #5C3A21;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.form-box input[type="email"],
.form-box input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid rgba(92,58,33,0.4);
    border-radius: 8px;
    font-size: 16px;
    background: #fff;
    color: #5C3A21;
    outline: none;
    transition: 0.3s;
}

.form-box input::placeholder {
    color: rgba(92,58,33,0.6); 
}

.form-box input:focus {
    border-color: #E7A769; 
    box-shadow: 0px 0px 8px rgba(231,167,105,0.7);
    background: #FFF6EC;
}

.form-box button {
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #E7A769, #DFA569);
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.form-box button:hover {
    background: linear-gradient(135deg, #DFA569, #E7A769);
    transform: scale(1.02);
}

.error-box {
    background: rgba(220,38,38,0.8);
    padding:10px;
    border-radius:8px;
    margin-bottom:12px;
    text-align: center;
    color: #fff;
}

.register-link {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
    color: #5C3A21;
}

.register-link a {
    color: #E7A769; 
    font-weight: 600;
    text-decoration: none;
}

.register-link a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
  <div class="form-box">
    <h2>Login</h2>

    <?php if (!empty($errors)): ?>
      <div class="error-box">
        <?php foreach($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="" method="post">
      <input type="email" name="email" placeholder="Enter your email" 
             value="<?= htmlspecialchars($_POST['email'] ?? $prefilledEmail) ?>" required>
      <input type="password" name="pass" placeholder="Enter your password" required>
      <button type="submit">Login</button>
    </form>

    <div class="register-link">
        Don’t have an account? <a href="<?= BASE_URL ?>/auth/register.php">Register here</a>
    </div>
  </div>
</body>
</html>
