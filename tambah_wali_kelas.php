
<?php
// tambah_wali_kelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// 1. AMBIL DATA GURU
// ========================================
$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     ORDER BY nama ASC"
);

if (!$guru) {
    die("Gagal mengambil data guru: " . mysqli_error($koneksi));
}

// ========================================
// 2. AMBIL DATA KELAS AKTIF
// ========================================
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

if (!$kelas) {
    die("Gagal mengambil data kelas: " . mysqli_error($koneksi));
}

// ========================================
// 3. AMBIL TAHUN AJARAN AKTIF
// ========================================
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

if (!$tahun) {
    die("Gagal mengambil tahun ajaran: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Wali Kelas</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-header {
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
            <h1 class="mb-3">Tambah Wali Kelas</h1>

            <!-- TOMBOL KEMBALI -->
            <div class="mb-3">

                <a href="kelola_wali_kelas.php"
                   class="btn btn-secondary">

                    Kembali ke Data Wali Kelas

                </a>

            </div>

            <!-- CARD FORM -->
            <div class="card shadow-sm">

                <!-- HEADER -->
                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        Form Tambah Wali Kelas
                    </h5>

                </div>

                <!-- BODY -->
                <div class="card-body">

                    <form action="proses_tambah_wali_kelas.php"
                          method="POST"
                          id="formTambahWaliKelas">

                        <!-- ================================= -->
                        <!-- 1. GURU -->
                        <!-- ================================= -->

                        <div class="mb-3">

                            <label for="guru_id" class="form-label">
                                Guru
                            </label>

                            <select name="guru_id"
                                    id="guru_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Pilih Guru
                                </option>

                                <?php while ($row = mysqli_fetch_assoc($guru)) { ?>

                                    <option value="<?= htmlspecialchars((string) $row['id']) ?>">

                                        <?= htmlspecialchars($row['nama']) ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- ================================= -->
                        <!-- 2. KELAS -->
                        <!-- ================================= -->

                        <div class="mb-3">

                            <label for="kelas_id" class="form-label">
                                Kelas
                            </label>

                            <select name="kelas_id"
                                    id="kelas_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Pilih Kelas
                                </option>

                                <?php while ($row = mysqli_fetch_assoc($kelas)) { ?>

                                    <option value="<?= htmlspecialchars((string) $row['id']) ?>">

                                        <?= htmlspecialchars($row['nama']) ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- ================================= -->
                        <!-- 3. TAHUN AJARAN -->
                        <!-- ================================= -->

                        <div class="mb-3">

                            <label for="tahun_ajaran_id" class="form-label">
                                Tahun Ajaran
                            </label>

                            <select name="tahun_ajaran_id"
                                    id="tahun_ajaran_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Pilih Tahun Ajaran
                                </option>

                                <?php while ($row = mysqli_fetch_assoc($tahun)) { ?>

                                    <option value="<?= htmlspecialchars((string) $row['id']) ?>">

                                        <?= htmlspecialchars($row['nama']) ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- ================================= -->
                        <!-- 4. TANGGAL MULAI -->
                        <!-- ================================= -->

                        <div class="mb-3">

                            <label for="tanggal_mulai" class="form-label">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   id="tanggal_mulai"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- ================================= -->
                        <!-- 5. TANGGAL SELESAI -->
                        <!-- ================================= -->

                        <div class="mb-3">

                            <label for="tanggal_selesai" class="form-label">
                                Tanggal Selesai
                            </label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   id="tanggal_selesai"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- ================================= -->
                        <!-- 6. STATUS -->
                        <!-- ================================= -->

                        <div class="mb-4">

                            <label for="status_aktif" class="form-label">
                                Status
                            </label>

                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1">
                                    Aktif
                                </option>

                                <option value="0">
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                        <!-- ================================= -->
                        <!-- TOMBOL AKSI -->
                        <!-- ================================= -->

                        <button type="submit"
                                name="simpan"
                                value="1"
                                class="btn btn-primary">

                            <i class="bi bi-save me-1"></i>
                            Simpan Wali Kelas

                        </button>

                        <button type="reset"
                                class="btn btn-outline-secondary">

                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset

                        </button>


                    </form>

                </div>

            </div>

        </main>

    </div>

</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
