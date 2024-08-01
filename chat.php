<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['giver_id'])) {
    die('Giver ID is required.');
}

$giver_id = $_GET['giver_id'];
$user_id = $_SESSION['user_id'];

// Ambil detail pemberi donasi dari database
$sql = "SELECT username, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $giver_id);
$stmt->execute();
$stmt->bind_result($giver_name, $giver_phone);
$stmt->fetch();
$stmt->close();

// Ambil pesan antara user dan giver
$sql = "SELECT c.*, u.username AS sender_name 
        FROM chat c
        LEFT JOIN users u ON c.sender_id = u.id
        WHERE (c.sender_id = ? AND c.receiver_id = ?) OR (c.sender_id = ? AND c.receiver_id = ?)
        ORDER BY c.created_at ASC";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("iiii", $user_id, $giver_id, $giver_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Giver</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .button-container {
            display: flex;
            justify-content: center;
            /* Memusatkan tombol di tengah */
            width: 100%;
            /* Memastikan lebar penuh */
            margin-top: 20px;
            /* Jarak atas untuk tombol */
        }

        .btn {
            padding: 10px 20px;
            margin: 5px;
            text-align: center;
            text-decoration: none;
            color: white;
            background-color: #FFA500;
            /* Warna latar belakang */
            border: none;
            /* Hapus border default */
            border-radius: 5px;
            /* Radius sudut */
            width: 200px;
            /* Lebar button */
        }

        .btn:hover {
            background-color: #FF7A00;
            /* Warna saat hover */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Chat dengan <?php echo htmlspecialchars($giver_name); ?></h1>
        <p>Telepon: <?php echo htmlspecialchars($giver_phone); ?></p>
        <div class="chat-box">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="chat-message">
                    <strong><?php echo htmlspecialchars($row['sender_name']); ?>:</strong>
                    <p><?php echo htmlspecialchars($row['message']); ?></p>
                    <span class="chat-timestamp"><?php echo htmlspecialchars($row['created_at']); ?></span>
                </div>
            <?php endwhile; ?>
        </div>
        <!-- Form untuk mengirim pesan -->
        <form action="send_message.php" method="POST">
            <input type="hidden" name="giver_id" value="<?php echo htmlspecialchars($giver_id); ?>">
            <textarea name="message" placeholder="Enter your message" required></textarea>
            <button type="submit" class="btn">Kirim Pesan</button>
        </form>
        <div class="button-container">
            <a href="chat_history.php" class="btn">Kembali ke Riwayat Chat</a>
            <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
        </div>
    </div>
</body>

</html>