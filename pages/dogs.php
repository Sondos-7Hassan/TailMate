<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

$query = ("SELECT id, name, image, area, status FROM animals WHERE type='Dog' AND status IN ('Available', 'Reserved')");

$sex = $_GET['sex'] ?? '';
$size = $_GET['size'] ?? '';
$city = $_GET['city'] ?? '';
$age = $_GET['age'] ?? '';
$is_neutered = $_GET['is_neutered'] ?? '';
$vaccination = $_GET['vaccination'] ?? '';

if (!empty($sex)) {
  $query .= " AND sex = '$sex'";
}
if (!empty($size)) {
  $query .= " AND size = '$size'";
}
if (!empty($city)) {
  $query .= " AND area = '$city'";
}
if (!empty($age)) {
  if ($age == '1') {
    $query .= " AND age BETWEEN 0 AND 2";
  } elseif ($age == '2') {
    $query .=  " AND age BETWEEN 3 AND 4";
  } elseif ($age == '3') {
    $query .=  " AND age BETWEEN 5 AND 6";
  } elseif ($age == '4') {
    $query .= " AND age BETWEEN 7 AND 8";
  } elseif ($age == '5') {
    $query .=  " AND age >= 9";
  }
}
if (!empty($is_neutered)) {
  $query .= " AND is_neutered = '$is_neutered'";
}
if (!empty($vaccination)) {
  $query .= " AND vaccination = '$vaccination'";
}

