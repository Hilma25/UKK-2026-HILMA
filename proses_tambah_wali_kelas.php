
<?php
// proses_tambah_wali_kelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// 1. VALIDASI METHOD POST
// ========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tambah_wali_kelas.php");
    exit;
}

// ========================================
// 2. AMBIL DATA FORM
// ========================================
$tahun_ajaran_id = filter_input(INPUT_POST, 'tahun_ajaran_id', FILTER_VALIDATE_INT);
$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_VALIDATE_INT);
$guru_id = filter_input(INPUT_POST, 'guru_id', FILTER_VALIDATE_INT);

$tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');

$status_aktif = filter_input(INPUT_POST, 'status_aktif', FILTER_VALIDATE_INT);

// ========================================
// 3. VALIDASI DATA
// ========================================
if (
    !$tahun_ajaran_id ||
    !$kelas_id ||
    !$guru_id ||
    !in_array($status_aktif, [0, 1], true)
) {
    $_SESSION['pesan_error'] = 'Data wali kelas tidak valid.';
    header("Location: tambah_wali_kelas.php");
    exit;
}

// ========================================
// 4. VALIDASI FORMAT TANGGAL
// ========================================
$format_mulai = DateTime::createFromFormat('!Y-m-d', $tanggal_mulai);
$format_selesai = DateTime::createFromFormat('!Y-m-d', $tanggal_selesai);

if (
    !$format_mulai ||
    !$format_selesai ||
    $format_mulai->format('Y-m-d') !== $tanggal_mulai ||
    $format_selesai->format('Y-m-d') !== $tanggal_selesai
) {
    $_SESSION['pesan_error'] = 'Format tanggal tidak valid.';
    header("Location: tambah_wali_kelas.php");
    exit;
}

// ========================================
// 5. VALIDASI RENTANG TANGGAL
// ========================================
if ($tanggal_selesai < $tanggal_mulai) {
    $_SESSION['pesan_error'] = 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
    header("Location: tambah_wali_kelas.php");
    exit;
}

// ========================================
// 6. VALIDASI DATA GURU, KELAS DAN TAHUN
// ========================================
$stmt_cek = mysqli_prepare(
    $koneksi,
    "SELECT
        (SELECT COUNT(*) FROM t_guru WHERE id = ?) AS jumlah_guru,
        (SELECT COUNT(*) FROM t_kelas WHERE id = ? AND status_aktif = 1) AS jumlah_kelas,
        (SELECT COUNT(*) FROM t_tahun_ajaran WHERE id = ? AND status_aktif = 1) AS jumlah_tahun"
);

if (!$stmt_cek) {
    $_SESSION['pesan_error'] = 'Gagal memvalidasi data: ' . mysqli_error($koneksi);
    header("Location: tambah_wali_kelas.php");
    exit;
}

mysqli_stmt_bind_param(
    $stmt_cek,
    "iii",
    $guru_id,
    $kelas_id,
    $tahun_ajaran_id
);

mysqli_stmt_execute($stmt_cek);

$hasil_cek = mysqli_stmt_get_result($stmt_cek);
$data_cek = mysqli_fetch_assoc($hasil_cek);

mysqli_stmt_close($stmt_cek);

if (
    !$data_cek ||
    (int) $data_cek['jumlah_guru'] === 0 ||
    (int) $data_cek['jumlah_kelas'] === 0 ||
    (int) $data_cek['jumlah_tahun'] === 0
) {
    $_SESSION['pesan_error'] = 'Data guru, kelas, atau tahun ajaran tidak ditemukan atau tidak aktif.';
    header("Location: tambah_wali_kelas.php");
    exit;
}

// ========================================
// 7. SIMPAN DATA WALI KELAS
// ========================================
$sql = "INSERT INTO t_wali_kelas (
            tahun_ajaran_id,
            kelas_id,
            guru_id,
            tanggal_mulai,
            tanggal_selesai,
            status_aktif
        ) VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['pesan_error'] = 'Gagal menyiapkan query: ' . mysqli_error($koneksi);
    header("Location: tambah_wali_kelas.php");
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "iiissi",
    $tahun_ajaran_id,
    $kelas_id,
    $guru_id,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif
);

// ========================================
// 8. EKSEKUSI DAN REDIRECT
// ========================================
if (mysqli_stmt_execute($stmt)) {

    $_SESSION['pesan_sukses'] = 'Data wali kelas berhasil ditambahkan.';

    mysqli_stmt_close($stmt);

    header("Location: kelola_wali_kelas.php");
    exit;

} else {

    $_SESSION['pesan_error'] = 'Gagal menambahkan wali kelas: ' . mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);

    header("Location: tambah_wali_kelas.php");
    exit;
}
?>
