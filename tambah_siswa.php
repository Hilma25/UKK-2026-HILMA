<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// Mengambil data kelas
$query_kelas = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_kelas
    ORDER BY nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>


<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-4">Tambah Siswa</h1>

            <div class="mb-3">
                <a href="kelola_siswa.php" class="btn btn-secondary">
                    Kembali ke Data Siswa
                </a>
            </div>
     

<!-- FORM TAMBAH SISWA -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Form Tambah Siswa</h5>
    </div>

    <div class="card-body p-3">
        <form action="proses_tambah_siswa.php" method="POST">


          <!-- NIS -->
          <div class="mb-3">
            <label class="form-label">NIS</label>
                <input type="text" name="nis" id="nis"
                                   class="form-control" maxlength="10"
                                   pattern="[0-9]{10}" inputmode="numeric"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   title="NIS harus tepat 10 angka" required>
                <div class="form-text">NIS harus terdiri dari 10 angka.</div>
            </div>


            <!-- NISN -->
            <div class="mb-3">
            <label class="form-label">NISN</label>
                <input type="text" name="nisn" id="nisn"
                                class="form-control" maxlength="10"
                                pattern="[0-9]{10}" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                title="NISN harus tepat 10 angka" required>
                <div class="form-text">NISN harus terdiri dari 10 angka.</div>
            </div>

            <!-- Nama Siswa -->
            <div class="mb-2">
                <label for="nama" class="form-label mb-1">Nama Siswa</label>
                <input type="text" name="nama" id="nama"
                    class="form-control" required>
            </div>

            <!-- Jenis Kelamin -->
            <div class="mb-2">
                <label for="jenis_kelamin" class="form-label mb-1">
                    Jenis Kelamin
                </label>
                <select name="jenis_kelamin" id="jenis_kelamin"
                    class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <!-- Tanggal Lahir -->
            <div class="mb-2">
                <label for="tanggal_lahir" class="form-label mb-1">
                    Tanggal Lahir
                </label>
                <input type="date" name="tanggal_lahir"
                    id="tanggal_lahir"
                    class="form-control" required>
            </div>

            <!-- Alamat -->
            <div class="mb-2">
                <label for="alamat" class="form-label mb-1">Alamat</label>
                <textarea name="alamat" id="alamat"
                    class="form-control"
                    rows="3" required></textarea>
            </div>

            <!-- Status Aktif -->
            <div class="mb-2">
                <label for="status_aktif" class="form-label mb-1">
                    Status Aktif
                </label>
                <select name="status_aktif" id="status_aktif"
                    class="form-select" required>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>


                <!-- Tombol -->
                <button type="submit" name="simpan" value="1" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i>
                    Simpan Siswa
                </button>
                
                    <button type="reset" class="btn btn-outline-secondary px-4 ms-1">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<!-- Membatasi NIS dan NISN maksimal 10 angka -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const nis = document.getElementById('nis');
    const nisn = document.getElementById('nisn');

    nis.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });

    nisn.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });

});
</script>

</body>
</html>