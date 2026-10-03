<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php'; // الاتصال بقاعدة البيانات

// حماية الداشبورد للادمن فقط
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

// معالجة حذف حيوان أليف
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $petId = (int) $_GET['id'];
    $delete = $connect->prepare("DELETE FROM animals WHERE id = ?");
    $delete->execute([$petId]);
    header("Location: " . BASE_URL . "/admin/availablepets.php");
    exit();
}

$query = $connect->query("SELECT * FROM animals ORDER BY status ASC, id DESC");
$pets = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<?php
include __DIR__ . '/../includes/head.php';
?>
<body>

  <div class="wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <!-- Content -->
    <div class="content">
      <?php include __DIR__ . '/../includes/admin_navbar.php'; ?>

      <!-- Content Area -->
      <div>
        <h2 class="mb-4">Pets</h2>

        <!-- Table -->
        <table class="table table-bordered align-middle">
          <thead style="background-color: #E7A769; color: #5C3A21;">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Type</th>
              <th>Gender</th>
              <th>Age</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($pets)) { ?>
              <?php foreach ($pets as $pet){ ?>
              <tr>
                <td><?php echo $pet['id']; ?></td>
                <td><?php echo $pet['name']; ?></td>
                <td><?php echo $pet['type']; ?></td>
                <td><?php echo $pet['sex']; ?></td>
                <td><?php echo $pet['age']; ?></td>
                <td>
                  <?php 
                  $badgeClass = 'bg-secondary';
                  if ($pet['status'] == 'Available') {
                      $badgeClass = 'bg-success';
                  } elseif ($pet['status'] == 'Reserved') {
                      $badgeClass = 'bg-warning text-dark';
                  } elseif ($pet['status'] == 'Adopted') {
                      $badgeClass = 'bg-info';
                  }
                  ?>
                  <span class="badge <?php echo $badgeClass; ?>"><?php echo $pet['status'] ?? 'Available'; ?></span>
                </td>
                <td>
                  <a href="<?= BASE_URL ?>/admin/create_pet.php?id=<?php echo $pet['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                  <a href="<?= BASE_URL ?>/admin/availablepets.php?action=delete&id=<?php echo $pet['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this pet?')">Delete</a>
                </td>
              </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="7" class="text-center">No pets available</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
