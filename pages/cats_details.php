<?php require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $connect->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->execute([$id]);
    $dog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dog) {
        echo "Cat not found!";
        exit;
    }
} else {
    echo "No Cat selected!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TailMate - Cat Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#f5f0e6] text-[#5d4037]">
    <?php include __DIR__ . '/../includes/user_navbar.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <!-- صورة الحيوان -->
        <section class="mb-8 text-center">
            <div class="w-1/2 rounded-xl mx-auto">
                <?php if (!empty($dog['image'])) { ?>
                    <img src="<?= BASE_URL ?>/assets/images/<?php echo $dog['image']; ?>"
                        class="w-1/2 rounded-xl mx-auto">




                <?php } else { ?>
                    <p class="p-6">No image available</p>
                <?php } ?>
            </div>
        </section>

        <!-- بيانات الحيوان -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8" id="animalSection">
            <!-- Basic Information -->
            <div class="lg:col-span-2">
                <div class="bg-white border-[#d7ccc8] border rounded-lg p-6">
                    <h2 class="text-3xl font-bold text-[#5d4037] mb-4">
                        <?php echo htmlspecialchars($dog['name']); ?>
                    </h2>
                    <hr>
                    <hr>
                    <p class="text-lg leading-relaxed mb-4">
                        <?php echo nl2br(htmlspecialchars($dog['description'])); ?>
                    </p>
                    <hr>
                    <hr>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="space-y-2">
                            <h3 class="font-semibold text-lg"><strong>Basic Info :</strong></h3>
                            <ul class="list-disc list-inside space-y-1 text-[#5d4037]/90">
                                <li>Sex: <?php echo $dog['sex']; ?></li>
                                <li>Age: <?php echo $dog['age'];
                                            echo $dog['size'] == 'Small' ? ' Months' : ' Years'; ?></li>
                                <li>Size: <?php echo $dog['size']; ?></li>
                                <li>Type: <?php echo $dog['type']; ?></li>
                                <li>Area: <?php echo $dog['area']; ?></li>
                            </ul>
                        </div>

                        <div class="space-y-2">
                            <h3 class="font-semibold text-lg"><strong>Extra Info :</strong></h3>
                            <ul class="list-disc list-inside space-y-1 text-[#5d4037]/90">
                                <li>Vaccination: <?php echo $dog['vaccination']; ?></li>
                                <li>Health Status: <?php echo $dog['health_status']; ?></li>
                                <li>Is Neutered: <?php echo $dog['is_neutered'] ? "Yes" : "No"; ?></li>
                                <li>Status: <?php echo $dog['status']; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adoption Info -->
            <div>
                <div class="bg-white border-[#d7ccc8] border rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4"><strong>Adoption Info</strong></h3>
                    <hr>
                    <hr>
                    <p><strong>ID:</strong> <?php echo $dog['id']; ?></p>
                    <p><strong>Name:</strong> <?php echo $dog['name']; ?></p>
                    <p><strong>Available:</strong> <?php echo $dog['status'] == 'Available' ? 'Yes' : 'No'; ?></p>
                    <a href="<?= BASE_URL ?>/pages/adoptionform.php?animal_id=<?php echo $dog['id']; ?>"
                        class="block mt-4 w-full text-center bg-[#8d6e63] hover:bg-[#6d4c41] text-white py-3 rounded-lg font-semibold transition-colors">
                        🐾 Apply to Adopt
                    </a>

                    <a href="<?= BASE_URL ?>/pages/cats.php"
                        class="block mt-4 text-center text-[#5d4037] hover:underline">
                        &larr; Back to Listings
                    </a>

                </div>
            </div>
        </section>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>