<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// عدد المستخدمين
$users = $connect->query("SELECT COUNT(*) AS count FROM users");
$userrow = $users->fetch(PDO::FETCH_ASSOC);
$userCount = $userrow['count'];

// عدد الحيوانات المتاحة
$availablePets = $connect->query("SELECT COUNT(*) AS count FROM animals WHERE status = 'Available'");
$availablePetsRow = $availablePets->fetch(PDO::FETCH_ASSOC);
$availablePetsCount = $availablePetsRow['count'];

// عدد الطلبات المعلقة
$pendingRequests = $connect->query("SELECT COUNT(*) AS count FROM adoption_requests WHERE status = 'Pending'");
$pendingRequestsRow = $pendingRequests->fetch(PDO::FETCH_ASSOC);
$pendingRequestsCount = $pendingRequestsRow['count'];

// عدد الرسائل
$messages = $connect->query("SELECT COUNT(*) AS count FROM contact_messages");
$messagesRow = $messages->fetch(PDO::FETCH_ASSOC);
$messagesCount = $messagesRow['count'];

// Adoption Statistics
$totalAdoptions = $connect->query("SELECT COUNT(*) AS count FROM animals WHERE status = 'Adopted'")->fetch(PDO::FETCH_ASSOC)['count'];
$thisMonthAdoptions = $connect->query("SELECT COUNT(*) AS count FROM animals WHERE status = 'Adopted' AND DATE_FORMAT(adopted_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')")->fetch(PDO::FETCH_ASSOC)['count'];
$reservedPets = $connect->query("SELECT COUNT(*) AS count FROM animals WHERE status = 'Reserved'")->fetch(PDO::FETCH_ASSOC)['count'];
$totalRequests = $connect->query("SELECT COUNT(*) AS count FROM adoption_requests")->fetch(PDO::FETCH_ASSOC)['count'];

// Recent Adoptions
$recentAdoptions = $connect->query("
    SELECT 
        a.name as animal_name,
        a.type,
        a.adopted_at,
        u.name as adopter_name,
        u.image as adopter_image
    FROM animals a
    LEFT JOIN users u ON a.adopted_by = u.id
    WHERE a.status = 'Adopted'
    ORDER BY a.adopted_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../includes/head.php'; ?>
<body>

<div class="wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <div class="content">
        <?php include __DIR__ . '/../includes/admin_navbar.php'; ?>

        <div>
            <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Overview</h2>

            <!-- Main Statistics -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-center shadow-sm" style="border-left: 5px solid #E7A769;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-muted mb-0">Total Users</h6>
                                <i class="bi bi-people-fill" style="font-size: 2rem; color: #E7A769;"></i>
                            </div>
                            <h2 class="fw-bold mb-0" style="color: #5C3A21;"><?php echo $userCount ?></h2>
                            <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-sm btn-outline-secondary mt-3">View All</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-center shadow-sm" style="border-left: 5px solid #28a745;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-muted mb-0">Available Pets</h6>
                                <i class="bi bi-heart-fill" style="font-size: 2rem; color: #28a745;"></i>
                            </div>
                            <h2 class="fw-bold mb-0" style="color: #28a745;"><?php echo $availablePetsCount ?></h2>
                            <a href="<?= BASE_URL ?>/admin/availablepets.php" class="btn btn-sm btn-outline-success mt-3">View Pets</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-center shadow-sm" style="border-left: 5px solid #ffc107;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-muted mb-0">Pending Requests</h6>
                                <i class="bi bi-clock-fill" style="font-size: 2rem; color: #ffc107;"></i>
                            </div>
                            <h2 class="fw-bold mb-0" style="color: #ffc107;"><?php echo $pendingRequestsCount ?></h2>
                            <a href="<?= BASE_URL ?>/admin/requests.php" class="btn btn-sm btn-outline-warning mt-3">View Requests</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-center shadow-sm" style="border-left: 5px solid #17a2b8;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-muted mb-0">Messages</h6>
                                <i class="bi bi-envelope-fill" style="font-size: 2rem; color: #17a2b8;"></i>
                            </div>
                            <h2 class="fw-bold mb-0" style="color: #17a2b8;"><?php echo $messagesCount ?></h2>
                            <a href="<?= BASE_URL ?>/admin/message.php" class="btn btn-sm btn-outline-info mt-3">View Messages</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adoption Statistics -->
            <div class="row g-4 mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header" style="background-color: #5C3A21; color: white;">
                            <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Adoption Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="p-3" style="background: linear-gradient(135deg, #dc3545, #c82333); border-radius: 10px; color: white;">
                                        <i class="bi bi-heart-fill" style="font-size: 2.5rem;"></i>
                                        <h3 class="mt-2"><?php echo $totalAdoptions; ?></h3>
                                        <p class="mb-0">Total Adoptions</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3" style="background: linear-gradient(135deg, #28a745, #218838); border-radius: 10px; color: white;">
                                        <i class="bi bi-calendar-check-fill" style="font-size: 2.5rem;"></i>
                                        <h3 class="mt-2"><?php echo $thisMonthAdoptions; ?></h3>
                                        <p class="mb-0">This Month</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3" style="background: linear-gradient(135deg, #ffc107, #e0a800); border-radius: 10px; color: white;">
                                        <i class="bi bi-hourglass-split" style="font-size: 2.5rem;"></i>
                                        <h3 class="mt-2"><?php echo $reservedPets; ?></h3>
                                        <p class="mb-0">Reserved Pets</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3" style="background: linear-gradient(135deg, #17a2b8, #138496); border-radius: 10px; color: white;">
                                        <i class="bi bi-file-earmark-text-fill" style="font-size: 2.5rem;"></i>
                                        <h3 class="mt-2"><?php echo $totalRequests; ?></h3>
                                        <p class="mb-0">Total Requests</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Adoptions -->
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header" style="background-color: #E7A769; color: #5C3A21;">
                            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Adoptions</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentAdoptions)): ?>
                                <p class="text-muted text-center mb-0">No recent adoptions yet.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($recentAdoptions as $adoption): ?>
                                    <div class="list-group-item d-flex align-items-center">
                                        <?php if (!empty($adoption['adopter_image']) && file_exists(UPLOADS_PATH . '/' . $adoption['adopter_image'])): ?>
                                            <img src="<?= BASE_URL ?>/uploads/<?php echo htmlspecialchars($adoption['adopter_image']); ?>" 
                                                 alt="Adopter" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px; background: linear-gradient(135deg, #E7A769, #d49659); color: white; font-weight: bold;">
                                                <?php echo strtoupper(substr($adoption['adopter_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <i class="bi bi-heart-fill text-danger"></i> 
                                                <strong><?php echo htmlspecialchars($adoption['animal_name']); ?></strong>
                                                <span class="badge bg-info"><?php echo $adoption['type']; ?></span>
                                            </h6>
                                            <small class="text-muted">
                                                Adopted by <strong><?php echo htmlspecialchars($adoption['adopter_name'] ?? 'Unknown'); ?></strong> 
                                                on <?php echo date('M d, Y - h:i A', strtotime($adoption['adopted_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="<?= BASE_URL ?>/admin/adoption_history.php" class="btn btn-sm" style="background-color: #5C3A21; color: white;">
                                        <i class="bi bi-arrow-right-circle"></i> View Full History
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
