<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// جلب سجل التبني الكامل
$query = $connect->query("
    SELECT 
        a.id as animal_id,
        a.name as animal_name,
        a.type,
        a.age,
        a.image,
        a.adopted_at,
        u.id as user_id,
        u.name as adopter_name,
        u.email as adopter_email,
        u.phone as adopter_phone,
        u.image as adopter_image,
        ar.id as request_id,
        ar.status,
        ar.created_at as request_date
    FROM animals a
    LEFT JOIN users u ON a.adopted_by = u.id
    LEFT JOIN adoption_requests ar ON ar.animal_id = a.id AND ar.user_id = u.id AND ar.status = 'Completed'
    WHERE a.status = 'Adopted'
    ORDER BY a.adopted_at DESC
");
$adoptions = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../includes/head.php'; ?>
<body>

<div class="wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <div class="content">
        <?php include __DIR__ . '/../includes/admin_navbar.php'; ?>

        <div>
            <h2 class="mb-4"><i class="bi bi-heart-fill text-danger"></i> Adoption History</h2>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card" style="background: linear-gradient(135deg, #28a745, #218838); color: white;">
                        <div class="card-body text-center">
                            <h3><?php echo count($adoptions); ?></h3>
                            <p class="mb-0"><i class="bi bi-check-circle"></i> Total Adoptions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white;">
                        <div class="card-body text-center">
                            <?php 
                            $thisMonth = count(array_filter($adoptions, function($a) {
                                return date('Y-m', strtotime($a['adopted_at'])) == date('Y-m');
                            }));
                            ?>
                            <h3><?php echo $thisMonth; ?></h3>
                            <p class="mb-0"><i class="bi bi-calendar-check"></i> This Month</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card" style="background: linear-gradient(135deg, #E7A769, #d49659); color: white;">
                        <div class="card-body text-center">
                            <?php 
                            $dogs = count(array_filter($adoptions, fn($a) => $a['type'] == 'Dog'));
                            $cats = count(array_filter($adoptions, fn($a) => $a['type'] == 'Cat'));
                            ?>
                            <h3><?php echo $dogs; ?> / <?php echo $cats; ?></h3>
                            <p class="mb-0"><i class="bi bi-diagram-3"></i> Dogs / Cats</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($adoptions)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No completed adoptions yet.
                </div>
            <?php else: ?>

            <!-- Adoption Cards -->
            <div class="row">
                <?php foreach ($adoptions as $adoption): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header" style="background-color: #E7A769; color: #5C3A21;">
                            <h5 class="mb-0">
                                <i class="bi bi-heart-fill"></i> 
                                <?php echo htmlspecialchars($adoption['animal_name']); ?> 
                                <span class="badge bg-light text-dark"><?php echo $adoption['type']; ?></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Animal Image -->
                                <div class="col-md-4 text-center">
                                    <?php if (!empty($adoption['image'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/images/<?php echo htmlspecialchars($adoption['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($adoption['animal_name']); ?>" 
                                             class="img-fluid rounded" 
                                             style="max-height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <p class="mt-2 mb-0"><strong>Age:</strong> <?php echo $adoption['age']; ?> years</p>
                                </div>

                                <!-- Adoption Details -->
                                <div class="col-md-8">
                                    <h6 class="text-primary mb-3">
                                        <i class="bi bi-person-circle"></i> Adopted By:
                                    </h6>
                                    
                                    <div class="d-flex align-items-center mb-3">
                                        <?php if (!empty($adoption['adopter_image']) && file_exists(UPLOADS_PATH . '/' . $adoption['adopter_image'])): ?>
                                            <img src="<?= BASE_URL ?>/uploads/<?php echo htmlspecialchars($adoption['adopter_image']); ?>" 
                                                 alt="Adopter" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px; background: linear-gradient(135deg, #E7A769, #d49659); color: white; font-weight: bold;">
                                                <?php echo strtoupper(substr($adoption['adopter_name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($adoption['adopter_name']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($adoption['adopter_email']); ?></small>
                                        </div>
                                    </div>

                                    <p class="mb-1">
                                        <i class="bi bi-telephone-fill text-success"></i> 
                                        <strong>Phone:</strong> <?php echo htmlspecialchars($adoption['adopter_phone'] ?? 'N/A'); ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-calendar-event text-info"></i> 
                                        <strong>Request Date:</strong> <?php echo date('M d, Y', strtotime($adoption['request_date'])); ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="bi bi-check-circle-fill text-success"></i> 
                                        <strong>Adopted On:</strong> <?php echo date('M d, Y - h:i A', strtotime($adoption['adopted_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="<?= BASE_URL ?>/admin/requests.php" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text"></i> View Request #<?php echo $adoption['request_id']; ?>
                                </a>
                                <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-person"></i> View User Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
