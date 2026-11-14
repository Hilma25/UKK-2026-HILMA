
<?php
// edit_penempatan_siswa.php

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
// 2. AMBIL DATA PENEMPATAN SISWA
// ========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM t_kelas_siswa
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['pesan_error'] = 'Data penempatan siswa tidak ditemukan.';
    header('Location: penempatan_siswa.php');
    exit;
}

// ========================================
// 3. AMBIL DATA SISWA
// ========================================

$stmt_siswa = mysqli_prepare(
    $koneksi,
    "SELECT id, nis, nama, status_aktif
     FROM t_siswa
     WHERE status_aktif = 1 OR id = ?
     ORDER BY nama ASC"
);

mysqli_stmt_bind_param(
    $stmt_siswa,
    "i",
    $data['siswa_id']
);

mysqli_stmt_execute($stmt_siswa);

$siswa = mysqli_stmt_get_result($stmt_siswa);

// ========================================
// 4. AMBIL DATA TAHUN AJARAN
// ========================================

$stmt_tahun = mysqli_prepare(
    $koneksi,
    "SELECT id, nama, status_aktif
     FROM t_tahun_ajaran
     WHERE status_aktif = 1 OR id = ?
     ORDER BY id DESC"
);

mysqli_stmt_bind_param(
    $stmt_tahun,
    "i",
    $data['tahun_ajaran_id']
);

mysqli_stmt_execute($stmt_tahun);

$tahun = mysqli_stmt_get_result($stmt_tahun);

// ========================================
// 5. AMBIL DATA KELAS
// ========================================

$stmt_kelas = mysqli_prepare(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan, status_aktif
     FROM t_kelas
     WHERE status_aktif = 1 OR id = ?
     ORDER BY nama ASC"
);

mysqli_stmt_bind_param(
    $stmt_kelas,
    "i",
    $data['kelas_id']
);

mysqli_stmt_execute($stmt_kelas);

$kelas = mysqli_stmt_get_result($stmt_kelas);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Penempatan Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <!-- JUDUL DAN TOMBOL KEMBALI -->
            <div class="mb-4">

                <h2 class="mb-3">Edit Penempatan Siswa</h2>

                <a href="penempatan_siswa.php"
                   class="btn btn-secondary">
                    Kembali ke Data Penempatan Siswa
                </a>

            </div>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])): ?>

                <div class="alert alert-danger alert-dismissible fade show"
                     role="alert">

                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>

                </div>

                <?php unset($_SESSION['pesan_error']); ?>

            <?php endif; ?>

            <!-- FORM EDIT PENEMPATAN SISWA -->
            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Edit Penempatan Siswa
                </div>

                <div class="card-body">

                    <form action="proses_edit_penempatan_siswa.php"
                          method="POST">

                        <input type="hidden"
                               name="id"
                               value="<?= (int) $data['id']; ?>">

                        <div class="row">

                            <!-- SISWA -->
                            <div class="col-md-6 mb-3">

                                <label for="siswa_id" class="form-label">
                                    Siswa
                                </label>

                                <select class="form-select"
                                        id="siswa_id"
                                        name="siswa_id"
                                        required>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)): ?>

                                        <option
                                            value="<?= (int) $s['id']; ?>"
                                            <?= (int) $s['id'] === (int) $data['siswa_id']
                                                ? 'selected'
                                                : ''; ?>>

                                            <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>

                                            <?= (int) $s['status_aktif'] === 0
                                                ? ' (Tidak Aktif)'
                                                : ''; ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <!-- TAHUN AJARAN -->
                            <div class="col-md-6 mb-3">

                                <label for="tahun_ajaran_id" class="form-label">
                                    Tahun Ajaran
                                </label>

                                <select class="form-select"
                                        id="tahun_ajaran_id"
                                        name="tahun_ajaran_id"
                                        required>

                                    <?php while ($ta = mysqli_fetch_assoc($tahun)): ?>

                                        <option
                                            value="<?= (int) $ta['id']; ?>"
                                            <?= (int) $ta['id'] === (int) $data['tahun_ajaran_id']
                                                ? 'selected'
                                                : ''; ?>>

                                            <?= htmlspecialchars($ta['nama']); ?>

                                            <?= (int) $ta['status_aktif'] === 0
                                                ? ' (Tidak Aktif)'
                                                : ''; ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <!-- KELAS -->
                            <div class="col-md-6 mb-3">

                                <label for="kelas_id" class="form-label">
                                    Kelas
                                </label>

                                <select class="form-select"
                                        id="kelas_id"
                                        name="kelas_id"
                                        required>

                                    <?php while ($k = mysqli_fetch_assoc($kelas)): ?>

                                        <option
                                            value="<?= (int) $k['id']; ?>"
                                            <?= (int) $k['id'] === (int) $data['kelas_id']
                                                ? 'selected'
                                                : ''; ?>>

                                            <?= htmlspecialchars(
                                                $k['nama'] . ' - Tingkat ' .
                                                $k['tingkat'] . ' - ' .
                                                $k['jurusan']
                                            ); ?>

                                            <?= (int) $k['status_aktif'] === 0
                                                ? ' (Tidak Aktif)'
                                                : ''; ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <!-- TANGGAL MULAI -->
                            <div class="col-md-6 mb-3">

                                <label for="tanggal_mulai" class="form-label">
                                    Tanggal Mulai
                                </label>

                                <input type="date"
                                       class="form-control"
                                       id="tanggal_mulai"
                                       name="tanggal_mulai"
                                       value="<?= htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>"
                                       required>

                            </div>

                            <!-- TANGGAL SELESAI -->
                            <div class="col-md-6 mb-3">

                                <label for="tanggal_selesai" class="form-label">
                                    Tanggal Selesai
                                </label>

                                <input type="date"
                                       class="form-control"
                                       id="tanggal_selesai"
                                       name="tanggal_selesai"
                                       value="<?= htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>"
                                       required>

                            </div>

                            <!-- STATUS AKTIF -->
                            <div class="col-md-6 mb-3">

                                <label for="status_aktif" class="form-label">
                                    Status Penempatan
                                </label>

                                <select class="form-select"
                                        id="status_aktif"
                                        name="status_aktif"
                                        required>

                                    <option value="1"
                                        <?= (int) $data['status_aktif'] === 1
                                            ? 'selected'
                                            : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= (int) $data['status_aktif'] === 0
                                            ? 'selected'
                                            : ''; ?>>
                                        Tidak Aktif
                                    </option>

                                </select>

                            </div>

                        </div>

                        <hr>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                Update Penempatan Siswa
                            </button>

                            <a href="penempatan_siswa.php"
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
