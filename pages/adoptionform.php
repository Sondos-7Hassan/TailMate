<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';
include __DIR__ . '/../includes/validate.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$animalId = isset($_GET['animal_id']) ? (int)$_GET['animal_id'] : 0;

// جلب بيانات المستخدم
$userQuery = $connect->prepare("SELECT name, email FROM users WHERE id = ?");
$userQuery->execute([$userId]);
$userData = $userQuery->fetch(PDO::FETCH_ASSOC);

// التحقق من وجود الحيوان
if ($animalId > 0) {
    $animalCheck = $connect->prepare("SELECT id, name, status FROM animals WHERE id = ?");
    $animalCheck->execute([$animalId]);
    $animal = $animalCheck->fetch(PDO::FETCH_ASSOC);
    
    if (!$animal) {
        die("Animal not found!");
    }
    
    // التحقق من حالة الحيوان
    if ($animal['status'] == 'Reserved' || $animal['status'] == 'Adopted') {
        echo "<div style='background:#f2dede;color:#a94442;padding:20px;margin:20px;border-radius:5px;text-align:center;'>
        <h2>Sorry!</h2>
        <p>This animal is no longer available for adoption.</p>
        <a href='" . BASE_URL . "/pages/cats.php' style='display:inline-block;margin-top:10px;padding:10px 20px;background:#5C3A21;color:white;text-decoration:none;border-radius:5px;'>Browse Other Pets</a>
        </div>";
        exit();
    }
    
    // التحقق من وجود طلب سابق لنفس الحيوان من نفس المستخدم
    $existingRequest = $connect->prepare("
        SELECT id, status FROM adoption_requests 
        WHERE user_id = ? AND animal_id = ? 
        AND status IN ('Pending', 'Under Review', 'Approved')
    ");
    $existingRequest->execute([$userId, $animalId]);
    $existing = $existingRequest->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        echo "<div style='background:#fcf8e3;color:#8a6d3b;padding:20px;margin:20px;border-radius:5px;text-align:center;'>
        <h2>Pending Request</h2>
        <p>You already have a pending adoption request for this animal.</p>
        <p>Status: <strong>" . htmlspecialchars($existing['status']) . "</strong></p>
        <a href='" . BASE_URL . "/pages/profile.php' style='display:inline-block;margin-top:10px;padding:10px 20px;background:#5C3A21;color:white;text-decoration:none;border-radius:5px;'>View My Requests</a>
        </div>";
        exit();
    }
}

