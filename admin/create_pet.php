<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php'; // الاتصال بقاعدة البيانات

// حماية الداشبورد للادمن فقط
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// التحقق من وجود معرف للتعديل
$editMode = false;
$petData = null;
if (isset($_GET['id'])) {
    $editMode = true;
    $petId = (int) $_GET['id'];
    $stmt = $connect->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->execute([$petId]);
    $petData = $stmt->fetch(PDO::FETCH_ASSOC);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'];
    $type = $_POST['type'];
    $gender = $_POST['sex'];
    $age = $_POST['age'];
    $description = $_POST['description'];
    $status = $_POST['status'] ?? 'Available'; // إضافة حالة الحيوان

    $img = $_FILES['image'];
    $imgname = $img['name'];
    $imgtmp = $img['tmp_name'];
    $imgerror = "";
    
    // إذا كان في وضع التعديل ولم يتم رفع صورة جديدة
    if ($editMode && empty($imgname) && isset($_POST['pet_id'])) {
        $petId = (int) $_POST['pet_id'];
        $updatePet = $connect->prepare("UPDATE animals SET name = ?, type = ?, sex = ?, age = ?, description = ?, status = ? WHERE id = ?");
        if($updatePet->execute([$name, $type, $gender, $age, $description, $status, $petId])){
            header("Location: " . BASE_URL . "/admin/availablepets.php");
            exit();
        }
    } else {
        $exts = pathinfo($imgname, PATHINFO_EXTENSION);
        $allowedexts = ['jpg', 'jpeg', 'png', 'gif'];

        if(!in_array(strtolower($exts), $allowedexts)){
            $imgerror = "<div class='alert alert-danger'>Invalid file type.</div>";
        } else {
            if ($editMode && isset($_POST['pet_id'])) {
                $petId = (int) $_POST['pet_id'];
                $updatePet = $connect->prepare("UPDATE animals SET name = ?, type = ?, sex = ?, age = ?, description = ?, image = ?, status = ? WHERE id = ?");
                if($updatePet->execute([$name, $type, $gender, $age, $description, $imgname, $status, $petId])){
                    move_uploaded_file($imgtmp, UPLOADS_PATH . '/' . $imgname);
                    header("Location: " . BASE_URL . "/admin/availablepets.php");
                    exit();
                }
            } else {
                $newpet = $connect->prepare("INSERT INTO animals (name, type, sex, age, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if($newpet->execute([$name, $type, $gender, $age, $description, $imgname, $status])){
                    move_uploaded_file($imgtmp, UPLOADS_PATH . '/' . $imgname);
                    header("Location: " . BASE_URL . "/admin/availablepets.php");
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Error adding pet. Please try again.</div>";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Pet - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f5f5f5;
    }
    .wrapper {
      display: flex;
    }
    .content {
      margin-left: 250px;
      padding: 20px;
      flex: 1;
    }
      padding: 10px 20px;
      color: white;
      margin-bottom: 20px;
    }
    .form-container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .btn-custom {
      background-color: #5C3A21;
      color: white;
      font-weight: bold;
    }
    .btn-custom:hover {
      background-color: #E7A769;
      color: white;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <!-- Content -->
  <div class="content">
    <?php include __DIR__ . '/../includes/admin_navbar.php'; ?>

    <div class="col-8 mx-auto mt-4">
      <div class="form-container">
        <h3 class="mb-4" style="color:#5C3A21;"><?php echo $editMode ? 'Edit Pet' : 'Add New Pet'; ?></h3>

        <?php if (!empty($imgerror)) echo $imgerror; ?>

        <form method="POST" enctype="multipart/form-data">
          <?php if ($editMode && $petData): ?>
            <input type="hidden" name="pet_id" value="<?php echo $petData['id']; ?>">
          <?php endif; ?>
          
          <div class="mb-3">
            <label class="form-label">Pet Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter pet name" 
                   value="<?php echo $editMode && $petData ? htmlspecialchars($petData['name']) : ''; ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
              <option value="">Select type</option>
              <option value="Cat" <?php echo ($editMode && $petData && $petData['type'] == 'Cat') ? 'selected' : ''; ?>>Cat</option>
              <option value="Dog" <?php echo ($editMode && $petData && $petData['type'] == 'Dog') ? 'selected' : ''; ?>>Dog</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="sex" class="form-select" required>
              <option value="">Select gender</option>
              <option value="Male" <?php echo ($editMode && $petData && $petData['sex'] == 'Male') ? 'selected' : ''; ?>>Male</option>
              <option value="Female" <?php echo ($editMode && $petData && $petData['sex'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Age (years)</label>
            <input type="number" name="age" class="form-control" placeholder="Enter age" 
                   value="<?php echo $editMode && $petData ? htmlspecialchars($petData['age']) : ''; ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="Available" <?php echo ($editMode && $petData && $petData['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
              <option value="Reserved" <?php echo ($editMode && $petData && $petData['status'] == 'Reserved') ? 'selected' : ''; ?>>Reserved</option>
              <option value="Adopted" <?php echo ($editMode && $petData && $petData['status'] == 'Adopted') ? 'selected' : ''; ?>>Adopted</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Enter description" required><?php echo $editMode && $petData ? htmlspecialchars($petData['description']) : ''; ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Image <?php echo $editMode ? '(Leave empty to keep current image)' : ''; ?></label>
            <input type="file" name="image" class="form-control" <?php echo !$editMode ? 'required' : ''; ?>>
            <?php if ($editMode && $petData && !empty($petData['image'])): ?>
              <small class="text-muted">Current image: <?php echo htmlspecialchars($petData['image']); ?></small>
            <?php endif; ?>
          </div>
          <button type="submit" class="btn btn-custom w-100"><?php echo $editMode ? 'Update Pet' : 'Add Pet'; ?></button>
          <a href="<?= BASE_URL ?>/admin/availablepets.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>