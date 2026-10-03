<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php'; 

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// جلب المستخدمين مع عدد طلبات التبني
$stmt = $connect->query("
    SELECT 
        u.*,
        COUNT(DISTINCT ar.id) as total_requests,
        COUNT(DISTINCT CASE WHEN ar.status = 'Completed' THEN ar.id END) as completed_adoptions,
        MAX(ar.created_at) as last_request_date
    FROM users u
    LEFT JOIN adoption_requests ar ON u.id = ar.user_id
    GROUP BY u.id
    ORDER BY u.id DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../includes/head.php'; ?>
<body>

  <div class="wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <!-- Content -->
    <div class="content">
      <?php include __DIR__ . '/../includes/admin_navbar.php'; ?>

      <!-- Content Area -->
      <div>
        <h2 class="mb-4"><i class="bi bi-people"></i> Users Management</h2>

        <!-- Statistics Cards -->
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="card" style="background: linear-gradient(135deg, #E7A769, #d49659); color: white;">
              <div class="card-body text-center">
                <h3><?php echo count($users); ?></h3>
                <p class="mb-0">Total Users</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card" style="background: linear-gradient(135deg, #28a745, #218838); color: white;">
              <div class="card-body text-center">
                <h3><?php echo count(array_filter($users, fn($u) => $u['is_admin'] == 1)); ?></h3>
                <p class="mb-0">Admins</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white;">
              <div class="card-body text-center">
                <h3><?php echo count(array_filter($users, fn($u) => $u['completed_adoptions'] > 0)); ?></h3>
                <p class="mb-0">Adopters</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <table class="table table-bordered align-middle">
          <thead style="background-color: #E7A769; color: #5C3A21;">
            <tr>
              <th>ID</th>
              <th>Profile</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Adoptions</th>
              <th>Last Activity</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo $user['id']; ?></td>
              <td>
                <?php if (!empty($user['image']) && file_exists(UPLOADS_PATH . '/' . $user['image'])): ?>
                  <img src="<?= BASE_URL ?>/uploads/<?php echo htmlspecialchars($user['image']); ?>" 
                       alt="Profile" 
                       style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                  <div style="width: 40px; height: 40px; border-radius: 50%; background: #E7A769; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                  </div>
                <?php endif; ?>
              </td>
              <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
              <td>
                <?php if ($user['is_admin'] == 1): ?>
                  <span class="badge bg-danger">Admin</span>
                <?php else: ?>
                  <span class="badge bg-primary">User</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-info"><?php echo $user['total_requests']; ?> Requests</span>
                <?php if ($user['completed_adoptions'] > 0): ?>
                  <br><span class="badge bg-success mt-1"><?php echo $user['completed_adoptions']; ?> Adopted</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($user['last_request_date']): ?>
                  <?php echo date('M d, Y', strtotime($user['last_request_date'])); ?>
                <?php else: ?>
                  <span class="text-muted">No activity</span>
                <?php endif; ?>
              </td>
              <td>
                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewUser<?php echo $user['id']; ?>">
                  <i class="bi bi-eye"></i> View
                </button>
                <a href="<?= BASE_URL ?>/pages/notifications.php" class="btn btn-sm btn-primary">
                  <i class="bi bi-chat"></i> Message
                </a>
              </td>
            </tr>

            <!-- View User Modal -->
            <div class="modal fade" id="viewUser<?php echo $user['id']; ?>" tabindex="-1">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header" style="background-color: #E7A769;">
                    <h5 class="modal-title" style="color: #5C3A21;">
                      <i class="bi bi-person-circle"></i> User Details - <?php echo htmlspecialchars($user['name']); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-4 text-center">
                        <?php if (!empty($user['image']) && file_exists(UPLOADS_PATH . '/' . $user['image'])): ?>
                          <img src="<?= BASE_URL ?>/uploads/<?php echo htmlspecialchars($user['image']); ?>" 
                               alt="Profile" 
                               style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #E7A769;">
                        <?php else: ?>
                          <div style="width: 150px; height: 150px; margin: 0 auto; border-radius: 50%; background: linear-gradient(135deg, #E7A769, #d49659); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 4rem;">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                          </div>
                        <?php endif; ?>
                        <h4 class="mt-3"><?php echo htmlspecialchars($user['name']); ?></h4>
                        <?php if ($user['is_admin'] == 1): ?>
                          <span class="badge bg-danger">Administrator</span>
                        <?php else: ?>
                          <span class="badge bg-primary">Regular User</span>
                        <?php endif; ?>
                      </div>
                      <div class="col-md-8">
                        <h5 class="mb-3">Contact Information</h5>
                        <p><i class="bi bi-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><i class="bi bi-telephone"></i> <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                        
                        <hr>
                        <h5 class="mb-3">Adoption Statistics</h5>
                        <p><i class="bi bi-clipboard-data"></i> <strong>Total Requests:</strong> <?php echo $user['total_requests']; ?></p>
                        <p><i class="bi bi-heart-fill text-success"></i> <strong>Completed Adoptions:</strong> <?php echo $user['completed_adoptions']; ?></p>
                        <?php if ($user['last_request_date']): ?>
                          <p><i class="bi bi-calendar"></i> <strong>Last Request:</strong> <?php echo date('F d, Y', strtotime($user['last_request_date'])); ?></p>
                        <?php endif; ?>
                        
                        <?php
                        // جلب حيواناته المتبناة
                        $adoptedQuery = $connect->prepare("
                            SELECT a.name, a.type, ar.created_at as adopted_date 
                            FROM animals a 
                            JOIN adoption_requests ar ON a.id = ar.animal_id 
                            WHERE ar.user_id = ? AND ar.status = 'Completed'
                            ORDER BY ar.created_at DESC
                        ");
                        $adoptedQuery->execute([$user['id']]);
                        $adoptedPets = $adoptedQuery->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        
                        <?php if (!empty($adoptedPets)): ?>
                          <hr>
                          <h5 class="mb-3">Adopted Pets</h5>
                          <ul class="list-group">
                            <?php foreach ($adoptedPets as $pet): ?>
                              <li class="list-group-item">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <strong><?php echo htmlspecialchars($pet['name']); ?></strong> (<?php echo $pet['type']; ?>)
                                <br><small class="text-muted">Adopted on <?php echo date('M d, Y', strtotime($pet['adopted_date'])); ?></small>
                              </li>
                            <?php endforeach; ?>
                          </ul>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
