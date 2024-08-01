<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'receiver') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['donation_id'])) {
    $donation_id = $_GET['donation_id'];
    $receiver_id = $_SESSION['user_id'];

    $sql = "UPDATE donations SET receiver_id = ?, status = 'received' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $receiver_id, $donation_id);

    if ($stmt->execute()) {
        header('Location: ../receive.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
