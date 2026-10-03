<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// جلب بيانات المستخدم
$stmt = $connect->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// معالجة تحديث البيانات
$successMessage = '';
$errorMessage = '';

// معالجة رفع صورة البروفايل
if (isset($_POST['upload_image'])) {
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['profile_image']['tmp_name'];
        $size = $_FILES['profile_image']['size'];
        $orig_name = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        
        $finfo = @getimagesize($tmp);
        if (!$finfo) {
            $errorMessage = "The uploaded file is not a valid image.";
        } elseif (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $errorMessage = "Image type not supported — use JPG or PNG.";
        } elseif ($size > 2097152) { // 2MB
            $errorMessage = "Image size is larger than 2MB.";
        } else {
            $safeBase = preg_replace("/[^a-zA-Z0-9_-]/", "_", strtolower($user['name']));
            $unique = uniqid($safeBase . "_", true);
            $imageName = $unique . "." . $ext;
            $uploadDir = UPLOADS_PATH;
            
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            
            $dest = $uploadDir . DIRECTORY_SEPARATOR . $imageName;
            if (move_uploaded_file($tmp, $dest)) {
                // حذف الصورة القديمة إذا كانت موجودة
                if (!empty($user['image']) && file_exists($uploadDir . DIRECTORY_SEPARATOR . $user['image'])) {
                    @unlink($uploadDir . DIRECTORY_SEPARATOR . $user['image']);
                }
                
                // تحديث قاعدة البيانات
                $updateStmt = $connect->prepare("UPDATE users SET image = ? WHERE id = ?");
                if ($updateStmt->execute([$imageName, $userId])) {
                    $_SESSION['profile_pic'] = $imageName;
                    $successMessage = "Profile picture updated successfully!";
                    // إعادة تحميل البيانات
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $errorMessage = "Error updating profile picture in database.";
                }
            } else {
                $errorMessage = "Failed to save image to server.";
            }
        }
    } else {
        $errorMessage = "Please select an image to upload.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['upload_image']) && !isset($_POST['change_password'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    
    // التحقق من صحة البيانات
    if (empty($name) || empty($email)) {
        $errorMessage = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } else {
        // تحديث البيانات
        $updateStmt = $connect->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        if ($updateStmt->execute([$name, $email, $phone, $userId])) {
            $_SESSION['username'] = $name;
            $successMessage = "Profile updated successfully!";
            // إعادة تحميل البيانات
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $errorMessage = "Error updating profile.";
        }
    }
}

