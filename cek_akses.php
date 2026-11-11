<?php
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek hak akses pengguna
function cek_role($role_diizinkan) {

    if (!in_array($_SESSION['role'], $role_diizinkan)) {
        echo "Akses ditolak!";
        echo "<br>";
        echo "<a href='dashboard.php'>Kembali ke Dashboard</a>";
        exit;
    }

}
?>
