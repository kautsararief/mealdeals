<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil username dan role dari database jika belum ada dalam sesi
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    $sql = "SELECT username, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $role);
    $stmt->fetch();
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $stmt->close();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- <link rel="stylesheet" href="css/styles.css"> -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            border-top: 5px solid #FF8C00;
            /* Garis atas */
        }

        h1 {
            color: #FFA500;
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .button-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .btn {
            display: inline-block;
            padding: 15px 25px;
            font-size: 18px;
            color: white;
            background-color: #FF8C00;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            flex-basis: calc(50% - 20px);
            /* Responsif di perangkat kecil */
            box-sizing: border-box;
        }

        .btn:hover {
            background-color: #FF7F50;
            transform: translateY(-2px);
        }

        .btn i {
            margin-right: 8px;
        }

        /* Tambahkan margin khusus untuk tombol Logout */
        .btn.logout {
            margin-top: 30px;
            /* Atur jarak yang diinginkan */
        }

        @media (max-width: 600px) {
            .btn {
                flex-basis: 100%;
                margin-bottom: 10px;
            }

            h1 {
                font-size: 2em;
            }

            p {
                font-size: 1em;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Dashboard</h1>
        <p>Selamat Datang, <?php echo htmlspecialchars($username); ?>!</p>
        <div class="button-container">
            <a href="profile.php" class="btn">Profil</a>
            <?php if ($role === 'giver') : ?>
                <a href="donate.php" class="btn">Beri Donasi Makanan</a>
            <?php elseif ($role === 'receiver') : ?>
                <a href="receive.php" class="btn">Terima Donasi Makanan</a>
            <?php endif; ?>
            <a href="history.php" class="btn">Riwayat Donasi</a>
            <a href="chat_history.php" class="btn">Riwayat Pesan</a>
            <a href="php/logout.php" class="btn logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</body>

</html>