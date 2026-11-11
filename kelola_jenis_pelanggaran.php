<?php
// kelola_jenis_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data jenis pelanggaran beserta nama kategorinya
$sql = "SELECT
            p.id,
            p.pelanggaran_kategori_id,
            pk.nama AS nama_kategori,
            p.kode,
            p.nama,
            p.poin,
            p.deskripsi,
            p.status_aktif,
            p.created_at,
            p.updated_at
        FROM t_pelanggaran p
        LEFT JOIN t_pelanggaran_kategori pk
            ON p.pelanggaran_kategori_id = pk.id
        ORDER BY p.nama ASC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Gagal mengambil data: " .
        htmlspecialchars(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Jenis Pelanggaran</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
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

        .table thead th {
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
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
            <h1 class="mb-3">Kelola Jenis Pelanggaran</h1>

            <!-- PESAN SUKSES -->
            <?php if (isset($_SESSION['pesan_sukses'])) { ?>

                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>
                </div>

                <?php unset($_SESSION['pesan_sukses']); ?>

            <?php } ?>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                </div>

                <?php unset($_SESSION['pesan_error']); ?>

            <?php } ?>


            <!-- TOMBOL NAVIGASI -->
            <div class="mb-3">

                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_jenis_pelanggaran.php"
                   class="btn btn-primary">
                    + Tambah Jenis Pelanggaran
                </a>

            </div>


            <!-- CARD DAFTAR PELANGGARAN -->
            <div class="card shadow-sm">

                <!-- HEADER CARD -->
                <div class="card-header">
                    Daftar Jenis Pelanggaran
                </div>

                <!-- BODY CARD -->
                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelPelanggaran"
                               class="table table-striped table-bordered table-hover align-middle w-100">

                            <!-- HEADER TABEL -->
                            <thead class="table-dark">

                                <tr>
                                    <th>No</th>
                                    <th>Kategori</th>
                                    <th>Kode</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Diubah</th>
                                    <th>Aksi</th>
                                </tr>

                            </thead>

                            <!-- ISI TABEL -->
                            <tbody>

                                <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                    <tr>

                                        <!-- Nomor urut otomatis -->
                                        <td></td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['kode'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= (int) $row['poin']; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['deskripsi'] ?? '-'); ?>
                                        </td>

                                        <!-- STATUS AKTIF -->
                                        <td>

                                            <?php if ((int) $row['status_aktif'] === 1) { ?>

                                                <span class="badge bg-success">
                                                    Aktif
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge bg-danger">
                                                    Tidak Aktif
                                                </span>

                                            <?php } ?>

                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['created_at'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['updated_at'] ?? '-'); ?>
                                        </td>

                                        <!-- TOMBOL AKSI -->
                                        <td>

                                            <div class="d-flex gap-2">

                                                <a href="edit_jenis_pelanggaran.php?id=<?= (int) $row['id']; ?>"
                                                   class="btn btn-warning btn-sm">
                                                    Edit
                                                </a>

                                                <a href="hapus_jenis_pelanggaran.php?id=<?= (int) $row['id']; ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?');">
                                                    Hapus
                                                </a>

                                            </div>

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


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- DataTables Bootstrap 5 -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>


<!-- INISIALISASI DATATABLES -->
<script>

$(document).ready(function () {

    var tabel = $('#tabelPelanggaran').DataTable({

        language: {

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data tidak ditemukan",

            emptyTable: "Belum ada jenis pelanggaran",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }

        },

        pageLength: 9,

        lengthMenu: [5, 9, 25, 50, 100],

        order: [[4, 'asc']],

        columnDefs: [
            {
                targets: [0, 9],
                orderable: false,
                searchable: false
            }
        ],

        // Nomor urut otomatis
        drawCallback: function () {

            var api = this.api();

            var info = api.page.info();

            api.column(0, {
                page: 'current'
            }).nodes().each(function (cell, i) {

                cell.innerHTML = info.start + i + 1;

            });

        }

    });

});

</script>

</body>
</html>