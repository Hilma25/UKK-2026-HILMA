<?php
// catat_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ====================================
// DAFTAR KELAS AKTIF
// ====================================

$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// ====================================
// DAFTAR SISWA
// ====================================

$siswa = mysqli_query(
    $koneksi,
    "SELECT s.id, s.nis, s.nama, ks.kelas_id
     FROM t_siswa s
     INNER JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
     WHERE s.status_aktif = 1
       AND ks.status_aktif = 1
     ORDER BY s.nama ASC"
);

// ====================================
// TAHUN AJARAN AKTIF
// ====================================

$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

$tahunAktif = mysqli_fetch_assoc($tahun);

// ====================================
// DAFTAR JENIS PELANGGARAN
// ====================================

$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// ====================================
// DATA CATATAN PELANGGARAN
// ====================================

$sql = "SELECT
            ps.*,
            ta.nama AS tahun_ajaran,
            s.nis,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas,
            p.nama AS nama_pelanggaran
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_tahun_ajaran ta
            ON ps.tahun_ajaran_id = ta.id
        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k
            ON ps.kelas_id = k.id
        LEFT JOIN t_pelanggaran p
            ON ps.pelanggaran_id = p.id
        ORDER BY ps.id DESC";

$hasil = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Catat Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DATATABLES BOOTSTRAP 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">

    <style>

        /* ====================================
           PEMBUNGKUS PENCARIAN
        ==================================== */

        .pencarian-wrapper {
            position: relative;
            width: 100%;
        }

        /* ====================================
           HASIL PENCARIAN
        ==================================== */

        #hasilPelanggaran {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;

            z-index: 9999;

            max-height: 180px;
            overflow-y: auto;

            display: none;

            background-color: white;

            border: 1px solid #dee2e6;
            border-radius: 5px;

            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        #hasilPelanggaran .list-group-item {
            cursor: pointer;
        }

        /* ====================================
           RUANG INFORMASI POIN
           AGAR FORM TIDAK BERGESER
        ==================================== */

        #infoPoin,
        #pesanPencarian {
            min-height: 18px;
        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Catat Pelanggaran Siswa</h2>

            <!-- PESAN SUKSES -->

            <?php if (isset($_SESSION['pesan_sukses'])) { ?>

                <div class="alert alert-success">

                    <?php
                    echo htmlspecialchars($_SESSION['pesan_sukses']);
                    unset($_SESSION['pesan_sukses']);
                    ?>

                </div>

            <?php } ?>


            <!-- PESAN ERROR -->

            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger">

                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>

                </div>

            <?php } ?>


            <!-- ====================================
                 FORM CATAT PELANGGARAN
            ==================================== -->

            <div class="card mb-4">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Catat Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <form action="proses_simpan_pelanggaran.php"
                          method="POST"
                          id="formPelanggaran">

                        <div class="row">

                            <!-- ====================================
                                 KELAS
                            ==================================== -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Kelas
                                </label>

                                <select name="kelas_id"
                                        id="kelas"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Kelas --
                                    </option>

                                    <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>

                                        <option value="<?php echo $k['id']; ?>">

                                            <?php
                                            echo htmlspecialchars(
                                                $k['nama'] . ' - ' .
                                                $k['tingkat'] . ' - ' .
                                                $k['jurusan']
                                            );
                                            ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!-- ====================================
                                 NAMA SISWA
                            ==================================== -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Nama Siswa
                                </label>

                                <select name="siswa_id"
                                        id="siswa"
                                        class="form-select"
                                        required
                                        disabled>

                                    <option value="">
                                        -- Pilih Kelas Terlebih Dahulu --
                                    </option>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                        <option
                                            value="<?php echo $s['id']; ?>"
                                            data-kelas="<?php echo $s['kelas_id']; ?>">

                                            <?php
                                            echo htmlspecialchars(
                                                $s['nis'] . ' - ' . $s['nama']
                                            );
                                            ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!-- ====================================
                                 TAHUN AJARAN
                            ==================================== -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tahun Ajaran
                                </label>

                                <?php if ($tahunAktif) { ?>

                                    <input type="text"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($tahunAktif['nama']); ?>"
                                           readonly>

                                    <input type="hidden"
                                           name="tahun_ajaran_id"
                                           value="<?php echo $tahunAktif['id']; ?>">

                                <?php } else { ?>

                                    <input type="text"
                                           class="form-control"
                                           value="Belum ada tahun ajaran aktif"
                                           readonly>

                                <?php } ?>

                            </div>


                            <!-- ====================================
                                 JENIS PELANGGARAN
                            ==================================== -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Jenis Pelanggaran
                                </label>

                                <!-- PEMBUNGKUS PENCARIAN -->

                                <div class="pencarian-wrapper">

                                    <!-- INPUT PENCARIAN -->

                                    <input type="text"
                                           id="cariPelanggaran"
                                           class="form-control"
                                           placeholder="Cari jenis pelanggaran..."
                                           autocomplete="off"
                                           required>

                                    <!-- ID PELANGGARAN -->

                                    <input type="hidden"
                                           name="pelanggaran_id"
                                           id="pelanggaran">

                                    <!-- ID KATEGORI -->

                                    <input type="hidden"
                                           name="pelanggaran_kategori_id"
                                           id="kategori">

                                    <!-- POIN -->

                                    <input type="hidden"
                                           name="poin"
                                           id="poin">

                                    <!-- HASIL PENCARIAN -->

                                    <div id="hasilPelanggaran"
                                         class="list-group">

                                        <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>

                                            <button type="button"
                                                    class="list-group-item list-group-item-action pilihan-pelanggaran"
                                                    data-id="<?php echo $p['id']; ?>"
                                                    data-nama="<?php echo htmlspecialchars($p['nama'], ENT_QUOTES); ?>"
                                                    data-poin="<?php echo $p['poin']; ?>"
                                                    data-kategori="<?php echo $p['pelanggaran_kategori_id']; ?>">

                                                <?php echo htmlspecialchars($p['nama']); ?>

                                                - <?php echo $p['poin']; ?> poin

                                            </button>

                                        <?php } ?>

                                    </div>

                                </div>


                                <!-- INFORMASI POIN -->

                                <small id="infoPoin"
                                       class="text-success d-block mt-2"></small>

                                <!-- PESAN PENCARIAN -->

                                <small id="pesanPencarian"
                                       class="text-danger d-block"></small>

                            </div>


                            <!-- ====================================
                                 TANGGAL
                            ==================================== -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tanggal
                                </label>

                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?php echo date('Y-m-d'); ?>"
                                       required>

                            </div>


                            <!-- ====================================
                                 STATUS
                            ==================================== -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Status
                                </label>

                                <select name="status"
                                        class="form-select"
                                        required>

                                    <option value="Ditindaklanjuti">
                                        Ditindaklanjuti
                                    </option>

                                    <option value="Proses">
                                        Proses
                                    </option>

                                    <option value="Selesai">
                                        Selesai
                                    </option>

                                </select>

                            </div>


                            <!-- ====================================
                                 KETERANGAN
                            ==================================== -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Keterangan
                                </label>

                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="3"></textarea>

                            </div>

                        </div>


                        <!-- TOMBOL SIMPAN -->

                        <button type="submit"
                                class="btn btn-primary"
                                <?php echo !$tahunAktif ? 'disabled' : ''; ?>>

                            Simpan Pelanggaran

                        </button>


                        <!-- TOMBOL KEMBALI -->

                        <a href="dashboard.php"
                           class="btn btn-secondary">

                            Kembali

                        </a>

                    </form>

                </div>

            </div>


            <!-- ====================================
                 DATA CATATAN PELANGGARAN
            ==================================== -->

            <div class="card">

                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Data Catatan Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelPelanggaran" class="table table-bordered table-striped">

                            <thead class="table-dark">

                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Pelanggaran</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php $no = 1; ?>

                                <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                    <tr>

                                        <td>
                                            <?php echo $no++; ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['poin'] ?? '0'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['status'] ?? '-'); ?>
                                        </td>

                                        <td>

                                            <?php if ($row['status'] !== 'Selesai') { ?>

                                                <a href="edit_catat_pelanggaran.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-warning btn-sm">

                                                    Edit

                                                </a>

                                                <a href="hapus_pelanggaran.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus pelanggaran ini?')">

                                                    Hapus

                                                </a>

                                            <?php } else { ?>

                                                <span class="text-muted">Tidak ada aksi</span>

                                            <?php } ?>

                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </main>

    </div>

