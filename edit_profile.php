<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pengguna
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Proses update data pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $foundation_profile = $_POST['foundation_profile'];
    $profile_picture = $user['profile_picture']; // default to the current profile picture

    // Proses upload foto profil
    if ($_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $temp_name = $_FILES['profile_picture']['tmp_name'];
        $file_name = basename($_FILES['profile_picture']['name']);
        $upload_dir = 'uploads/profile_pictures/';
        $new_file_name = uniqid() . "-" . $file_name;

        if (move_uploaded_file($temp_name, $upload_dir . $new_file_name)) {
            // Delete old profile picture if exists
            if (!empty($user['profile_picture'])) {
                unlink($upload_dir . $user['profile_picture']);
            }
            $profile_picture = $new_file_name;
        } else {
            echo "Failed to upload file.";
            exit();
        }
    }

    // Pastikan foundation_profile diperlakukan sebagai string
    $foundation_profile = strval($foundation_profile);

    $sql = "UPDATE users SET username = ?, email = ?, phone = ?, foundation_profile = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $username, $email, $phone, $foundation_profile, $profile_picture, $user_id);

    if ($stmt->execute()) {
        header('Location: profile.php');
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .container form {
            display: grid;
            gap: 15px;
            text-align: left;
        }

        .container input[type="text"],
        .container input[type="email"],
        .container textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .container textarea {
            resize: vertical;
            height: 150px;
        }

        .container button {
            background-color: #FFA500;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .container button:hover {
            background-color: #FF7A00;
        }

        .btn {
            display: inline-block;
            background-color: #FFA500;
            color: #333;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            margin-top: 20px;
            color: white;
        }

        .btn:hover {
            background-color: #FF7A00;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" placeholder="Nama Lembaga" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email" required>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="Nomor Telepon" required>
            <input type="text" name="location" value="<?php echo htmlspecialchars($user['location']); ?>" placeholder="Alamat" required>
            <textarea name="foundation_profile" placeholder="Profil Lembaga/Yayasan" required><?php echo htmlspecialchars($user['foundation_profile']); ?></textarea>
            <label for="profile_picture">Foto Profil</label>
            <input type="file" name="profile_picture" accept="image/*">
            <button type="submit">Update Profil</button>
        </form>
        <a href="profile.php" class="btn">Kembali</a>
    </div>
</body>

</html>