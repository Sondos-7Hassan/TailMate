<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php'; 
include __DIR__ . '/../includes/validation.php'; 
if (!function_exists('validate_input')) {
    function validate_input($v){
        $v = trim($v);
        $v = stripslashes($v);
        $v = htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return $v;
    }
}
if (!function_exists('check_unique')) {
    function check_unique($field, $table, $value) {
        global $connect;
        try {
            $sql = "SELECT COUNT(*) FROM `$table` WHERE `$field` = :val";
            $stm = $connect->prepare($sql);
            $stm->execute([':val' => $value]);
            return $stm->fetchColumn() > 0;
        } catch (Exception $e) {
            return true;
        }
    }
}
// --------------------------------------------------------------------

$errors = [];
$old = ['name'=>'','email'=>'','phone'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = validate_input($_POST['name'] ?? '');
    $email    = validate_input($_POST['email'] ?? '');
    $phone    = validate_input($_POST['phone_number'] ?? '');
    $pass     = $_POST['pass'] ?? '';
    $confirm  = $_POST['confirm_pass'] ?? '';
    $imageName = null;

    $old['name']  = $name;
    $old['email'] = $email;
    $old['phone'] = $phone;

    if ($name === '') $errors[] = "Name required";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "The email format is incorrect";
    if (!preg_match('/^(010|011|012|015)\d{8}$/', $phone)) $errors[] = "Mobile number must be 11 digits long and start with 010, 011, 012 or 015";
    if (strlen($pass) < 8) $errors[] = "The password must be at least 8 characters long";
    if ($pass !== $confirm) $errors[] ="The password and confirmation do not match";

    if (!empty($email) && check_unique('email', 'users', $email)) {
        $errors[] = "This email address is already in use";
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading image.";
        } else {
            $tmp = $_FILES['image']['tmp_name'];
            $size = $_FILES['image']['size'];
            $orig_name = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg','jpeg','png'];

            $finfo = @getimagesize($tmp);
            if ($finfo === false) {
                $errors[] ="The uploaded file is not a valid image.";
            } elseif (!in_array($ext, $allowed_ext, true)) {
                $errors[] = "Image type not supported — use JPG or PNG.";
            } elseif ($size > 2 * 1024 * 1024) {
                $errors[] = "Image size is larger than 2MB.";
            } else {
                // انشاء اسم فريد
                $safeBase = preg_replace("/[^a-zA-Z0-9_-]/", "_", strtolower($name ?: pathinfo($orig_name, PATHINFO_FILENAME)));
                $unique = uniqid($safeBase . "_", true);
                $imageName = $unique . "." . $ext;
                $uploadDir = UPLOADS_PATH;
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0755, true);
                }
                $dest = $uploadDir . DIRECTORY_SEPARATOR . $imageName;
                if (!move_uploaded_file($tmp, $dest)) {
                    $errors[] = "Failed to save image to server.";
                    $imageName = null;
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $connect->prepare("INSERT INTO users (name, email, phone, password, image) VALUES (:name,:email,:phone,:password,:image)");
            $ok = $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':password' => $hashed,
                ':image' => $imageName
            ]);
            if ($ok) {
                $_SESSION['user_id'] = $connect->lastInsertId(); 
                $_SESSION['username'] = $name;
                $_SESSION['profile_pic'] = $imageName ?? 'default.png';
                $_SESSION['is_admin'] = 0;
                header("Location: " . BASE_URL . "/pages/home.php");
                exit;
            } else {
                $errors[] = "An error occurred while saving data.";

            }
        } catch (PDOException $ex) {
            $errors[] = "Database error.";
          
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

body {
  background: #FFF6EC;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Poppins', sans-serif;
  margin:0;
}

.form-box {
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(6px);
  padding: 30px;
  border-radius: 16px;
  width: 100%;
  max-width: 480px;
  color: #5C3A21;
  box-shadow: 0 8px 30px rgba(92,58,33,0.5);
}

h2 { 
  text-align:center; 
  margin:0 0 18px; 
  text-transform:uppercase; 
  letter-spacing:1px;
  color: #5C3A21;
}

.form-box input[type="text"], 
.form-box input[type="email"], 
.form-box input[type="password"], 
.form-box input[type="file"] {
  width:100%; 
  padding:12px; 
  margin:10px 0; 
  border-radius:8px; 
  border:1px solid rgba(92,58,33,0.3);
  background: #fff; 
  color:#5C3A21; 
  outline:none; 
  font-size:15px;
  transition: 0.3s;
}

.form-box input:focus { 
  box-shadow:0 0 8px rgba(231,167,105,0.6);
  border-color:#E7A769; 
  background: #FFF6EC;
}

.form-box input::placeholder {
  color: rgba(92,58,33,0.6);
}

.form-box button { 
  width:100%; 
  padding:14px; 
  margin-top:14px; 
  border:0; 
  border-radius:10px; 
  cursor:pointer;
  background: linear-gradient(135deg,#E7A769,#DFA569);
  color:#fff; 
  font-weight:600;
  font-size: 17px;
  transition:0.3s;
}

.form-box button:hover { 
  background: linear-gradient(135deg,#DFA569,#E7A769);
  transform: scale(1.02);
}

.checkbox-area {
  margin: 12px 0;
  font-size: 14px;
  color: #5C3A21;
}

.checkbox-area input {
  margin-right: 6px;
}

.login-link {
  margin-top: 14px;
  text-align: center;
  font-size: 14px;
  color: #5C3A21;
}
.login-link a {
  color: #DFA569;
  font-weight: 600;
  text-decoration: none;
}
.login-link a:hover {
  text-decoration: underline;
}

/* === Popup Modal === */
.modal {
  display: none;
  position: fixed;
  z-index: 999;
  left:0;
  top:0;
  width:100%;
  height:100%;
  background: rgba(0,0,0,0.5);
}

.modal-content {
  background: #fff;
  color:#5C3A21;
  margin: 60px auto;
  padding: 20px;
  border-radius: 16px;
  width: 90%;
  max-width: 600px;
  box-shadow: 0 8px 30px rgba(92,58,33,0.5);
  animation: fadeIn 0.3s ease-in-out;
  max-height: 80vh;
  overflow-y: auto;
}

.modal h3 {
  margin-top:0;
  text-align: center;
  color:#5C3A21;
}

.close-btn {
  float:right;
  font-size:22px;
  font-weight:bold;
  cursor:pointer;
  color:#DFA569;
}

.close-btn:hover {
  color:#E7A769;
}

@keyframes fadeIn {
  from {opacity:0; transform:translateY(-20px);}
  to {opacity:1; transform:translateY(0);}
}
</style>
</head>
<body>
  <div class="form-box" role="main">
    <h2>Register</h2>

    <?php if (!empty($errors)): ?>
  <div style="color:red; margin-bottom:15px;">
    <ul>
      <?php foreach($errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

    <form action="#" method="POST" enctype="multipart/form-data" novalidate>
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="phone_number" placeholder="Phone Number" required>
        <input type="password" name="pass" placeholder="Password" required>
        <input type="password" name="confirm_pass" placeholder="Confirm Password" required>
        <label class="small">Profile Image (optional, JPG/PNG, max 2MB)</label>
        <input type="file" name="image" accept="image/jpeg,image/png">

        <div class="checkbox-area">
          <label>
            <input type="checkbox" name="terms" required>
            I agree to the <a href="#" id="openTerms">Terms & Conditions</a>.
          </label>
        </div>

        <button type="submit">Register</button>
    </form>

    <div class="login-link">
      Already have an account? <a href="<?= BASE_URL ?>/auth/login.php">Login here</a>.
    </div>
  </div>

  <!-- Popup Modal -->
  <div id="termsModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeTerms">&times;</span>
      <h3>Adoption Terms & Conditions</h3>
      <p><strong>Adoption Rules:</strong></p>
      <ul>
        <li>You must be at least 21 years old to adopt.</li>
        <li>You agree to provide a safe and loving home environment.</li>
        <li>Adopted animals must not be used for breeding or commercial purposes.</li>
        <li>Regular veterinary care, including vaccinations and check-ups, is required.</li>
        <li>If you can no longer care for the pet, you must contact us before rehoming.</li>
      </ul>

      <p><strong>How to Care for Your New Pet:</strong></p>
      <ul>
        <li><b>Cats:</b> Provide fresh water, quality food, and a clean litter box daily. Offer toys and scratching posts to keep them active. Ensure they have a quiet space to rest.</li>
        <li><b>Dogs:</b> Walk your dog at least twice a day, provide nutritious food and fresh water. Give them enough playtime, training, and attention. Never leave them chained for long periods.</li>
      </ul>

      <p>Adopting an animal is a lifelong commitment. By agreeing to these terms, you confirm that you are ready to take responsibility for your new family member.</p>
    </div>
  </div>

<script>
  const modal = document.getElementById("termsModal");
  const openBtn = document.getElementById("openTerms");
  const closeBtn = document.getElementById("closeTerms");

  openBtn.onclick = function(e){
    e.preventDefault();
    modal.style.display = "block";
  }

  closeBtn.onclick = function(){
    modal.style.display = "none";
  }

  window.onclick = function(event) {
    if(event.target == modal){
      modal.style.display = "none";
    }
  }
</script>
</body>
</html>

