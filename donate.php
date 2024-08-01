<?php
session_start();
include 'php/database.php';

// Mengatur zona waktu ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'giver') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $giver_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $location = $_POST['location'];

    // Get expiration date and pickup duration
    $expires_at = $_POST['expires_at']; // Y-m-d H:i:s format
    $pickup_duration = $_POST['pickup_duration']; // in hours

    // Calculate the pickup deadline
    $pickup_deadline = date('Y-m-d H:i:s', strtotime("+$pickup_duration hours"));

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["food_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["food_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["food_image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["food_image"]["tmp_name"], $target_file)) {
            // Insert data into the donations table including expires_at and pickup_deadline
            $sql = "INSERT INTO donations (giver_id, title, description, quantity, location, food_image, expires_at, pickup_deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssss", $giver_id, $title, $description, $quantity, $location, $target_file, $expires_at, $pickup_deadline);

            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Food</title>
    <!-- <link rel="stylesheet" href="css/styles.css"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }

        .container form {
            display: grid;
            gap: 10px;
        }

        .container form input[type="text"],
        .container form input[type="number"],
        .container form input[type="datetime-local"],
        .container form textarea,
        .container form button {
            width: calc(100% - 20px);
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .container form input[type="file"] {
            padding: 8px 0;
            font-size: 14px;
        }

        .container form button {
            background-color: #FFA500;
            color: white;
            border: none;
            cursor: pointer;
        }

        .container form button:hover {
            background-color: #FF7A00;
        }
    </style>
</head>

<body>
    <div class="container">
        <form action="donate.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Nama Makanan/Minuman" required>
            <textarea name="description" placeholder="Deskripsi" required></textarea>
            <input type="number" name="quantity" placeholder="Jumlah" required>
            <input type="text" name="location" placeholder="Alamat" required>
            <input type="file" name="food_image" accept="image/*" required>

            <!-- New inputs for expiration and pickup duration -->
            <label for="expires_at">Waktu Kadaluarsa (Tanggal dan Jam):</label>
            <input type="datetime-local" name="expires_at" required>

            <label for="pickup_duration">Durasi Maksimal Pengambilan (jam):</label>
            <input type="number" name="pickup_duration" placeholder="Jam" required>

            <button type="submit">Donasi</button>
        </form>
    </div>
</body>

</html>