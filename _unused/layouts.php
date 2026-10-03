<?php
session_start();
require "connection.php"; // الاتصال بقاعدة البيانات

// حماية الداشبورد للادمن فقط
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

$users = $connect->query("SELECT COUNT(*) AS count FROM users");
$userrow = $users->fetch(PDO::FETCH_ASSOC);
$userCount = $userrow['count'];

$pendingrequests = $connect->query("SELECT COUNT(*) AS count FROM adoption_requests WHERE status = 'pending'");
$pendingrequestsrow = $pendingrequests->fetch(PDO::FETCH_ASSOC);
$pendingrequestsCount = $pendingrequestsrow['count'];

$pets = $connect->query("SELECT COUNT(*) AS count FROM pets");
$petsrow = $pets->fetch(PDO::FETCH_ASSOC);
$petsCount = $petsrow['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #5C3A21;
      color: white;
      padding: 15px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      margin: 10px 0;
      padding: 8px;
      border-radius: 5px;
    }
    .sidebar a:hover {
      background-color: #E7A769;
    }
    .content {
      flex-grow: 1;
      padding: 20px;
    }
    .navbar {
      background-color: #5C3A21;
      padding: 10px 20px;
      color: white;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Dashboard</h2>
    <a href="dashboard.php">Overview</a>
    <a href="users.php">Users</a>
    <a href="availablepets.php">Pets</a>
    <a href="create_pet.php">Add Pet</a>
    <a href="requests.php">Requests</a>
    <a href="logout.php">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <!-- Navbar -->
    <div class="navbar">
      <span>Welcome, Admin</span>
    </div>

    <!-- Page Content -->
    <div class="container mt-4">
      <h3>Overview</h3>
      <div class="row">
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <h5 class="card-title">Users</h5>
              <p class="card-text"><?php echo $userCount; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <h5 class="card-title">Pets</h5>
              <p class="card-text"><?php echo $petsCount?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <h5 class="card-title">Pending Requests</h5>
              <p class="card-text"><?php echo $pendingrequestsCount ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
