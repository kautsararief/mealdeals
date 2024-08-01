<?php
session_start();
include 'php/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM donations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    header('Location: admin_dashboard.php');
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_id'])) {
    $donation_id = $_POST['donation_id'];

    // Cek apakah donasi ini milik pengguna yang login
    $sql = "SELECT giver_id FROM donations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $stmt->bind_result($giver_id);
    $stmt->fetch();
    $stmt->close();

    if ($giver_id === $user_id) {
        // Hapus donasi dari database
        $sql = "DELETE FROM donations WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("i", $donation_id);
        if ($stmt->execute()) {
            header('Location: history.php');
            exit();
        } else {
            die('Execute failed: ' . $stmt->error);
        }
    } else {
        echo "Anda tidak memiliki izin untuk menghapus donasi ini.";
    }
} else {
    header('Location: history.php');
    exit();
}
