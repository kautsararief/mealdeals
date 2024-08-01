<?php
// Inisialisasi variabel untuk autentikasi
$authenticated = false;

// Jika form login sudah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $correct_username = '4dmM34lDeals';
    $correct_password = 'A2mJ8nS4300822';

    if ($username === $correct_username && $password === $correct_password) {
        $authenticated = true;
    } else {
        $authenticated = false;
    }
}

// Jika belum terautentikasi, tampilkan form login
if (!$authenticated) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Halaman Login</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .login-box {
                background-color: #fff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .login-box h2 {
                text-align: center;
                margin-bottom: 20px;
                color:#FFA500;
            }

            .login-box input[type="text"],
            .login-box input[type="password"] {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-sizing: border-box;
            }

            .login-box input[type="submit"] {
                background-color: #f90;
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .login-box input[type="submit"]:hover {
                background-color: #d98000;
            }
        </style>
    </head>

    <body>
        <div class="login-box">
            <h2>LOGIN KE ADMIN</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                <input type="submit" value="Login">
            </form>
        </div>
    </body>

    </html>

<?php
} else {
    include 'php/database.php';

    // Ambil data dari tabel donations dengan username dari users
    $donations_sql = "
        SELECT d.*, u1.username AS giver_username, u2.username AS receiver_username
        FROM donations d
        LEFT JOIN users u1 ON d.giver_id = u1.id
        LEFT JOIN users u2 ON d.receiver_id = u2.id
        ORDER BY d.created_at DESC";
    $donations_result = $conn->query($donations_sql);

    $users_sql = "SELECT * FROM users ORDER BY created_at DESC";
    $users_result = $conn->query($users_sql);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Admin</title>
        <link rel="stylesheet" href="css/styles.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                display: flex;
                min-height: 100vh;
            }

            .sidebar {
                background-color: #f90;
                color: #fff;
                width: 250px;
                padding: 20px;
                box-sizing: border-box;
            }

            .sidebar h2 {
                font-size: 1.5rem;
                margin-bottom: 20px;
                text-align: center;
            }

            .sidebar ul {
                list-style-type: none;
                padding: 0;
                margin: 0;
            }

            .sidebar li {
                margin-bottom: 10px;
            }

            .sidebar a {
                color: #fff;
                text-decoration: none;
                display: block;
                padding: 10px;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }

            .sidebar a:hover {
                background-color: #d98000;
            }

            .content {
                flex: 1;
                padding: 20px;
            }

            table {
                width: 100%;
                margin-top: 20px;
                border-collapse: collapse;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            th,
            td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            th {
                background-color: #f90;
                color: #fff;
            }

            tr:hover {
                background-color: #ffe0b2;
            }

            .btn {
                display: inline-block;
                padding: 10px 20px;
                background-color: #f90;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
                margin-top: 10px;
                transition: background-color 0.3s ease;
            }

            .btn:hover {
                background-color: #d98000;
            }

            .table-title {
                font-size: 1.5rem;
                margin-top: 20px;
                color: #f90;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="#" onclick="showTable('donations')">Riwayat Donasi</a></li>
                <li><a href="#" onclick="showTable('users')">Users</a></li>
                <li><a href="dashboard.php">Kembali ke Dashboard</a></li>
            </ul>
        </div>

        <div class="content">
            <h1>Dashboard Admin</h1>

            <div id="tableTitle" class="table-title"></div>

            <!-- Donations Table -->
            <table id="donationsTable" style="display:none;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th>Stok</th>
                        <th>Alamat</th>
                        <th>Dibuat</th>
                        <th>Penerima Donasi</th>
                        <th>Pemberi Donasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="donationsTableBody">
                    <?php while ($donation = $donations_result->fetch_assoc()) : ?>
                        <tr data-giver-id="<?php echo htmlspecialchars($donation['giver_id']); ?>" data-receiver-id="<?php echo htmlspecialchars($donation['receiver_id']); ?>">
                            <td><?php echo htmlspecialchars($donation['id']); ?></td>
                            <td><?php echo htmlspecialchars($donation['title']); ?></td>
                            <td><?php echo htmlspecialchars($donation['description']); ?></td>
                            <td><?php echo htmlspecialchars($donation['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($donation['location']); ?></td>
                            <td><?php echo htmlspecialchars($donation['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($donation['receiver_username']); ?></td>
                            <td><?php echo htmlspecialchars($donation['giver_username']); ?></td>
                            <td>
                                <a href="edit_donation.php?id=<?php echo $donation['id']; ?>">Edit</a>
                                <a href="delete_donation.php?id=<?php echo $donation['id']; ?>" onclick="return confirm('Anda yakin ingin menghapus donasi ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Users Table -->
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Lembaga</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Role</th>
                        <th>Alamat</th>
                        <th>Profil Lembaga</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['location']); ?></td>
                            <td><?php echo htmlspecialchars($user['foundation_profile']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                                <button onclick="filterDonations(<?php echo htmlspecialchars($user['id']); ?>)">Lihat Donasi</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <script>
            // Fungsi untuk mengganti tampilan tabel
            function showTable(tableName) {
                const donationsTable = document.getElementById('donationsTable');
                const usersTable = document.getElementById('usersTable');
                const tableTitle = document.getElementById('tableTitle');

                if (tableName === 'donations') {
                    donationsTable.style.display = 'table';
                    usersTable.style.display = 'none';
                    tableTitle.textContent = 'Tabel Riwayat Donasi';
                } else if (tableName === 'users') {
                    donationsTable.style.display = 'none';
                    usersTable.style.display = 'table';
                    tableTitle.textContent = 'Tabel Users';
                }
            }

            // Fungsi untuk filter donasi berdasarkan user_id
            function filterDonations(userId) {
                showTable('donations');

                const donationsTableBody = document.getElementById('donationsTableBody');
                const rows = donationsTableBody.getElementsByTagName('tr');

                for (let row of rows) {
                    const giverId = row.getAttribute('data-giver-id');
                    const receiverId = row.getAttribute('data-receiver-id');

                    // Filter berdasarkan giver_id atau receiver_id
                    if (giverId == userId || receiverId == userId) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }

            // Tampilkan tabel donasi secara default saat pertama kali halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                showTable('donations');
            });
        </script>
    </body>

    </html>
<?php
} // Tutup else
?>