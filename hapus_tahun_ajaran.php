<?php

session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

if (!isset($_GET['id'])) {
    die("ID tahun ajaran tidak ditemukan!");
}

$id = (int) $_GET['id'];

$sql = "DELETE FROM t_tahun_ajaran WHERE id='$id'";

if (mysqli_query($koneksi, $sql)) {
    header("Location: kelola_tahun_ajaran.php");
    exit;
} else {
    echo "Gagal menghapus tahun ajaran. Data mungkin masih digunakan di kelas siswa. "
         . mysqli_error($koneksi);
}

?>