$animals = $connect->query("$query");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dogs - TailMate</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #FFF6EC;
    }

    /* Navbar */
    .navbar {
      background: #E7A769;
      padding: 15px 30px;
      box-shadow: 0 2px 6px #5C3A21;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar a {
      color: #5C3A21;
      text-decoration: none;
      margin: 0 15px;
      font-weight: bold;
      transition: 0.3s;
    }

    .navbar a:hover {
      color: #3e2414;
    }

    .navbar .logo img {
      height: 50px;
    }

    h1 {
      text-align: center;
      color: #5C3A21;
      margin: 40px 0 30px;
      font-size: 48px;
    }

    .filters {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      margin-bottom: 30px;
      padding: 0 20px;
    }

    .filters select,
    .filters button {
      padding: 10px 15px;
      border-radius: 6px;
      border: 1px solid #5C3A21;
      font-size: 14px;
      color: #5C3A21;
    }

    .filters button {
      background: #E7A769;
      color: white;
      border: none;
      cursor: pointer;
    }

    .filters button:hover {
      background: #DFA569;
    }


    .grid {
      display: grid;
      /* grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); */
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      padding: 0 40px;
      /* مسافة يمين وشمال */
    }

    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      text-align: center;
      transition: transform 0.2s;
      position: relative;
    }

    .card:hover {
      transform: translateY(-6px);
    }

    .card.reserved {
      opacity: 0.7;
    }

    .card.reserved::before {
      content: 'RESERVED';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 20px 40px;
      font-size: 24px;
      font-weight: bold;
      border-radius: 10px;
      z-index: 10;
      letter-spacing: 2px;
    }

    .card.reserved img {
      filter: brightness(0.5);
    }

    .card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      display: block;
    }

    .card .info {
      padding: 15px;
      text-align: center;
    }

    .card .info h3 {
      margin: 0 0 10px;
      font-size: 20px;
      color: #5C3A21;
    }

    .card .info p {
      margin: 0 0 15px;
      color: #5C3A21;
      font-size: 14px;
    }

    .btn {
      display: inline-block;
      padding: 10px 15px;
      background: #E7A769;
      color: white;
      font-size: 14px;
      font-weight: bold;
      border-radius: 6px;
      text-decoration: none;
      transition: background 0.2s;
    }

    .btn:hover {
      background: #DFA569;
    }

    footer {
      background-color: #d1bfa4;

      padding: 40px 20px;
      margin-top: 50px;

      width: 100%;
      box-sizing: border-box;
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../includes/user_navbar.php'; ?>

  <h1>Find Your Perfect Dog</h1>

  <form method="GET">
    <div class="filters">
      <select name="sex" id="sex">
        <option value="" disabled selected>Sex</option>
        <option value="Male" <?= ($sex == 'Male') ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= ($sex == 'Female') ? 'selected' : '' ?>>Female</option>
      </select>

      <select name="size" id="size">
        <option value="" disabled selected>Size</option>
        <option value="Small" <?= ($size == 'Small') ? 'selected' : '' ?>>Small</option>
        <option value="Medium" <?= ($size == 'Medium') ? 'selected' : '' ?>>Medium</option>
        <option value="Large" <?= ($size == 'Large') ? 'selected' : '' ?>>Large</option>
      </select>

      <select name="age" id="age">
        <option value="" disabled selected>Age</option>
        <option value="1" <?= ($age == '1') ? 'selected' : '' ?>>0-2 years</option>
        <option value="2" <?= ($age == '2') ? 'selected' : '' ?>>3-4 years</option>
        <option value="3" <?= ($age == '3') ? 'selected' : '' ?>>5-6 years</option>
        <option value="4" <?= ($age == '4') ? 'selected' : '' ?>>7-8 years</option>
        <option value="5" <?= ($age == '5') ? 'selected' : '' ?>>9+ years</option>
      </select>

      <select name="is_neutered">
        <option value="" disabled selected>Spayed/Neutered</option>
        <option value="Yes" <?= ($is_neutered == 'Yes') ? 'selected' : '' ?>>Yes</option>
        <option value="No" <?= ($is_neutered == 'No') ? 'selected' : '' ?>>No</option>
      </select>

      <select name="city">
        <option value="" disabled selected>City</option>
        <option value="Cairo" <?= ($city == 'Cairo') ? 'selected' : '' ?>>Cairo</option>
        <option value="Giza" <?= ($city == 'Giza') ? 'selected' : '' ?>>Giza</option>
        <option value="Alexandria" <?= ($city == 'Alexandria') ? 'selected' : '' ?>>Alexandria</option>
        <option value="Mansoura" <?= ($city == 'Mansoura') ? 'selected' : '' ?>>Mansoura</option>
      </select>

      <select name="vaccination" id="vaccination_status">
        <option value="" disabled selected>Vaccination</option>
        <option value="Fully vaccinated" <?= ($vaccination == 'Fully vaccinated') ? 'selected' : '' ?>>Fully Vaccinated</option>
        <option value="Almost Fully vaccinated" <?= ($vaccination == 'Almost Fully vaccinated') ? 'selected' : '' ?>>Partially Vaccinated</option>
        <option value="Unvaccinated" <?= ($vaccination == 'Unvaccinated') ? 'selected' : '' ?>>Not Vaccinated</option>
      </select>
      <button type="submit">Filter</button>
      <button type="reset" onclick="window.location='<?= BASE_URL ?>/pages/dogs.php'">Clear</button>
    </div>
  </form>

  <!-- عرض الحيوانات -->
  <div class="grid">
    <?php
    if ($animals->rowCount() > 0) {
      while ($row = $animals->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['status'] ?? 'Available';
        $reservedClass = ($status == 'Reserved') ? 'reserved' : '';
        
        echo "<div class='card $reservedClass'>";
        echo "<img src='" . BASE_URL . "/assets/images/" . htmlspecialchars($row['image'] ?? '') . "' alt='" . htmlspecialchars($row['name'] ?? '') . "'>";
        echo "<div class='info'>";
        echo "<h3>" . htmlspecialchars($row['name'] ?? '') . "</h3>";
        echo "<p>" . htmlspecialchars($row['area'] ?? '') . "</p>";
        
        if ($status != 'Reserved') {
          echo "<a class='btn' href='" . BASE_URL . "/pages/dogs_details.php?id=" . $row['id'] . "'>View Details</a>";
        }
        
        echo "</div>";
        echo "</div>";
      }
    } else {
      echo "<p style='text-align:center; color:#5C3A21; font-size:20px; margin-top:40px;'>No dogs found.</p>";
    }
    ?>
  </div>

</body>
<?php include __DIR__ . '/../includes/footer.php'; ?>

</html>