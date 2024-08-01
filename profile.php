<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Tentukan teks role berdasarkan nilai dari database
$role_text = '';
if ($user['role'] == 'giver') {
    $role_text = 'Pemberi/Penyalur Donasi';
} elseif ($user['role'] == 'receiver') {
    $role_text = 'Penerima Donasi';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
            width: 90%;
            max-width: 600px;
            text-align: center;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .container p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 10px;
            text-align: left;
        }

        .btn-group {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .btn {
            display: inline-block;
            background-color: #FFA500;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            margin-bottom: 10px;
            cursor: pointer;
            width: 100%;
            max-width: 200px;
            box-sizing: border-box;
        }

        .btn:hover {
            background-color: #FF7A00;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .profile-picture {
                width: 120px;
                height: 120px;
            }

            .container h1 {
                font-size: 2em;
            }

            .container p {
                font-size: 1em;
            }

            .btn {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Profil</h1>
        <img src="uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
        <p>Lembaga: <?php echo htmlspecialchars($user['username']); ?></p>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Nomor Telepon: <?php echo htmlspecialchars($user['phone']); ?></p>
        <p>Alamat: <?php echo nl2br(htmlspecialchars($user['location'])); ?></p>
        <p>Profil Lembaga: <?php echo nl2br(htmlspecialchars($user['foundation_profile'])); ?></p>
        <p>Role: <?php echo htmlspecialchars($role_text); ?></p>
        <div class="btn-group">
            <a href="edit_profile.php" class="btn">Edit Profil</a>
            <a href="dashboard.php" class="btn">Kembali</a>
        </div>
    </div>
</body>

</html>
