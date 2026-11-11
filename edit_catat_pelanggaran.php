<?php
// edit_catat_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ============================================
// 1. AMBIL ID PELANGGARAN
// ============================================
$id = isset($_GET['id']) && ctype_digit($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    $_SESSION['pesan_error'] = "ID pelanggaran tidak valid.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// ============================================
// 2. AMBIL DATA PELANGGARAN
// ============================================
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['pesan_error'] = "Data pelanggaran tidak ditemukan.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// ============================================
// 3. AMBIL DAFTAR SISWA
// ============================================
$siswa_id = (int) $data['siswa_id'];

$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1 OR id = $siswa_id
     ORDER BY nama ASC"
);

// ============================================
// 4. AMBIL DAFTAR TAHUN AJARAN
// ============================================
$tahun_id = (int) $data['tahun_ajaran_id'];

$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1 OR id = $tahun_id
     ORDER BY id DESC"
);

// ============================================
// 5. AMBIL DAFTAR KELAS
// ============================================
$kelas_id = (int) $data['kelas_id'];

$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1 OR id = $kelas_id
     ORDER BY nama ASC"
);

// ============================================
// 6. AMBIL DAFTAR JENIS PELANGGARAN
// ============================================
$pelanggaran_id = (int) $data['pelanggaran_id'];

$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1 OR id = $pelanggaran_id
     ORDER BY nama ASC"
);

// ============================================
// 7. AMBIL DAFTAR GURU
// ============================================
$guru_id = (int) $data['guru_id'];

$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE status_aktif = 1 OR id = $guru_id
     ORDER BY nama ASC"
);

