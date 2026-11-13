<?php
// proses_edit_catat_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ====================================
// CEK REQUEST POST
// ====================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: catat_pelanggaran.php");
    exit;
}

// ====================================
// CEK ID CATATAN
// ====================================

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['pesan_error'] = "ID catatan tidak valid.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// ====================================
// AMBIL DATA FORM
// ====================================

$pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
$tanggal = trim($_POST['tanggal'] ?? '');
$status = trim($_POST['status'] ?? '');
$keterangan = trim($_POST['keterangan'] ?? '');

// ====================================
// VALIDASI INPUT
// ====================================

if (
    $pelanggaran_id <= 0 ||
    $tanggal === '' ||
    $status === ''
) {
    $_SESSION['pesan_error'] = "Semua data wajib diisi.";
    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}

// Validasi tanggal
$tanggal_parts = explode('-', $tanggal);

if (
    count($tanggal_parts) !== 3 ||
    !checkdate(
        (int) $tanggal_parts[1],
        (int) $tanggal_parts[2],
        (int) $tanggal_parts[0]
    )
) {
    $_SESSION['pesan_error'] = "Format tanggal tidak valid.";
    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}

// Validasi status
$status_valid = ['Ditindaklanjuti', 'Proses', 'Selesai'];

if (!in_array($status, $status_valid, true)) {
    $_SESSION['pesan_error'] = "Status tidak valid.";
    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}

// ====================================
// CEK DATA CATATAN
// ====================================

$sql_cek = "SELECT id, status
            FROM t_pelanggaran_siswa
            WHERE id = ?
            LIMIT 1";

$stmt_cek = mysqli_prepare($koneksi, $sql_cek);

mysqli_stmt_bind_param($stmt_cek, "i", $id);
mysqli_stmt_execute($stmt_cek);

$result = mysqli_stmt_get_result($stmt_cek);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt_cek);

if (!$data) {
    $_SESSION['pesan_error'] = "Data catatan tidak ditemukan.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Cek apakah sudah selesai
if ($data['status'] === 'Selesai') {
    $_SESSION['pesan_error'] = "Catatan yang sudah selesai tidak dapat diedit.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// ====================================
// CEK JENIS PELANGGARAN
// ====================================

$sql_pelanggaran = "SELECT id
                    FROM t_pelanggaran
                    WHERE id = ?
                    LIMIT 1";

$stmt_pelanggaran = mysqli_prepare($koneksi, $sql_pelanggaran);

mysqli_stmt_bind_param(
    $stmt_pelanggaran,
    "i",
    $pelanggaran_id
);

mysqli_stmt_execute($stmt_pelanggaran);

$result_pelanggaran = mysqli_stmt_get_result($stmt_pelanggaran);

$cek_pelanggaran = mysqli_fetch_assoc($result_pelanggaran);

mysqli_stmt_close($stmt_pelanggaran);

if (!$cek_pelanggaran) {
    $_SESSION['pesan_error'] = "Jenis pelanggaran tidak ditemukan.";
    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}

// ====================================
// UPDATE DATA CATATAN PELANGGARAN
// ====================================

$sql = "UPDATE t_pelanggaran_siswa
        SET
            pelanggaran_id = ?,
            tanggal = ?,
            status = ?,
            keterangan = ?
        WHERE id = ?";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['pesan_error'] = "Query update gagal dibuat.";
    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "isssi",
    $pelanggaran_id,
    $tanggal,
    $status,
    $keterangan,
    $id
);

// ====================================
// EKSEKUSI UPDATE
// ====================================

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['pesan_sukses'] = "Data catatan pelanggaran berhasil diperbarui.";

    header("Location: catat_pelanggaran.php");
    exit;

} else {

    $_SESSION['pesan_error'] = "Gagal memperbarui data catatan pelanggaran.";

    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}

mysqli_stmt_close($stmt);
?>