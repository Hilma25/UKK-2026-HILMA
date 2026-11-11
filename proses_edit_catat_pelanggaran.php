<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: catat_pelanggaran.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$siswa_id = filter_input(INPUT_POST, 'siswa_id', FILTER_VALIDATE_INT);
$tahun_ajaran_id = filter_input(INPUT_POST, 'tahun_ajaran_id', FILTER_VALIDATE_INT);
$kelas_id = filter_input(INPUT_POST, 'kelas_id', FILTER_VALIDATE_INT);
$pelanggaran_id = filter_input(INPUT_POST, 'pelanggaran_id', FILTER_VALIDATE_INT);
$guru_id = filter_input(INPUT_POST, 'guru_id', FILTER_VALIDATE_INT);
$poin = filter_input(INPUT_POST, 'poin', FILTER_VALIDATE_INT);

$tanggal = $_POST['tanggal'] ?? '';
$status = $_POST['status'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$tindakan = $_POST['tindakan'] ?? '';

$statusValid = ['Ditindaklanjuti', 'Proses', 'Selesai'];

if (
    !$id || !$siswa_id || !$tahun_ajaran_id ||
    !$kelas_id || !$pelanggaran_id || !$guru_id ||
    $poin === false || $poin === null || $poin < 0 ||
    !in_array($status, $statusValid, true) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ||
    !checkdate(
        (int)substr($tanggal, 5, 2),
        (int)substr($tanggal, 8, 2),
        (int)substr($tanggal, 0, 4)
    )
) {
    $_SESSION['pesan_error'] = "Data yang dimasukkan tidak valid.";
    header("Location: edit_catat_pelanggaran.php?id=" . (int)$id);
    exit;
}

// Ambil kategori dari tabel pelanggaran.
// Tidak menggunakan nilai kategori dari hidden input agar tidak mudah dimanipulasi.
$stmtKategori = mysqli_prepare(
    $koneksi,
    "SELECT pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE id = ? AND status_aktif = 1"
);

mysqli_stmt_bind_param($stmtKategori, "i", $pelanggaran_id);
mysqli_stmt_execute($stmtKategori);

$hasilKategori = mysqli_stmt_get_result($stmtKategori);
$kategoriData = mysqli_fetch_assoc($hasilKategori);
mysqli_stmt_close($stmtKategori);

if (!$kategoriData) {
    $_SESSION['pesan_error'] = "Jenis pelanggaran tidak valid.";
    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}

$kategori_id = (int)$kategoriData['pelanggaran_kategori_id'];

// Update data pelanggaran
$stmt = mysqli_prepare($koneksi, "
    UPDATE t_pelanggaran_siswa SET
        siswa_id = ?,
        tahun_ajaran_id = ?,
        kelas_id = ?,
        pelanggaran_id = ?,
        pelanggaran_kategori_id = ?,
        guru_id = ?,
        tanggal = ?,
        poin = ?,
        status = ?,
        keterangan = ?,
        tindakan = ?
    WHERE id = ?
");

mysqli_stmt_bind_param(
    $stmt,
    "iiiiiisisssi",
    $siswa_id,
    $tahun_ajaran_id,
    $kelas_id,
    $pelanggaran_id,
    $kategori_id,
    $guru_id,
    $tanggal,
    $poin,
    $status,
    $keterangan,
    $tindakan,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Data pelanggaran berhasil diperbarui.";
} else {
    $_SESSION['pesan_error'] = "Gagal memperbarui data pelanggaran.";
}

mysqli_stmt_close($stmt);

header("Location: catat_pelanggaran.php");
exit;
?>