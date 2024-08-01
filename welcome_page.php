<?php
session_start();
include 'php/database.php';

// Cek jika user sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Donation</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        
    </style>
</head>

<body>
    <div class="container">
        <h1>Selamat Datang di Meal Deals</h1>
        <p>Bantu mengurangi pemborosan stok makanan berlebih dan donasikan kepada mereka yang membutuhkan.</p>
        <a href="register.php" class="btn">Register</a>
        <a href="login.php" class="btn">Login</a>
    </div>
</body>

</html>