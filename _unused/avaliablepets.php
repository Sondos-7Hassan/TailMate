<?php
session_start();
require "connection.php"; // الاتصال بقاعدة البيانات

// حماية الداشبورد للادمن فقط
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

$query = $connect->query("SELECT * FROM animals WHERE status = 'Available'");
$pets = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<?php
include 'head.php';
?>
<body>

  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>Dashboard</h2>
      <a href="dashboard.php">Overview</a>
      <a href="users.php">Users</a>
      <a href="avaliablepets.php">Pets</a>
      <a href="requests.php">Requests</a>
      <a href="create_pet.php">Add Pet</a>
      <a href="logout.php">Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
      <!-- Navbar -->
      <div class="navbar">
        <span>Welcome, Admin</span>
      </div>

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
              
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pets as $pet){ ?>
            <tr>
              <td><?php echo $pet['id']; ?></td>
              <td><?php echo $pet['name']; ?></td>
              <td><?php echo $pet['type']; ?></td>
              <td><?php echo $pet['sex']; ?></td>
              <td><?php echo $pet['age']; ?></td>
              <td><?php echo $pet['status']; ?></td>

              
              
            </tr>
            <?php } ?>

            <tr>
              
              
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
