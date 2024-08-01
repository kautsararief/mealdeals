<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data donasi beserta nama dan nomor telepon pemberi donasi yang masih tersedia
$sql = "SELECT d.*, u.username AS giver_name, u.phone AS giver_phone 
        FROM donations d
        LEFT JOIN users u ON d.giver_id = u.id
        WHERE d.receiver_id IS NULL
        ORDER BY d.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receive Donations</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px 0;
            text-align: center;
            text-decoration: none;
            color: white;
            background-color: #FFA500;
            border: none;
            border-radius: 5px;
            width: 200px;
        }

        .btn:hover {
            background-color: #FF7A00;
        }

        .center-button {
            text-align: center;
            margin-top: 20px;
        }

        .expired {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Terima Donasi</h1>
        <ul>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <li>
                    <p>Nama Makanan/Minuman: <?php echo htmlspecialchars($row['title']); ?></p>
                    <p>Jumlah: <?php echo htmlspecialchars($row['quantity']); ?></p>
                    <p>Alamat: <?php echo htmlspecialchars($row['location']); ?></p>
                    <p>Deskripsi: <?php echo htmlspecialchars($row['description']); ?></p>
                    <p>Pemberi Donasi: <?php echo htmlspecialchars($row['giver_name']); ?></p>
                    <p>Telepon: <?php echo htmlspecialchars($row['giver_phone']); ?></p>
                    <p>Diunggah pada: <?php echo htmlspecialchars($row['created_at']); ?></p>
                    <?php if (!empty($row['food_image'])) : ?>
                        <p><img src="<?php echo htmlspecialchars($row['food_image']); ?>" alt="Food Image" width="200"></p>
                    <?php endif; ?>

                    <!-- Menampilkan expires_at dan pickup_duration -->
                    <p>Waktu Kadaluarsa: <?php echo htmlspecialchars($row['expires_at']); ?></p>
                    <p>Dapat diambil sebelum: <?php echo htmlspecialchars($row['pickup_deadline']) ; ?></p>

                    <a href="php/receive_donation.php?donation_id=<?php echo $row['id']; ?>" class="btn">Terima Donasi</a>
                    <a href="chat.php?giver_id=<?php echo $row['giver_id']; ?>" class="btn">Chat Pemberi Donasi</a>
                </li>
            <?php endwhile; ?>
        </ul>
        <div class="center-button">
            <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
        </div>
    </div>
</body>

</html>