</div>


<!-- ====================================
     JAVASCRIPT
==================================== -->

<script>

// ====================================
// FILTER SISWA BERDASARKAN KELAS
// ====================================

const pilihanKelas = document.getElementById('kelas');
const pilihanSiswa = document.getElementById('siswa');

const semuaSiswa = Array.from(pilihanSiswa.options);

pilihanKelas.addEventListener('change', function () {

    const kelasDipilih = this.value;

    pilihanSiswa.value = '';
    pilihanSiswa.disabled = !kelasDipilih;

    semuaSiswa.forEach(function (opsi, index) {

        if (index === 0) {

            opsi.hidden = false;

            opsi.textContent = kelasDipilih
                ? '-- Pilih Siswa --'
                : '-- Pilih Kelas Terlebih Dahulu --';

            return;

        }

        opsi.hidden = opsi.dataset.kelas !== kelasDipilih;

    });

});


// ====================================
// PENCARIAN JENIS PELANGGARAN
// ====================================

const cariPelanggaran = document.getElementById('cariPelanggaran');

const hasilPelanggaran = document.getElementById('hasilPelanggaran');

const inputPelanggaran = document.getElementById('pelanggaran');

const inputKategori = document.getElementById('kategori');

const inputPoin = document.getElementById('poin');

const infoPoin = document.getElementById('infoPoin');

