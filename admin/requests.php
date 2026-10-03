<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// معالجة القبول أو الرفض
$successMessage = '';
$errorMessage = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $requestId = (int) $_GET['id'];
    $action = $_GET['action'];
    
    // جلب بيانات الطلب
    $requestQuery = $connect->prepare("SELECT ar.animal_id, ar.user_id, u.name as user_name, a.name as animal_name FROM adoption_requests ar JOIN users u ON ar.user_id = u.id JOIN animals a ON ar.animal_id = a.id WHERE ar.id = ?");
    $requestQuery->execute([$requestId]);
    $request = $requestQuery->fetch(PDO::FETCH_ASSOC);
    $animalId = $request['animal_id'];
    $userId = $request['user_id'];
    $userName = $request['user_name'];
    $animalName = $request['animal_name'];

    if ($action === 'accept') {
        // تحديث حالة الطلب إلى Approved
        $update = $connect->prepare("UPDATE adoption_requests SET status = 'Approved' WHERE id = ?");
        $update->execute([$requestId]);
        
        // تحديث حالة الحيوان إلى Reserved
        $updateAnimal = $connect->prepare("UPDATE animals SET status = 'Reserved' WHERE id = ?");
        $updateAnimal->execute([$animalId]);
        
        // إرسال رسالة للمستخدم
        $adminId = $_SESSION['user_id'];
        $message = "🎉 Great news! Your adoption request for {$animalName} has been APPROVED! We will contact you soon to finalize the adoption process.";
        $insertMessage = $connect->prepare("INSERT INTO user_messages (from_user_id, to_user_id, message, request_id, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insertMessage->execute([$adminId, $userId, $message, $requestId]);
        
        $successMessage = "Request #{$requestId} approved successfully! Message sent to {$userName}.";
        
    } elseif ($action === 'reject') {
        // تحديث حالة الطلب إلى Rejected
        $update = $connect->prepare("UPDATE adoption_requests SET status = 'Rejected' WHERE id = ?");
        $update->execute([$requestId]);
        
        // إرسال رسالة للمستخدم
        $adminId = $_SESSION['user_id'];
        $message = "We regret to inform you that your adoption request for {$animalName} has been declined. This may be due to various factors. You're welcome to browse other available pets or contact us for more information.";
        $insertMessage = $connect->prepare("INSERT INTO user_messages (from_user_id, to_user_id, message, request_id, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insertMessage->execute([$adminId, $userId, $message, $requestId]);
        
        // التحقق من وجود طلبات أخرى نشطة لنفس الحيوان
        $checkOtherRequests = $connect->prepare("
            SELECT COUNT(*) as count FROM adoption_requests 
            WHERE animal_id = ? AND id != ? 
            AND status IN ('Pending', 'Under Review', 'Approved')
        ");
        $checkOtherRequests->execute([$animalId, $requestId]);
        $otherRequests = $checkOtherRequests->fetch(PDO::FETCH_ASSOC);
        
        // إذا لم يكن هناك طلبات نشطة أخرى، أرجع الحيوان إلى Available
        if ($otherRequests['count'] == 0) {
            $updateAnimal = $connect->prepare("UPDATE animals SET status = 'Available' WHERE id = ?");
            $updateAnimal->execute([$animalId]);
        }
        
        $successMessage = "Request #{$requestId} rejected. Notification sent to {$userName}.";
        
    } elseif ($action === 'review') {
        $update = $connect->prepare("UPDATE adoption_requests SET status = 'Under Review' WHERE id = ?");
        $update->execute([$requestId]);
        
        // إرسال رسالة للمستخدم
        $adminId = $_SESSION['user_id'];
        $message = "Your adoption request for {$animalName} is now under review. We're carefully evaluating your application and will get back to you soon.";
        $insertMessage = $connect->prepare("INSERT INTO user_messages (from_user_id, to_user_id, message, request_id, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insertMessage->execute([$adminId, $userId, $message, $requestId]);
        
        $successMessage = "Request #{$requestId} marked as Under Review.";
        
    } elseif ($action === 'complete') {
        // تحديث حالة الطلب إلى Completed
        $update = $connect->prepare("UPDATE adoption_requests SET status = 'Completed', adopted_at = NOW() WHERE id = ?");
        $update->execute([$requestId]);
        
        // تحديث حالة الحيوان إلى Adopted
        $updateAnimal = $connect->prepare("UPDATE animals SET status = 'Adopted', adopted_by = ?, adopted_at = NOW() WHERE id = ?");
        $updateAnimal->execute([$userId, $animalId]);
        
        // إرسال رسالة للمستخدم
        $adminId = $_SESSION['user_id'];
        $message = "🐾 Congratulations! The adoption of {$animalName} is now complete! Thank you for giving them a loving home. We wish you both a wonderful life together!";
        $insertMessage = $connect->prepare("INSERT INTO user_messages (from_user_id, to_user_id, message, request_id, created_at) VALUES (?, ?, ?, ?, NOW())");
        $insertMessage->execute([$adminId, $userId, $message, $requestId]);
        
        $successMessage = "Adoption completed! {$animalName} is now officially adopted by {$userName}.";
        
    } elseif ($action === 'pending') {
        $update = $connect->prepare("UPDATE adoption_requests SET status = 'Pending' WHERE id = ?");
        $update->execute([$requestId]);
        
        $successMessage = "Request #{$requestId} returned to Pending status.";
    }

    // إعادة التوجيه مع الرسالة
    $_SESSION['request_message'] = $successMessage;
    $_SESSION['request_message_type'] = 'success';
    header("Location: " . BASE_URL . "/admin/requests.php");
    exit();
}

// عرض الرسائل
if (isset($_SESSION['request_message'])) {
    $successMessage = $_SESSION['request_message'];
    $messageType = $_SESSION['request_message_type'] ?? 'success';
    unset($_SESSION['request_message']);
    unset($_SESSION['request_message_type']);
}

// عرض الطلبات
$query = $connect->query("
SELECT 
    adoption_requests.id AS Request_number,
    users.name AS user,
    users.email AS user_email,
    animals.name AS animal,
    animals.type AS animal_type,
    animals.image AS animal_image,
    adoption_requests.user_id,
    adoption_requests.animal_id,
    adoption_requests.applicant_name,
    adoption_requests.applicant_email,
    adoption_requests.applicant_phone,
    adoption_requests.applicant_address,
    adoption_requests.applicant_age,
    adoption_requests.applicant_gender,
    adoption_requests.has_other_pets,
    adoption_requests.other_pets_details,
    adoption_requests.living_situation_details,
    adoption_requests.reason_for_adoption,
    adoption_requests.can_provide_medical_care,
    adoption_requests.status,
    adoption_requests.created_at
FROM adoption_requests
JOIN users ON adoption_requests.user_id = users.id
JOIN animals ON adoption_requests.animal_id = animals.id
ORDER BY adoption_requests.id DESC
");

$requests = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../includes/head.php'; ?>
<body>

<div class="wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <div class="content">
        <?php include __DIR__ . '/../includes/admin_navbar.php'; ?>

        <div>
            <h2 class="mb-4">Adoption Requests</h2>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($successMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <table class="table table-bordered align-middle">
                <thead style="background-color: #E7A769; color: #5C3A21;">
                    <tr>
                        <th>Request #</th>
                        <th>User</th>
                        <th>Animal</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)) { ?>
                        <?php foreach ($requests as $request) { ?>
                            <tr>
                                <td><?php echo $request['Request_number']; ?></td>
                                <td><?php echo htmlspecialchars($request['applicant_name']); ?></td>
                                <td><?php echo htmlspecialchars($request['animal']); ?></td>
                                <td><?php echo htmlspecialchars($request['applicant_phone'] ?? ''); ?></td>
                                <td><?php echo isset($request['created_at']) ? date('Y-m-d', strtotime($request['created_at'])) : 'N/A'; ?></td>
                                <td>
                                    <?php if ($request['status'] == 'Pending') { ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php } elseif ($request['status'] == 'Under Review') { ?>
                                        <span class="badge bg-info text-dark">Under Review</span>
                                    <?php } elseif ($request['status'] == 'Approved') { ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php } elseif ($request['status'] == 'Rejected') { ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php } elseif ($request['status'] == 'Completed') { ?>
                                        <span class="badge bg-primary">Completed</span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary"><?php echo $request['status']; ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $request['Request_number']; ?>">Show</button>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $request['Request_number']; ?>">Message</button>
                                    <?php if ($request['status'] == 'Pending') { ?>
                                        <a href="<?= BASE_URL ?>/admin/requests.php?action=review&id=<?php echo $request['Request_number']; ?>" class="btn btn-info btn-sm">Review</a>
                                        <a href="<?= BASE_URL ?>/admin/requests.php?action=accept&id=<?php echo $request['Request_number']; ?>" class="btn btn-success btn-sm">Accept</a>
                                        <a href="<?= BASE_URL ?>/admin/requests.php?action=reject&id=<?php echo $request['Request_number']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                    <?php } elseif ($request['status'] == 'Under Review') { ?>
                                        <a href="<?= BASE_URL ?>/admin/requests.php?action=accept&id=<?php echo $request['Request_number']; ?>" class="btn btn-success btn-sm">Accept</a>
                                        <a href="<?= BASE_URL ?>/admin/requests.php?action=reject&id=<?php echo $request['Request_number']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                    <?php } elseif ($request['status'] == 'Approved') { ?>
                                        <a href="<?= BASE_URL ?>/admin/requests.php?action=complete&id=<?php echo $request['Request_number']; ?>" class="btn btn-primary btn-sm">Complete</a>
                                        <a href="<?= BASE_URL ?>/admin/requests.php?action=reject&id=<?php echo $request['Request_number']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                    <?php } elseif ($request['status'] == 'Rejected') { ?>
                                        <a href="<?= BASE_URL ?>/admin/requests.php?action=pending&id=<?php echo $request['Request_number']; ?>" class="btn btn-warning btn-sm">Reopen</a>
                                    <?php } elseif ($request['status'] == 'Completed') { ?>
                                        <span class="text-muted">Completed</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="text-center">No requests found</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <!-- Modals Section - خارج الجدول -->
        <?php if (!empty($requests)) { ?>
            <?php foreach ($requests as $request) { ?>
                
                <!-- Modal for Request Details -->
                <div class="modal fade" id="detailsModal<?php echo $request['Request_number']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #E7A769;">
                                <h5 class="modal-title" style="color: #5C3A21;">Request #<?php echo $request['Request_number']; ?> Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <?php if (!empty($request['animal_image'])): ?>
                                            <img src="<?= BASE_URL ?>/assets/images/<?php echo htmlspecialchars($request['animal_image']); ?>" 
                                                 alt="Animal" class="img-fluid rounded" style="max-height: 200px;">
                                        <?php endif; ?>
                                        <h5 class="mt-2"><?php echo htmlspecialchars($request['animal']); ?></h5>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($request['animal_type']); ?></span>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 style="color: #E7A769;">Applicant Information</h6>
                                        <table class="table table-sm">
                                            <tr><th>Name:</th><td><?php echo htmlspecialchars($request['applicant_name']); ?></td></tr>
                                            <tr><th>Email:</th><td><?php echo htmlspecialchars($request['applicant_email']); ?></td></tr>
                                            <tr><th>Phone:</th><td><?php echo htmlspecialchars($request['applicant_phone'] ?? ''); ?></td></tr>
                                            <tr><th>Address:</th><td><?php echo htmlspecialchars($request['applicant_address'] ?? ''); ?></td></tr>
                                            <tr><th>Age:</th><td><?php echo htmlspecialchars($request['applicant_age'] ?? ''); ?></td></tr>
                                            <tr><th>Gender:</th><td><?php echo htmlspecialchars($request['applicant_gender'] ?? ''); ?></td></tr>
                                        </table>
                                        
                                        <h6 class="mt-3" style="color: #E7A769;">Adoption Details</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <th>Has Other Pets:</th>
                                                <td><?php echo $request['has_other_pets'] == 1 ? 'Yes' : 'No'; ?></td>
                                            </tr>
                                            <?php if ($request['has_other_pets'] == 1): ?>
                                            <tr>
                                                <th>Pet Details:</th>
                                                <td><?php echo htmlspecialchars($request['other_pets_details'] ?? ''); ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <th>Living Situation:</th>
                                                <td><?php echo nl2br(htmlspecialchars($request['living_situation_details'] ?? '')); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Reason for Adoption:</th>
                                                <td><?php echo nl2br(htmlspecialchars($request['reason_for_adoption'] ?? '')); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Can Provide Medical Care:</th>
                                                <td><?php echo $request['can_provide_medical_care'] == 1 ? 'Yes' : 'No'; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal for Sending Message -->
                <div class="modal fade" id="messageModal<?php echo $request['Request_number']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #E7A769;">
                                <h5 class="modal-title" style="color: #5C3A21;">Send Message to <?php echo htmlspecialchars($request['applicant_name']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="<?= BASE_URL ?>/pages/notifications.php">
                                <div class="modal-body">
                                    <p><strong>Request:</strong> #<?php echo $request['Request_number']; ?> - <?php echo htmlspecialchars($request['animal']); ?></p>
                                    <input type="hidden" name="to_user_id" value="<?php echo $request['user_id']; ?>">
                                    <input type="hidden" name="request_id" value="<?php echo $request['Request_number']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Message:</label>
                                        <textarea name="message" class="form-control" rows="4" placeholder="Type your message to the applicant..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="send_message" class="btn" style="background-color: #E7A769; color: #5C3A21;">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
            <?php } ?>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
