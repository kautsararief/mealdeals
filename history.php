<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Mengatur zona waktu ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Query untuk memperbarui status donasi yang melewati batas waktu pengambilan
$sql_update = "UPDATE donations SET status = 'Donasi dibatalkan' 
               WHERE status = 'available' AND pickup_deadline < NOW()";
$conn->query($sql_update);

// Ambil data donasi beserta nama dan nomor telepon pemberi dan penerima donasi
$sql = "SELECT d.*, 
        g.username AS giver_name, 
        g.phone AS giver_phone,
        r.username AS receiver_name,
        r.phone AS receiver_phone
        FROM donations d
        LEFT JOIN users g ON d.giver_id = g.id
        LEFT JOIN users r ON d.receiver_id = r.id
        WHERE d.giver_id = ? OR d.receiver_id = ?
        ORDER BY d.created_at DESC";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Donasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            margin: 20px;
        }

        .container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .container ul {
            list-style-type: none;
            padding: 0;
        }

        .container li {
            background-color: #f9f9f9;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .container p {
            margin: 5px 0;
            line-height: 1.6;
        }

        .container img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn {
            display: block;
            text-align: center;
            background-color: #FFA500;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #FF7A00;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .container p {
                font-size: 14px;
            }
        }

        .container img {
            max-width: 100%;
            max-height: 300px;
            /* Menentukan tinggi maksimal gambar */
            height: auto;
            /* Menjaga rasio aspek gambar */
            border-radius: 5px;
            margin-top: 10px;
            object-fit: cover;
            /* Memastikan gambar tetap terlihat baik dalam area yang ditentukan */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Riwayat Donasi</h1>
        <ul>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <li>
                    <p>Nama Makanan/Minuman: <?php echo htmlspecialchars($row['title']); ?></p>
                    <p>Jumlah: <?php echo htmlspecialchars($row['quantity']); ?></p>
                    <p>Alamat: <?php echo htmlspecialchars($row['location']); ?></p>
                    <p>Deskripsi: <?php echo htmlspecialchars($row['description']); ?></p>
                    <p>Status:
                        <?php
                        if ($row['status'] === 'available') {
                            echo 'Available';
                        } elseif ($row['status'] === 'Donasi dibatalkan') {
                            echo 'Donasi dibatalkan';
                        } else {
                            echo 'Received';
                        }
                        ?>
                    </p>
                    <p>Pemberi Donasi: <?php echo htmlspecialchars($row['giver_name']); ?> (Telepon: <?php echo htmlspecialchars($row['giver_phone']); ?>)</p>
                    <p>Waktu Donasi: <?php echo htmlspecialchars($row['created_at']); ?></p>
                    <p>Waktu Kadaluarsa: <?php echo htmlspecialchars($row['expires_at']); ?></p>
                    <p>Dapat Diambil Sebelum: <?php echo htmlspecialchars($row['pickup_deadline']); ?></p>
                    <?php if ($row['receiver_name']) : ?>
                        <p>Penerima Donasi: <?php echo htmlspecialchars($row['receiver_name']); ?> (Telepon: <?php echo htmlspecialchars($row['receiver_phone']); ?>)</p>
                    <?php endif; ?>
                    <?php if (!empty($row['food_image'])) : ?>
                        <p><img src="<?php echo htmlspecialchars($row['food_image']); ?>" alt="Food Image"></p>
                    <?php endif; ?>
                    <?php if ($user_id === $row['giver_id'] && $row['status'] === 'available') : ?>
                        <form method="post" action="delete_donation.php" style="display:inline;">
                            <input type="hidden" name="donation_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input class="btn" type="submit" value="Hapus" onclick="return confirm('Anda yakin ingin menghapus donasi ini?');">
                        </form>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="dashboard.php" class="btn">Kembali</a>
    </div>
</body>

</html>