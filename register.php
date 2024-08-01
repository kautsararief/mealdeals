<?php
session_start();
include 'php/database.php';

// Cek jika user sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $location = $_POST['location'];

    // Cek apakah username, email, atau phone sudah ada
    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ? OR phone = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sss", $username, $email, $phone);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Jika ada duplikasi
        $error = "Username, Email, atau Telepon sudah terdaftar.";
    } else {
        // Jika tidak ada duplikasi, lanjutkan registrasi
        $sql = "INSERT INTO users (username, email, phone, password, role, location) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $email, $phone, $password, $role, $location);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['role'] = $role;
            header('Location: login.php');
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- <link rel="stylesheet" href="css/styles.css"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .container form {
            display: grid;
            gap: 15px;
        }

        .container input[type="text"],
        .container input[type="email"],
        .container input[type="password"],
        .container select,
        .container button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .container select {
            appearance: none;
        }

        .container button {
            background-color: #FFA500;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .container button:hover {
            background-color: #FF7A00;
        }

        .container .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Nama Lembaga" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Telepon" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="giver">Pemberi Donasi</option>
                <option value="receiver">Penerima Donasi</option>
            </select>
            <input type="text" name="location" placeholder="Alamat" required>
            <button type="submit">Register</button>
        </form>
        <?php if (isset($error)) : ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>

</html>