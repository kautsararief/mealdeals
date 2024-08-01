<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$giver_id = $_GET['giver_id'];

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
</head>

<body>
    <div class="container">
        <h1>Chat Dengan <?php echo htmlspecialchars($_GET['giver_name']); ?></h1>
        <div class="chat-box">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="chat-message">
                    <strong><?php echo htmlspecialchars($row['sender_name']); ?>:</strong>
                    <p><?php echo htmlspecialchars($row['message']); ?></p>
                    <span class="chat-timestamp"><?php echo htmlspecialchars($row['created_at']); ?></span>
                </div>
            <?php endwhile; ?>
        </div>
        <a href="chat.php?giver_id=<?php echo htmlspecialchars($giver_id); ?>" class="btn">Kembali ke Chat</a>
    </div>
</body>

</html>