<?php
session_start();
include 'php/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $giver_id = $_POST['giver_id'];
    $message = $_POST['message'];

    $sql = "INSERT INTO chat (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("iis", $user_id, $giver_id, $message);

    if ($stmt->execute()) {
        header("Location: chat.php?giver_id=$giver_id");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
