<?php require_once __DIR__ . '/../config/config.php';
session_start();
require_once __DIR__ . '/../config/connection.php';

$query = ("SELECT * FROM animals WHERE type='Cat' AND status IN ('Available', 'Reserved')");


$sex = $_GET['sex'] ?? '';
$size = $_GET['size'] ?? '';
$city = $_GET['area'] ?? '';
$age = $_GET['age'] ?? '';
$is_neutered = $_GET['is_neutered'] ?? '';
$vaccination = $_GET['vaccination_status'] ?? '';




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
        $query .= " AND age BETWEEN 0 AND 2 AND size='Small'";
    } elseif ($age == '2') {
        $query .=  " AND age BETWEEN 3 AND 4 AND size='Small'";
    } elseif ($age == '3') {
        $query .=  " AND age BETWEEN 5 AND 6 AND size='Small'";
    } elseif ($age == '4') {
        $query .=  " AND age BETWEEN 7 AND 8 AND size='Medium'";
    } elseif ($age == '5') {
        $query .=  " AND age BETWEEN 9 AND 10 AND size='Medium'";
    } elseif ($age == '6') {
        $query .=  " AND age = 11 AND size='Medium'";
    } elseif ($age == '7') {
        $query .= " AND age BETWEEN 1 AND 2 AND size='Large'";
    } elseif ($age == '8') {
        $query .=  " AND age BETWEEN 3 AND 4 AND size='Large'";
    } elseif ($age == '9') {
        $query .=  " AND age >= 5 AND size='Large'";
    }
}
if (!empty($is_neutered)) {
    $query .= "  AND is_neutered ='$is_neutered'";
}
if (!empty($vaccination)) {
    $query .= "  AND vaccination ='$vaccination'";
}

$animals = $connect->query("$query");

if ($animals->rowCount() == 0) {
    echo "<p style='text-align:center; color:#5C3A21; font-size:20px; margin-top:40px;'> No cats match your filters</p>";
}



?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cats - TailMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 0 40px;
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

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1000px;
            margin: auto;
        }

        .footer-column {
            flex: 1 1 200px;
            margin: 10px;
        }

        .footer-column h4 {
            margin-bottom: 10px;
        }

        .footer-column a {
            display: block;
            color: #333;
            text-decoration: none;
            margin: 5px 0;
            font-size: 14px;
        }

        .footer-column a:hover {
            text-decoration: underline;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .social-icons .icon {
            text-align: center;
        }

        .social-icons img {
            width: 24px;
            height: 24px;
            display: block;
            margin: 0 auto 5px;
        }

        .logo {
            margin-top: 20px;
            text-align: right;
        }

        .logo img {
            height: 60px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>

    <h1>Find Your Perfect Cat</h1>

    <form method="GET" class="filters">
        <select name="sex" id="sex">
            <option value="" disabled selected>Sex</option>
            <option value="Male" <?= ($sex == 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($sex == 'Female') ? 'selected' : '' ?>>Female</option>
        </select>

        <select name="age" id="age">
            <option value="" disabled selected>Age</option>
            <optgroup label="Months">
                <option value="1" <?= ($age == '1') ? 'selected' : '' ?>>0-2 months</option>
                <option value="2" <?= ($age == '2') ? 'selected' : '' ?>>3-4 months</option>
                <option value="3" <?= ($age == '3') ? 'selected' : '' ?>>5-6 months</option>
                <option value="4" <?= ($age == '4') ? 'selected' : '' ?>>7-8 months</option>
                <option value="5" <?= ($age == '5') ? 'selected' : '' ?>>9-10 months</option>
                <option value="6" <?= ($age == '6') ? 'selected' : '' ?>>11 months</option>
            </optgroup>
            <optgroup label="Years">
                <option value="7" <?= ($age == '7') ? 'selected' : '' ?>>1-2 years</option>
                <option value="8" <?= ($age == '8') ? 'selected' : '' ?>>3-4 years</option>
                <option value="9" <?= ($age == '9') ? 'selected' : '' ?>>5+ years</option>
            </optgroup>
        </select>

        <select name="is_neutered">
            <option value="" disabled selected>Spayed/Neutered</option>
            <option value="Yes" <?= ($is_neutered == 'Yes') ? 'selected' : '' ?>>Yes</option>
            <option value="No" <?= ($is_neutered == 'No') ? 'selected' : '' ?>>No</option>
        </select>

        <select name="area">
            <option value="" disabled selected>City</option>
            <option value="Cairo" <?= ($city == 'Cairo') ? 'selected' : '' ?>>Cairo</option>
            <option value="Giza" <?= ($city == 'Giza') ? 'selected' : '' ?>>Giza</option>
            <option value="Alexandria" <?= ($city == 'Alexandria') ? 'selected' : '' ?>>Alexandria</option>
            <option value="Kafr Elsheikh" <?= ($city == 'Kafr Elsheikh') ? 'selected' : '' ?>>Kafr Elsheikh</option>
            <option value="Bani Suief" <?= ($city == 'Bani Suief') ? 'selected' : '' ?>>Bani Suief</option>
        </select>

        <select name="vaccination_status" id="vaccination_status">
            <option value="" disabled selected>Vaccination</option>
            <option value="yes" <?= ($vaccination == 'yes') ? 'selected' : '' ?>>Vaccinated</option>
            <option value="no" <?= ($vaccination == 'no') ? 'selected' : '' ?>>Not Vaccinated</option>
        </select>
        <button type="submit">Filter</button>
        <button type="button" onclick="window.location='<?= BASE_URL ?>/pages/cats.php'">Clear</button>
    </form>

    <div class="grid">
        <?php foreach ($animals as $animal): ?>
            <div class="card <?= ($animal['status'] == 'Reserved') ? 'reserved' : '' ?>">
                <img class="w-1/2 mx-auto" src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($animal['image'] ?? '') ?>" alt="<?= htmlspecialchars($animal['name'] ?? '') ?>">
                <div class="info">
                    <h3><?= htmlspecialchars($animal['name'] ?? '') ?></h3>
                    <p><?= htmlspecialchars($animal['area'] ?? '') ?></p>
                    <?php if ($animal['status'] != 'Reserved'): ?>
                        <a href="<?= BASE_URL ?>/pages/cats_details.php?id=<?= $animal['id'] ?>" class="btn">View Details</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
<?php include __DIR__ . '/../includes/footer.php'; ?>

</html>