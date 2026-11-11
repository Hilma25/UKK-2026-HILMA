
<?php
session_start();
include 'config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// Cek hak akses admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

// Cek tombol simpan
if (!isset($_POST['simpan'])) {
    header("Location: tambah_siswa.php");
    exit;
}

// Ambil data form
$nis = $_POST['nis'];
$nisn = $_POST['nisn'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$alamat = $_POST['alamat'];
$status_aktif = $_POST['status_aktif'];

// Query tambah siswa
$sql = "INSERT INTO t_siswa
        (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan query: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssssssi",
    $nis,
    $nisn,
    $nama,
    $jenis_kelamin,
    $tanggal_lahir,
    $alamat,
    $status_aktif
);

// Eksekusi penyimpanan
if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    // Kembali ke halaman kelola siswa
    header("Location: kelola_siswa.php");
    exit;

} else {

    echo "Gagal menambahkan siswa: " .
         mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);
}
?>
