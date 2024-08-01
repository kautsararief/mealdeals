<?php
include 'php/database.php';

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    $history_sql = "
        SELECT d.*, u1.username AS giver_username, u2.username AS receiver_username
        FROM donations d
        LEFT JOIN users u1 ON d.giver_id = u1.id
        LEFT JOIN users u2 ON d.receiver_id = u2.id
        WHERE d.giver_id = $user_id OR d.receiver_id = $user_id
        ORDER BY d.created_at DESC";
    $history_result = $conn->query($history_sql);

    if ($history_result->num_rows > 0) {
        echo '<table>';
        echo '<thead><tr><th>ID</th><th>Nama Produk</th><th>Deskripsi</th><th>Stok</th><th>Status</th><th>Alamat</th><th>Dibuat</th><th>Penerima Donasi</th><th>Pemberi Donasi</th></tr></thead>';
        echo '<tbody>';
        while ($donation = $history_result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($donation['id']) . '</td>';
            echo '<td>' . htmlspecialchars($donation['title']) . '</td>';
            echo '<td>' . htmlspecialchars($donation['description']) . '</td>';
            echo '<td>' . htmlspecialchars($donation['quantity']) . '</td>';
            echo '<td>' . htmlspecialchars($donation['status']) . '</td>';
            echo '<td>' . htmlspecialchars($donation['location']) . '</td>';
            echo '<td>' . htmlspecialchars($donation['created_at']) . '</td>';
            echo '<td>' . htmlspecialchars($donation['receiver_username']) . '</td>';
            echo '<td>' . htmlspecialchars($donation['giver_username']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Tidak ada riwayat donasi untuk user ini.</p>';
    }
} else {
    echo '<p>Invalid user ID.</p>';
}
