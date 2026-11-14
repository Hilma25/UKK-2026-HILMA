
<?php
// proses_edit_penempatan_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// 1. CEK METHOD POST
// ========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: penempatan_siswa.php');
    exit;
}

// ========================================
// 2. AMBIL DATA DARI FORM
// ========================================

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$siswa_id = filter_input(
    INPUT_POST,
    'siswa_id',
    FILTER_VALIDATE_INT
);

$tahun_ajaran_id = filter_input(
    INPUT_POST,
    'tahun_ajaran_id',
    FILTER_VALIDATE_INT
);

$kelas_id = filter_input(
    INPUT_POST,
    'kelas_id',
    FILTER_VALIDATE_INT
);

$tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');

$status_aktif = filter_input(
    INPUT_POST,
    'status_aktif',
    FILTER_VALIDATE_INT
);

// ========================================
// 3. VALIDASI INPUT
// ========================================

$tanggal_mulai_valid = DateTime::createFromFormat('!Y-m-d', $tanggal_mulai);
$tanggal_selesai_valid = DateTime::createFromFormat('!Y-m-d', $tanggal_selesai);

if (
    !$id || $id <= 0 ||
    !$siswa_id || $siswa_id <= 0 ||
    !$tahun_ajaran_id || $tahun_ajaran_id <= 0 ||
    !$kelas_id || $kelas_id <= 0 ||
    !$tanggal_mulai_valid ||
    $tanggal_mulai_valid->format('Y-m-d') !== $tanggal_mulai ||
    !$tanggal_selesai_valid ||
    $tanggal_selesai_valid->format('Y-m-d') !== $tanggal_selesai ||
    $tanggal_selesai < $tanggal_mulai ||
    !in_array($status_aktif, [0, 1], true)
) {
    $_SESSION['pesan_error'] = 'Data penempatan siswa tidak valid. Periksa kembali formulir.';
    header('Location: penempatan_siswa.php');
    exit;
}

// ========================================
// 4. PASTIKAN DATA PENEMPATAN TERSEDIA
// ========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_kelas_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil_data = mysqli_stmt_get_result($stmt);
$data_ada = mysqli_num_rows($hasil_data) > 0;

mysqli_stmt_close($stmt);

if (!$data_ada) {
    $_SESSION['pesan_error'] = 'Data penempatan siswa tidak ditemukan.';
    header('Location: penempatan_siswa.php');
    exit;
}

// ========================================
// 5. PASTIKAN SISWA TERSEDIA
// ========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $siswa_id);
mysqli_stmt_execute($stmt);

$hasil_siswa = mysqli_stmt_get_result($stmt);
$siswa_ada = mysqli_num_rows($hasil_siswa) > 0;

mysqli_stmt_close($stmt);

if (!$siswa_ada) {
    $_SESSION['pesan_error'] = 'Data siswa tidak ditemukan.';
    header('Location: edit_penempatan_siswa.php?id=' . $id);
    exit;
}

// ========================================
// 6. PASTIKAN TAHUN AJARAN TERSEDIA
// ========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $tahun_ajaran_id);
mysqli_stmt_execute($stmt);

$hasil_tahun = mysqli_stmt_get_result($stmt);
$tahun_ada = mysqli_num_rows($hasil_tahun) > 0;

mysqli_stmt_close($stmt);

if (!$tahun_ada) {
    $_SESSION['pesan_error'] = 'Data tahun ajaran tidak ditemukan.';
    header('Location: edit_penempatan_siswa.php?id=' . $id);
    exit;
}

// ========================================
// 7. PASTIKAN KELAS TERSEDIA
// ========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $kelas_id);
mysqli_stmt_execute($stmt);

$hasil_kelas = mysqli_stmt_get_result($stmt);
$kelas_ada = mysqli_num_rows($hasil_kelas) > 0;

mysqli_stmt_close($stmt);

if (!$kelas_ada) {
    $_SESSION['pesan_error'] = 'Data kelas tidak ditemukan.';
    header('Location: edit_penempatan_siswa.php?id=' . $id);
    exit;
}

// ========================================
// 8. UPDATE DATA PENEMPATAN SISWA
// ========================================

$sql = "UPDATE t_kelas_siswa
        SET siswa_id = ?,
            tahun_ajaran_id = ?,
            kelas_id = ?,
            tanggal_mulai = ?,
            tanggal_selesai = ?,
            status_aktif = ?
        WHERE id = ?";

$stmt = mysqli_prepare($koneksi, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "iiissii",
    $siswa_id,
    $tahun_ajaran_id,
    $kelas_id,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif,
    $id
);

// ========================================
// 9. CEK HASIL UPDATE
// ========================================

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    $_SESSION['pesan_sukses'] =
        'Data penempatan siswa berhasil diperbarui.';

    header('Location: penempatan_siswa.php');
    exit;
}

$kode_error = mysqli_stmt_errno($stmt);

mysqli_stmt_close($stmt);

if ($kode_error === 1062) {
    $_SESSION['pesan_error'] =
        'Data penempatan siswa sudah terdaftar.';
} else {
    $_SESSION['pesan_error'] =
        'Gagal memperbarui data penempatan siswa.';
}

header('Location: edit_penempatan_siswa.php?id=' . $id);
exit;
?>
