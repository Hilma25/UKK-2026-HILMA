<?php
session_start();
include 'config/koneksi.php';

// Pastikan form mengirim email dan password
if (!isset($_POST['email'], $_POST['password'])) {
    header("Location: login.php");
    exit;
}

$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = $_POST['password'];

// Cari akun berdasarkan email
$sql = "SELECT * FROM t_users WHERE email = '$email' LIMIT 1";
$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Query login gagal: " . mysqli_error($koneksi));
}

// Periksa akun
if (mysqli_num_rows($hasil) == 1) {

    $data = mysqli_fetch_assoc($hasil);

    // Periksa password
    if (password_verify($password, $data['password'])) {

        // Pastikan kolom ID tersedia
        if (!isset($data['id']) || (int)$data['id'] <= 0) {
            die("Kolom id pada t_users tidak ditemukan atau tidak valid.");
        }

        // Buat session baru
        session_regenerate_id(true);

        $_SESSION['login'] = true;
        $_SESSION['id_user'] = (int)$data['id'];
        $_SESSION['name'] = $data['name'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['role'] = $data['role'];

        // Pastikan session berhasil tersimpan
        session_write_close();

        header("Location: dashboard.php");
        exit;

    } else {
        $_SESSION['pesan_error'] = 'Password salah!';
        header("Location: login.php");
        exit;
    }

} else {
    $_SESSION['pesan_error'] = 'Email tidak ditemukan!';
    header("Location: login.php");
    exit;
}
?>