<?php
include 'php/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];
    $location = $_POST['location'];
    $receiver_id = $_POST['receiver_id'];
    $giver_id = $_POST['giver_id'];

    $sql = "UPDATE donations SET title = ?, description = ?, quantity = ?, status = ?, location = ?, receiver_id = ?, giver_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssii", $title, $description, $quantity, $status, $location, $receiver_id, $giver_id, $id);

    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM donations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $donation = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donation</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container">
        <h1>Edit Donation</h1>
        <form action="edit_donation.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($donation['id']); ?>">
            <input type="text" name="title" placeholder="Title" value="<?php echo htmlspecialchars($donation['title']); ?>" required>
            <textarea name="description" placeholder="Description" required><?php echo htmlspecialchars($donation['description']); ?></textarea>
            <input type="number" name="quantity" placeholder="Quantity" value="<?php echo htmlspecialchars($donation['quantity']); ?>" required>
            <input type="text" name="status" placeholder="Status" value="<?php echo htmlspecialchars($donation['status']); ?>" required>
            <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($donation['location']); ?>" required>
            <input type="text" name="receiver_id" placeholder="Receiver ID" value="<?php echo htmlspecialchars($donation['receiver_id']); ?>" required>
            <input type="text" name="giver_id" placeholder="Giver ID" value="<?php echo htmlspecialchars($donation['giver_id']); ?>" required>
            <button type="submit" class="btn">Update Donation</button>
        </form>
        <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>

</html>