// ============================================
// 8. FUNGSI HTML
// ============================================
function e($nilai)
{
    return htmlspecialchars(
        (string) $nilai,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Catatan Pelanggaran</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- Konten Utama -->
        <main class="col-md-9 col-lg-10 p-4">

            <!-- Judul dan Tombol Kembali -->
            <div class="mb-4">

                <h2 class="mb-3">Edit Catatan Pelanggaran Siswa</h2>

                <a href="catat_pelanggaran.php" class="btn btn-secondary">
                    Kembali ke Data Pelanggaran
                </a>

            </div>

            <!-- Pesan Error -->
            <?php if (isset($_SESSION['pesan_error'])): ?>

                <div class="alert alert-danger alert-dismissible fade show"
                     role="alert">

                    <?= e($_SESSION['pesan_error']); ?>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>

                </div>

                <?php unset($_SESSION['pesan_error']); ?>

            <?php endif; ?>

            <!-- Form Edit Pelanggaran -->
            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Edit Catatan Pelanggaran
                </div>

                <div class="card-body">

                    <form action="proses_edit_catat_pelanggaran.php"
                          method="POST">

                        <input type="hidden"
                               name="id"
                               value="<?= (int) $data['id']; ?>">

                        <div class="row">

                            <!-- Siswa -->
                            <div class="col-md-6 mb-3">

                                <label for="siswa_id" class="form-label">
                                    Siswa
                                </label>

                                <select name="siswa_id"
                                        id="siswa_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Siswa --
                                    </option>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                        <option
                                            value="<?= (int) $s['id']; ?>"
                                            <?= ($data['siswa_id'] == $s['id']) ? 'selected' : ''; ?>>

                                            <?= e($s['nis'] . ' - ' . $s['nama']); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- Tahun Ajaran -->
                            <div class="col-md-6 mb-3">

                                <label for="tahun_ajaran_id" class="form-label">
                                    Tahun Ajaran
                                </label>

                                <select name="tahun_ajaran_id"
                                        id="tahun_ajaran_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Tahun Ajaran --
                                    </option>

                                    <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>

                                        <option
                                            value="<?= (int) $ta['id']; ?>"
                                            <?= ($data['tahun_ajaran_id'] == $ta['id']) ? 'selected' : ''; ?>>

                                            <?= e($ta['nama']); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- Kelas -->
                            <div class="col-md-6 mb-3">

                                <label for="kelas_id" class="form-label">
                                    Kelas
                                </label>

                                <select name="kelas_id"
                                        id="kelas_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Kelas --
                                    </option>

                                    <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>

                                        <option
                                            value="<?= (int) $k['id']; ?>"
                                            <?= ($data['kelas_id'] == $k['id']) ? 'selected' : ''; ?>>

                                            <?= e(
                                                $k['nama'] . ' - ' .
                                                $k['tingkat'] . ' - ' .
                                                $k['jurusan']
                                            ); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- Jenis Pelanggaran -->
                            <div class="col-md-6 mb-3">

                                <label for="pelanggaran" class="form-label">
                                    Jenis Pelanggaran
                                </label>

                                <select name="pelanggaran_id"
                                        id="pelanggaran"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Jenis Pelanggaran --
                                    </option>

                                    <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>

                                        <option
                                            value="<?= (int) $p['id']; ?>"
                                            data-poin="<?= (int) $p['poin']; ?>"
                                            data-kategori="<?= (int) $p['pelanggaran_kategori_id']; ?>"
                                            <?= ($data['pelanggaran_id'] == $p['id']) ? 'selected' : ''; ?>>

                                            <?= e($p['nama'] . ' (' . $p['poin'] . ' poin)'); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                                <input type="hidden"
                                       name="pelanggaran_kategori_id"
                                       id="kategori"
                                       value="<?= (int) $data['pelanggaran_kategori_id']; ?>">

                            </div>

                            <!-- Guru Pelapor -->
                            <div class="col-md-6 mb-3">

                                <label for="guru_id" class="form-label">
                                    Guru Pelapor
                                </label>

                                <select name="guru_id"
                                        id="guru_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Guru --
                                    </option>

                                    <?php while ($g = mysqli_fetch_assoc($guru)) { ?>

                                        <option
                                            value="<?= (int) $g['id']; ?>"
                                            <?= ($data['guru_id'] == $g['id']) ? 'selected' : ''; ?>>

                                            <?= e($g['nama']); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- Tanggal -->
                            <div class="col-md-6 mb-3">

                                <label for="tanggal" class="form-label">
                                    Tanggal
                                </label>

                                <input type="date"
                                       name="tanggal"
                                       id="tanggal"
                                       class="form-control"
                                       value="<?= e($data['tanggal']); ?>"
                                       required>

                            </div>

                            <!-- Poin Pelanggaran -->
                            <div class="col-md-6 mb-3">

                                <label for="poin" class="form-label">
                                    Poin Pelanggaran
                                </label>

                                <input type="number"
                                       name="poin"
                                       id="poin"
                                       class="form-control"
                                       min="0"
                                       value="<?= (int) $data['poin']; ?>"
                                       required>

                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">

                                <label for="status" class="form-label">
                                    Status
                                </label>

                                <select name="status"
                                        id="status"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Status --
                                    </option>

                                    <?php
                                    $statusList = [
                                        'Ditindaklanjuti',
                                        'Proses',
                                        'Selesai'
                                    ];

                                    foreach ($statusList as $st) {
                                    ?>

                                        <option
                                            value="<?= e($st); ?>"
                                            <?= ($data['status'] == $st) ? 'selected' : ''; ?>>

                                            <?= e($st); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- Keterangan -->
                            <div class="col-md-6 mb-3">

                                <label for="keterangan" class="form-label">
                                    Keterangan
                                </label>

                                <textarea name="keterangan"
                                          id="keterangan"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Masukkan keterangan pelanggaran"><?= e($data['keterangan']); ?></textarea>

                            </div>

                            <!-- Tindakan -->
                            <div class="col-md-6 mb-3">

                                <label for="tindakan" class="form-label">
                                    Tindakan
                                </label>

                                <textarea name="tindakan"
                                          id="tindakan"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Masukkan tindakan yang diberikan"><?= e($data['tindakan']); ?></textarea>

                            </div>

                        </div>

                        <!-- Tombol -->
                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                Update Pelanggaran
                            </button>

                            <a href="catat_pelanggaran.php"
                               class="btn btn-secondary">
                                Batal
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Mengubah poin dan kategori otomatis -->
<script>
const pilihanPelanggaran = document.getElementById('pelanggaran');
const inputPoin = document.getElementById('poin');
const inputKategori = document.getElementById('kategori');

pilihanPelanggaran.addEventListener('change', function () {

    const opsi = this.options[this.selectedIndex];

    inputPoin.value = opsi.dataset.poin || '';
    inputKategori.value = opsi.dataset.kategori || '';

});
</script>

</body>
</html>