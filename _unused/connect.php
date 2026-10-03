<?php
try {
    $connect = new PDO("mysql:host=localhost;dbname=finalproject", "root", "");

    // $data = $connect->query("SELECT id, name, image, city FROM animal");
    // $animals = $data->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>