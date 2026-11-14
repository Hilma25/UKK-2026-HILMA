
<?php
// edit_wali_kelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// 1. VALIDASI ID
// ========================================
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('ID wali kelas tidak valid.');
}

// ========================================
// 2. AMBIL DATA WALI KELAS
// ========================================
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_wali_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$data) {
    exit('Data wali kelas tidak ditemukan.');
}

// ========================================
// 3. AMBIL DATA PILIHAN
// ========================================
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_guru ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Wali Kelas</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-header {
            background-color: #212529;
            color: white;
            font-weight: bold;
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="container-fluid">

        <div class="row">

            <!-- SIDEBAR -->
            <?php include 'layout/sidebar.php'; ?>

            <!-- KONTEN UTAMA -->
            <main class="col-md-9 col-lg-10 p-4">

                <!-- JUDUL HALAMAN -->
                <h1 class="mb-3">Edit Wali Kelas</h1>

                <!-- TOMBOL KEMBALI -->
                <div class="mb-3">
                    <a href="kelola_wali_kelas.php" class="btn btn-secondary">
                        Kembali ke Data Wali Kelas
                    </a>
                </div>

                <!-- CARD FORM EDIT -->
                <div class="card shadow-sm">

                    <!-- HEADER CARD -->
                    <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Edit Wali Kelas</h5>
                </div>

                    <!-- BODY CARD -->
                    <div class="card-body">

                        <form action="proses_edit_wali_kelas.php"
                            method="POST"
                            id="formEditWaliKelas">

                            <input type="hidden"
                                name="id"
                                value="<?= htmlspecialchars((string) $data['id']) ?>">

                            <!-- GURU -->
                            <div class="mb-3">
                                <label for="guru_id" class="form-label">
                                    Guru
                                </label>

                                <select name="guru_id"
                                    id="guru_id"
                                    class="form-select"
                                    required>

                                    <option value="">Pilih Guru</option>

                                    <?php while ($row = mysqli_fetch_assoc($guru)) { ?>
                                        <option
                                            value="<?= htmlspecialchars((string) $row['id']) ?>"
                                            <?= (int) $row['id'] === (int) $data['guru_id']
                                                ? 'selected' : '' ?>>

                                            <?= htmlspecialchars($row['nama']) ?>

                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <!-- KELAS -->
                            <div class="mb-3">
                                <label for="kelas_id" class="form-label">
                                    Kelas
                                </label>

                                <select name="kelas_id"
                                    id="kelas_id"
                                    class="form-select"
                                    required>

                                    <option value="">Pilih Kelas</option>

                                    <?php while ($row = mysqli_fetch_assoc($kelas)) { ?>
                                        <option
                                            value="<?= htmlspecialchars((string) $row['id']) ?>"
                                            <?= (int) $row['id'] === (int) $data['kelas_id']
                                                ? 'selected' : '' ?>>

                                            <?= htmlspecialchars($row['nama']) ?>

                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <!-- TAHUN AJARAN -->
                            <div class="mb-3">
                                <label for="tahun_ajaran_id" class="form-label">
                                    Tahun Ajaran
                                </label>

                                <select name="tahun_ajaran_id"
                                    id="tahun_ajaran_id"
                                    class="form-select"
                                    required>

                                    <option value="">Pilih Tahun Ajaran</option>

                                    <?php while ($row = mysqli_fetch_assoc($tahun)) { ?>
                                        <option
                                            value="<?= htmlspecialchars((string) $row['id']) ?>"
                                            <?= (int) $row['id'] === (int) $data['tahun_ajaran_id']
                                                ? 'selected' : '' ?>>

                                            <?= htmlspecialchars($row['nama']) ?>

                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <!-- TANGGAL MULAI -->
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">
                                    Tanggal Mulai
                                </label>

                                <input type="date"
                                    name="tanggal_mulai"
                                    id="tanggal_mulai"
                                    class="form-control"
                                    value="<?= htmlspecialchars($data['tanggal_mulai'] ?? '') ?>"
                                    required>
                            </div>

                            <!-- TANGGAL SELESAI -->
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">
                                    Tanggal Selesai
                                </label>

                                <input type="date"
                                    name="tanggal_selesai"
                                    id="tanggal_selesai"
                                    class="form-control"
                                    value="<?= htmlspecialchars($data['tanggal_selesai'] ?? '') ?>"
                                    required>
                            </div>

                            <!-- STATUS -->
                            <div class="mb-4">
                                <label for="status_aktif" class="form-label">
                                    Status
                                </label>

                                <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>

                                    <option value="1"
                                        <?= (int) $data['status_aktif'] === 1
                                            ? 'selected' : '' ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= (int) $data['status_aktif'] === 0
                                            ? 'selected' : '' ?>>
                                        Tidak Aktif
                                    </option>

                                </select>
                            </div>

                            <!-- TOMBOL AKSI -->
                            <button type="submit" class="btn btn-primary">
                                Update Wali Kelas
                            </button>

                            <a href="kelola_wali_kelas.php" class="btn btn-secondary">
                                Batal
                            </a>

                        </form>

                    </div>
                </div>

            </main>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
