
<?php
// hapus_penempatan_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// 1. VALIDASI ID PENEMPATAN
// ========================================

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    $_SESSION['pesan_error'] = 'ID penempatan siswa tidak valid.';
    header('Location: penempatan_siswa.php');
    exit;
}

// ========================================
// 2. PERIKSA DATA PENEMPATAN
// ========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT
        ks.id,
        s.nama AS nama_siswa,
        s.nis,
        ta.nama AS tahun_ajaran,
        k.nama AS nama_kelas
     FROM t_kelas_siswa ks
     LEFT JOIN t_siswa s
        ON ks.siswa_id = s.id
     LEFT JOIN t_tahun_ajaran ta
        ON ks.tahun_ajaran_id = ta.id
     LEFT JOIN t_kelas k
        ON ks.kelas_id = k.id
     WHERE ks.id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

// Jika data tidak ditemukan
if (!$data) {
    $_SESSION['pesan_error'] = 'Data penempatan siswa tidak ditemukan.';
    header('Location: penempatan_siswa.php');
    exit;
}

// ========================================
// 3. HAPUS DATA PENEMPATAN
// ========================================

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_kelas_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    $_SESSION['pesan_sukses'] =
        'Data penempatan siswa "' .
        ($data['nama_siswa'] ?? '-') .
        '" pada kelas "' .
        ($data['nama_kelas'] ?? '-') .
        '" berhasil dihapus.';

    header('Location: penempatan_siswa.php');
    exit;
}

// ========================================
// 4. PENANGANAN ERROR
// ========================================

$kode_error = mysqli_stmt_errno($stmt);

mysqli_stmt_close($stmt);

if ($kode_error === 1451) {

    $_SESSION['pesan_error'] =
        'Data penempatan tidak dapat dihapus karena masih digunakan oleh data lain.';

} else {

    $_SESSION['pesan_error'] =
        'Gagal menghapus data penempatan siswa.';
}

header('Location: penempatan_siswa.php');
exit;

?>
