
<?php
// edit_jenis_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil ID jenis pelanggaran
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    $_SESSION['pesan_error'] = 'ID jenis pelanggaran tidak valid.';
    header('Location: kelola_jenis_pelanggaran.php');
    exit;
}

// Ambil data jenis pelanggaran
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['pesan_error'] = 'Data jenis pelanggaran tidak ditemukan.';
    header('Location: kelola_jenis_pelanggaran.php');
    exit;
}

// Ambil kategori aktif dan kategori yang sedang digunakan
$stmt_kategori = mysqli_prepare(
    $koneksi,
    "SELECT id, nama, status_aktif
     FROM t_pelanggaran_kategori
     WHERE status_aktif = 1 OR id = ?
     ORDER BY nama ASC"
);

mysqli_stmt_bind_param(
    $stmt_kategori,
    "i",
    $data['pelanggaran_kategori_id']
);

mysqli_stmt_execute($stmt_kategori);

$kategori = mysqli_stmt_get_result($stmt_kategori);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Jenis Pelanggaran</title>

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
                <h2 class="mb-3">Edit Jenis Pelanggaran</h2>

                <a href="kelola_jenis_pelanggaran.php"
                   class="btn btn-secondary">
                    Kembali ke Data Jenis Pelanggaran
                </a>
            </div>

            <!-- Pesan Error -->
            <?php if (isset($_SESSION['pesan_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show"
                     role="alert">

                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"></button>
                </div>

                <?php unset($_SESSION['pesan_error']); ?>
            <?php endif; ?>

            <!-- Form Edit Jenis Pelanggaran -->
            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Jenis Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <form action="proses_edit_jenis_pelanggaran.php"
                          method="POST">

                        <input type="hidden"
                               name="id"
                               value="<?= (int) $data['id']; ?>">

                        <!-- Kategori Pelanggaran -->
                        <div class="mb-3">
                            <label for="pelanggaran_kategori_id"
                                   class="form-label">
                                Kategori Pelanggaran
                            </label>

                            <select class="form-select"
                                    id="pelanggaran_kategori_id"
                                    name="pelanggaran_kategori_id"
                                    required>

                                <?php while ($kat = mysqli_fetch_assoc($kategori)): ?>

                                    <option
                                        value="<?= (int) $kat['id']; ?>"
                                        <?= (int) $kat['id'] === (int) $data['pelanggaran_kategori_id']
                                            ? 'selected'
                                            : ''; ?>>

                                        <?= htmlspecialchars($kat['nama']); ?>

                                        <?= (int) $kat['status_aktif'] === 0
                                            ? ' (Tidak Aktif)'
                                            : ''; ?>

                                    </option>

                                <?php endwhile; ?>

                            </select>
                        </div>

                        <!-- Kode Pelanggaran -->
                        <div class="mb-3">
                            <label for="kode" class="form-label">
                                Kode Pelanggaran
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="kode"
                                   name="kode"
                                   maxlength="30"
                                   value="<?= htmlspecialchars($data['kode'] ?? ''); ?>"
                                   placeholder="Masukkan Kode Pelanggaran"
                                   required>
                        </div>

                        <!-- Nama Pelanggaran -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Pelanggaran
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="nama"
                                   name="nama"
                                   maxlength="150"
                                   value="<?= htmlspecialchars($data['nama'] ?? ''); ?>"
                                   placeholder="Masukkan Nama Pelanggaran"
                                   required>
                        </div>

                        <!-- Poin -->
                        <div class="mb-3">
                            <label for="poin" class="form-label">
                                Poin
                            </label>

                            <input type="number"
                                   class="form-control"
                                   id="poin"
                                   name="poin"
                                   min="0"
                                   value="<?= (int) $data['poin']; ?>"
                                   placeholder="Masukkan Poin Pelanggaran"
                                   required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                Deskripsi
                            </label>

                            <textarea class="form-control"
                                      id="deskripsi"
                                      name="deskripsi"
                                      rows="4"
                                      placeholder="Masukkan Deskripsi Pelanggaran"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <!-- Status Aktif -->
                        <div class="mb-3">
                            <label for="status_aktif" class="form-label">
                                Status Aktif
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

                        <!-- Tombol -->
                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                Update Jenis Pelanggaran
                            </button>

                            <a href="kelola_jenis_pelanggaran.php"
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