const pesanPencarian = document.getElementById('pesanPencarian');

const semuaPelanggaran = Array.from(
    document.querySelectorAll('.pilihan-pelanggaran')
);


// ====================================
// PROSES PENCARIAN
// ====================================

cariPelanggaran.addEventListener('input', function () {

    const kataKunci = this.value.toLowerCase().trim();

    // Kosongkan pilihan sebelumnya

    inputPelanggaran.value = '';
    inputKategori.value = '';
    inputPoin.value = '';

    infoPoin.textContent = '';
    pesanPencarian.textContent = '';

    // Jika pencarian kosong

    if (kataKunci === '') {

        semuaPelanggaran.forEach(function (item) {

            item.style.display = 'none';

        });

        hasilPelanggaran.style.display = 'none';

        return;

    }

    let jumlahHasil = 0;

    // Cari nama pelanggaran

    semuaPelanggaran.forEach(function (item) {

        const nama = item.dataset.nama.toLowerCase();

        if (nama.includes(kataKunci)) {

            item.style.display = 'block';

            jumlahHasil++;

        } else {

            item.style.display = 'none';

        }

    });

    // Tampilkan daftar tanpa menggeser form

    hasilPelanggaran.style.display = 'block';

    if (jumlahHasil === 0) {

        pesanPencarian.textContent =
            'Jenis pelanggaran tidak ditemukan.';

    }

});


// ====================================
// MEMILIH JENIS PELANGGARAN
// ====================================

hasilPelanggaran.addEventListener('click', function (e) {

    const item = e.target.closest('.pilihan-pelanggaran');

    if (!item) {
        return;
    }

    // Isi nama pelanggaran

    cariPelanggaran.value = item.dataset.nama;

    // Isi ID pelanggaran

    inputPelanggaran.value = item.dataset.id;

    // Isi kategori

    inputKategori.value = item.dataset.kategori;

    // Isi poin otomatis

    inputPoin.value = item.dataset.poin;

    // Tampilkan informasi poin

    infoPoin.textContent =
        'Poin pelanggaran: ' + item.dataset.poin + ' poin';

    pesanPencarian.textContent = '';

    // Sembunyikan daftar pencarian

    hasilPelanggaran.style.display = 'none';

});


// ====================================
// TAMPILKAN HASIL SAAT INPUT DIKLIK
// ====================================

cariPelanggaran.addEventListener('focus', function () {

    const kataKunci = this.value.toLowerCase().trim();

    if (kataKunci === '') {

        hasilPelanggaran.style.display = 'none';

        return;

    }

    let jumlahHasil = 0;

    semuaPelanggaran.forEach(function (item) {

        const cocok = item.dataset.nama
            .toLowerCase()
            .includes(kataKunci);

        item.style.display = cocok ? 'block' : 'none';

        if (cocok) {
            jumlahHasil++;
        }

    });

    hasilPelanggaran.style.display = 'block';

    if (jumlahHasil === 0) {

        pesanPencarian.textContent =
            'Jenis pelanggaran tidak ditemukan.';

    } else {

        pesanPencarian.textContent = '';

    }

});


// ====================================
// SEMBUNYIKAN HASIL JIKA KLIK DI LUAR
// ====================================

document.addEventListener('click', function (e) {

    if (
        !e.target.closest('.pencarian-wrapper')
    ) {

        hasilPelanggaran.style.display = 'none';

    }

});


// ====================================
// VALIDASI SEBELUM MENYIMPAN
// ====================================

document.getElementById('formPelanggaran')
    .addEventListener('submit', function (e) {

        if (!inputPelanggaran.value) {

            e.preventDefault();

            alert(
                'Silakan cari dan pilih jenis pelanggaran terlebih dahulu.'
            );

            cariPelanggaran.focus();

        }

    });

</script>


<!-- ====================================
     DATATABLES
==================================== -->

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    new DataTable('#tabelPelanggaran', {

        pageLength: 10,

        lengthChange: true,

        searching: true,

        language: {

            lengthMenu: "Tampilkan _MENU_ data",

            search: "Cari:",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data tidak ditemukan",

            emptyTable: "Tidak ada data yang tersedia",

            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Berikutnya",
                last: "Terakhir"
            }

        }

    });

});

</script>

</body>
</html>