$errors = [];
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['telephone']);
    $address = trim($_POST['address']);
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $hasPets = $_POST['hasPets'];
    $petDetails = trim($_POST['petDetails']);
    $houseDetails = trim($_POST['houseDetails']);
    $reasonDetails = trim($_POST['reasonDetails']);
    $care = $_POST['care'];
    $animalIdPost = (int)$_POST['animal_id'];

    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    if (empty($gender)) {
        $errors[] = "Please select your gender";
    }

    if (empty($errors)) {
        // إدراج الطلب
        $insertRequest = $connect->prepare("INSERT INTO 
        adoption_requests (`user_id`, `animal_id`, `applicant_name`, `applicant_email`, `applicant_phone`, `applicant_address`, `applicant_age`, `applicant_gender`, `has_other_pets`, `other_pets_details`, `living_situation_details`, `reason_for_adoption`, `can_provide_medical_care`, `status`)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        
        if ($insertRequest->execute([
            $userId,
            $animalIdPost,
            $name,
            $email,
            $phone,
            $address,
            $age,
            $gender,
            $hasPets,
            $petDetails,
            $houseDetails,
            $reasonDetails,
            $care
        ])) {
            // تحديث حالة الحيوان إلى Reserved مباشرة بعد تقديم الطلب
            $updateAnimal = $connect->prepare("UPDATE animals SET status = 'Reserved' WHERE id = ?");
            $updateAnimal->execute([$animalIdPost]);
            
            $successMessage = "<div style='background:#dff0d8;color:#3c763d;padding:20px;margin:20px;border-radius:5px;text-align:center;'>
            <h2>Request Submitted Successfully!</h2>
            <p>Thank you! Your adoption request has been sent to the administration.</p>
            <p>They will contact you shortly.</p>
            <a href='" . BASE_URL . "/pages/profile.php' style='display:inline-block;margin-top:10px;padding:10px 20px;background:#5C3A21;color:white;text-decoration:none;border-radius:5px;'>View My Requests</a>
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TailMate - Adoption Form</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFF6EC;
            color: #5C3A21;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(92, 58, 33, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #5C3A21;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            width: 96%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }


        .btn-submit {
            display: block;
            width: 100%;
            padding: 15px;
            background: #5C3A21;
            color: #fff;
            font-weight: bold;
            font-size: 1rem;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(92, 58, 33, 0.5);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #E7A769;
            color: #1A0C05;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(92, 58, 33, 0.6);
        }

        .error {
            color: darkred;
            font-size: 13px;
            margin-top: -15px;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>
    
    <?php 
    // عرض رسالة النجاح إذا كانت موجودة
    if (!empty($successMessage)) {
        echo $successMessage;
        // لا نعرض الفورم بعد النجاح
    } else {
        // عرض رسالة إذا لم يتم تحديد حيوان
        if ($animalId == 0) {
            echo "<div class='container'>
            <div style='background:#f2dede;color:#a94442;padding:20px;border-radius:5px;text-align:center;'>
                <h2>No Animal Selected</h2>
                <p>Please select an animal from our available pets.</p>
                <a href='" . BASE_URL . "/pages/cats.php' style='display:inline-block;margin:10px;padding:10px 20px;background:#5C3A21;color:white;text-decoration:none;border-radius:5px;'>View Cats</a>
                <a href='" . BASE_URL . "/pages/dogs.php' style='display:inline-block;margin:10px;padding:10px 20px;background:#5C3A21;color:white;text-decoration:none;border-radius:5px;'>View Dogs</a>
            </div>
            </div>";
        } else {
    ?>
    <div class="container">
        <h2>Adoption Form</h2>
        <form id="adoptionForm" method="POST" action="" novalidate>
            <input type="hidden" name="animal_id" value="<?php echo isset($_GET['animal_id']) ? htmlspecialchars($_GET['animal_id']) : ''; ?>">

            <label for="fullname">Full Name: <small style="color:#888;">(Auto-filled from your profile)</small></label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;" required>
            <span class="error" id="nameError"></span>

            <label for="email">Email: <small style="color:#888;">(Auto-filled from your profile)</small></label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed;" required>
            <span class="error" id="emailError"></span>

            <label for="telephone">Telephone:</label>
            <input type="tel" id="telephone" name="telephone" required>
            <span class="error" id="phoneError"></span>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" placeholder="Enter your full address" required>


            <label>Gender:</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="Male" required> Male</label>
                <label><input type="radio" name="gender" value="Female"> Female</label>
                <span class="error" id="genderError"></span>

            </div>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" min="18" required>
            <span class="error" id="ageError"></span>

            <label>Do you already have pets?</label>
            <div class="radio-group">
                <label><input type="radio" name="hasPets" value="Yes" required> Yes</label>
                <label><input type="radio" name="hasPets" value="No"> No</label>

            </div>

            <label for="petDetails">Details about your pet/s:</label>
            <input type="text" id="petDetails" name="petDetails" placeholder="if you don't have type I don't have" required>


            <label for="houseDetails">Details about House situation:</label>
            <input type="text" id="houseDetails" name="houseDetails" placeholder="if you don't have type I don't have" required>

            <label for="reasonDetails">What's your reason to adopt an animal?</label>
            <input type="text" id="reasonDetails" name="reasonDetails" required>

            <label>Are you able to give medical care?</label>
            <div class="radio-group">
                <label><input type="radio" name="care" value="Yes" required> Yes</label>
                <label><input type="radio" name="care" value="No"> No</label>
            </div>



            <button type="submit" class="btn-submit">Submit</button>
        </form>
    </div>

    <script>
        document.getElementById('adoptionForm').addEventListener('submit', function(e) {
            const fullname = document.getElementById('fullname').value.trim();
            const email = document.getElementById('email').value.trim();
            const telephone = document.getElementById('telephone').value.trim();
            const age = document.getElementById('age').value.trim();
            const address = document.getElementById('address').value.trim();

            let isvalid = true;

            let nameError = document.getElementById("nameError");
            let emailError = document.getElementById("emailError");
            let phoneError = document.getElementById("phoneError");
            let genderError = document.getElementById("genderError");
            let ageError = document.getElementById("ageError");

            nameError.textContent = "";
            emailError.textContent = "";
            phoneError.textContent = "";
            ageError.textContent = "";
            genderError.textContent = "";

            // gender
            const gen = document.getElementsByName("gender");
            let genderSelected = false;
            for (let i = 0; i < gen.length; i++) {
                if (gen[i].checked) {
                    genderSelected = true;
                    break;
                }
            }

            // validate fullname
            if (fullname.length < 3) {
                nameError.textContent = "*Name must be at least 3 characters long.";
                isvalid = false;
            }
            if (!isNaN(fullname)) {
                nameError.textContent = "*Name cannot be null.";
                isvalid = false;
            }

            // validate email
            const emailPat = /^[\w]+@[a-zA-Z0-9._]+\.[a-zA-Z]{2,3}$/;
            if (!emailPat.test(email)) {
                emailError.textContent = "*Please enter a valid email address.";
                isvalid = false;
            }

            // validate phone
            const phonePat = /^(010|011|012|015)\d{8}$/;
            if (!phonePat.test(telephone)) {
                phoneError.textContent = "*Please enter a valid phone number.";
                isvalid = false;
            }

            // validate age
            if (isNaN(age) || age < 18) {
                ageError.textContent = "*Age must be a number and at least 18.";
                isvalid = false;
            }

            // validate gender
            if (!genderSelected) {
                genderError.textContent = "*Please select your gender.";
                isvalid = false;
            }

            if (!isvalid) {
                e.preventDefault();
            }

            //         const formContainer = document.querySelector('.container');
            //         formContainer.innerHTML = `
            //     <h2>Request Submitted!</h2>
            //     <p>Thank you! Your adoption request has been sent to the administration and they will contact you shortly.</p>
            // `;
            //         return true;
        });
    </script>

    <?php 
        } // نهاية else - إغلاق شرط عرض الفورم
    } // نهاية else الرئيسي - إغلاق شرط رسالة النجاح
    ?>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>