// معالجة تغيير كلمة المرور
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errorMessage = "All password fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "New passwords do not match.";
    } elseif (strlen($newPassword) < 6) {
        $errorMessage = "Password must be at least 6 characters.";
    } else {
        // التحقق من كلمة المرور الحالية
        if (password_verify($currentPassword, $user['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $connect->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($updateStmt->execute([$hashedPassword, $userId])) {
                $successMessage = "Password changed successfully!";
            } else {
                $errorMessage = "Error changing password.";
            }
        } else {
            $errorMessage = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - TailMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #FFF6EC;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
            color: #5C3A21;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        
        .navbar-custom a:hover {
            color: #3e2414;
        }
        
        .profile-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(92, 58, 33, 0.1);
            margin-bottom: 20px;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            color: #5C3A21;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E7A769, #5C3A21);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(231, 167, 105, 0.3);
        }
        
        .btn-custom {
            background-color: #5C3A21;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            background-color: #E7A769;
            color: #5C3A21;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(92, 58, 33, 0.2);
        }
        
        .btn-outline-custom {
            border: 2px solid #E7A769;
            color: #E7A769;
            background: transparent;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-custom:hover {
            background: #E7A769;
            color: white;
            transform: translateY(-1px);
        }
        
        .section-title {
            color: #5C3A21;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: #E7A769;
        }
        
        .form-label {
            color: #5C3A21;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control:focus {
            border-color: #E7A769;
            box-shadow: 0 0 0 0.2rem rgba(231, 167, 105, 0.25);
        }
        
        .table {
            background: white;
        }
        
        .table thead {
            background-color: #E7A769;
            color: #5C3A21;
        }
        
        .badge {
            padding: 6px 12px;
            font-weight: 500;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>

    <div class="profile-container">
        <!-- Profile Header Card -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar" style="position: relative;">
                    <?php if (!empty($user['image']) && file_exists(UPLOADS_PATH . '/' . $user['image'])): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?php echo htmlspecialchars($user['image']); ?>" 
                             alt="Profile Picture" 
                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="text-muted"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                
                <!-- Edit Profile Picture Button -->
                <button type="button" class="btn btn-sm btn-outline-custom mt-2" data-bs-toggle="modal" data-bs-target="#editPictureModal">
                    <i class="bi bi-camera"></i> Change Profile Picture
                </button>
            </div>
        </div>

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

        <!-- Update Profile Form -->
        <div class="profile-card">
            <h4 class="section-title">
                <i class="bi bi-person-circle"></i> Update Profile
            </h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-custom">Update Profile</button>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="profile-card">
            <h4 class="section-title">
                <i class="bi bi-shield-lock"></i> Change Password
            </h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-custom">Change Password</button>
            </form>
        </div>

        <!-- My Adoption Requests -->
        <div class="profile-card">
            <h4 class="section-title">
                <i class="bi bi-file-earmark-text"></i> My Adoption Requests
            </h4>
            <?php
            $requestsStmt = $connect->prepare("
                SELECT adoption_requests.*, animals.name as animal_name, animals.type, animals.image
                FROM adoption_requests
                JOIN animals ON adoption_requests.animal_id = animals.id
                WHERE adoption_requests.user_id = ?
                ORDER BY adoption_requests.id DESC
            ");
            $requestsStmt->execute([$userId]);
            $requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if (!empty($requests)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Animal</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['animal_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['type']); ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = '';
                                        switch($request['status']) {
                                            case 'Pending': $statusClass = 'warning'; break;
                                            case 'Approved': $statusClass = 'success'; break;
                                            case 'Rejected': $statusClass = 'danger'; break;
                                            case 'Under Review': $statusClass = 'info'; break;
                                            case 'Completed': $statusClass = 'primary'; break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($request['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($request['created_at'] ?? 'now')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">You haven't submitted any adoption requests yet.</p>
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/pages/cats.php" class="btn btn-custom">Browse Pets</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- My Adopted Pets -->
        <div class="profile-card">
            <h4 class="section-title">
                <i class="bi bi-heart-fill text-danger"></i> My Adopted Pets
            </h4>
            <?php
            $adoptedStmt = $connect->prepare("
                SELECT 
                    a.id as animal_id,
                    a.name as animal_name,
                    a.type,
                    a.age,
                    a.image,
                    a.adopted_at,
                    ar.created_at as request_date
                FROM animals a
                JOIN adoption_requests ar ON a.id = ar.animal_id
                WHERE a.adopted_by = ? AND ar.status = 'Completed' AND ar.user_id = ?
                ORDER BY a.adopted_at DESC
            ");
            $adoptedStmt->execute([$userId, $userId]);
            $adoptedPets = $adoptedStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if (!empty($adoptedPets)): ?>
                <div class="row">
                    <?php foreach ($adoptedPets as $pet): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100" style="border: 2px solid #E7A769;">
                            <div class="row g-0">
                                <div class="col-4">
                                    <?php if (!empty($pet['image'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/images/<?php echo htmlspecialchars($pet['image']); ?>" 
                                             class="img-fluid rounded-start h-100" 
                                             alt="<?php echo htmlspecialchars($pet['animal_name']); ?>"
                                             style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center h-100 rounded-start">
                                            <i class="bi bi-image" style="font-size: 2rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h5 class="card-title" style="color: #5C3A21;">
                                            <i class="bi bi-heart-fill text-danger"></i> 
                                            <?php echo htmlspecialchars($pet['animal_name']); ?>
                                        </h5>
                                        <p class="card-text mb-1">
                                            <span class="badge bg-info"><?php echo $pet['type']; ?></span>
                                            <span class="badge bg-secondary"><?php echo $pet['age']; ?> years</span>
                                        </p>
                                        <p class="card-text mb-1" style="font-size: 0.85rem;">
                                            <i class="bi bi-calendar-event text-primary"></i> 
                                            <strong>Requested:</strong><br>
                                            <?php echo date('M d, Y', strtotime($pet['request_date'])); ?>
                                        </p>
                                        <p class="card-text" style="font-size: 0.85rem;">
                                            <i class="bi bi-check-circle-fill text-success"></i> 
                                            <strong>Adopted:</strong><br>
                                            <?php echo date('M d, Y', strtotime($pet['adopted_at'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> You haven't completed any adoptions yet.
                    <br><small>Once your adoption requests are approved and completed, they will appear here.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Editing Profile Picture -->
    <div class="modal fade" id="editPictureModal" tabindex="-1" aria-labelledby="editPictureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #E7A769;">
                    <h5 class="modal-title" id="editPictureModalLabel" style="color: #5C3A21;">
                        <i class="bi bi-camera"></i> Change Profile Picture
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <div class="profile-avatar mx-auto" style="width: 120px; height: 120px;">
                                <?php if (!empty($user['image']) && file_exists(UPLOADS_PATH . '/' . $user['image'])): ?>
                                    <img src="<?= BASE_URL ?>/uploads/<?php echo htmlspecialchars($user['image']); ?>" 
                                         alt="Current Picture" 
                                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white;">
                                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <p class="text-muted mt-2">Current Profile Picture</p>
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Upload New Picture (JPG or PNG, Max 2MB)</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/jpg" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="upload_image" class="btn btn-custom">
                            <i class="bi bi-upload"></i> Upload Picture
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
