<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil daftar chat terakhir dari setiap pengirim atau penerima
$sql = "SELECT c.*, u.username AS sender_name, u2.username AS receiver_name
        FROM (
            SELECT MAX(id) AS max_id
            FROM chat
            WHERE sender_id = ? OR receiver_id = ?
            GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)
        ) AS latest_chats
        JOIN chat c ON latest_chats.max_id = c.id
        LEFT JOIN users u ON c.sender_id = u.id
        LEFT JOIN users u2 ON c.receiver_id = u2.id
        ORDER BY c.created_at DESC";
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
    <title>Chat History</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #FFA500;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background: #e9ecef;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        li p {
            margin: 5px 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
            color: white;
            background-color: #FFA500;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #FF7A00;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Riwayat Pesan</h1>
        <ul>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <li>
                    <p><strong>Dari:</strong> <?php echo htmlspecialchars($row['sender_name']); ?></p>
                    <p><strong>Ke:</strong> <?php echo htmlspecialchars($row['receiver_name']); ?></p>
                    <p><strong>Pesan:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                    <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                    <a href="chat.php?giver_id=<?php echo ($row['sender_id'] == $user_id) ? $row['receiver_id'] : $row['sender_id']; ?>" class="btn">Balas</a>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="dashboard.php" class="btn back-btn">Kembali</a>
    </div>
</body>

</html>