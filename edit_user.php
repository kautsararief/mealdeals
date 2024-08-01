<?php
include 'php/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $location = $_POST['location'];
    $foundation_profile = $_POST['foundation_profile'];

    $sql = "UPDATE users SET username = ?, email = ?, phone = ?, role = ?, location = ?, foundation_profile = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $username, $email, $phone, $role, $location, $foundation_profile, $id);

    if ($stmt->execute()) {
        header('Location: admin_dashboard.php');
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .container form {
            display: grid;
            gap: 15px;
        }

        .container input[type="text"],
        .container input[type="email"],
        .container textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .container textarea {
            resize: vertical;
            height: 100px;
        }

        .container button {
            background-color: #FFA500;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .container button:hover {
            background-color: #FF7A00;
        }

        .btn {
            display: inline-block;
            background-color: #FFA500;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #FF7A00;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit User</h1>
        <form action="edit_user.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <input type="text" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            <input type="text" name="role" placeholder="Role" value="<?php echo htmlspecialchars($user['role']); ?>" required>
            <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($user['location']); ?>" required>
            <textarea name="foundation_profile" placeholder="Foundation Profile" required><?php echo htmlspecialchars($user['foundation_profile']); ?></textarea>
            <button type="submit" class="btn">Update</button>
        </form>
        <a href="admin_dashboard.php" class="btn">Kembali</a>
    </div>
</body>

</html>