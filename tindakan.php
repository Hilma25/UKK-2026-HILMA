
<?php
// tindakan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Proses menyimpan tindakan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    $tindakan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tindakan'] ?? '')
    );

    if ($id <= 0 || $tindakan === '') {
        $_SESSION['pesan_error'] = "Data tindakan tidak valid.";
        header('Location: tindakan.php');
        exit;
    }

    $sql_update = "UPDATE t_pelanggaran_siswa
                   SET tindakan = '$tindakan'
                   WHERE id = $id";

    if (mysqli_query($koneksi, $sql_update)) {

        $_SESSION['pesan_sukses'] = "Tindakan berhasil disimpan.";
        header('Location: tindakan.php');
        exit;

    } else {

        $_SESSION['pesan_error'] = mysqli_error($koneksi);
        header('Location: tindakan.php');
        exit;
    }
}

// Mengambil data pelanggaran siswa
$sql = "SELECT
            ps.id,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas,
            p.nama AS nama_pelanggaran,
            ps.tanggal,
            ps.keterangan,
            ps.tindakan
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k
            ON ps.kelas_id = k.id
        LEFT JOIN t_pelanggaran p
            ON ps.pelanggaran_id = p.id
        ORDER BY ps.tanggal DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die('Gagal mengambil data: ' . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tindakan Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-2">Tindakan Pelanggaran Siswa</h2>

            <p class="text-muted">
                Kelola tindakan untuk setiap pelanggaran siswa.
            </p>

            <div class="mb-3">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>
            </div>

            <!-- PESAN SUKSES -->
            <?php if (isset($_SESSION['pesan_sukses'])) { ?>

                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['pesan_sukses']) ?>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>

                <?php unset($_SESSION['pesan_sukses']); ?>

            <?php } ?>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['pesan_error']) ?>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>

                <?php unset($_SESSION['pesan_error']); ?>

            <?php } ?>

            <!-- TABEL DATA TINDAKAN -->
            <div class="card shadow-sm">

                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Data Pelanggaran Siswa</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelTindakan"
                               class="table table-striped table-bordered table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Pelanggaran</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Tindakan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                    <tr>

                                        <td></td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_siswa'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tanggal'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['keterangan'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <form
                                                id="formTindakan<?= (int) $row['id'] ?>"
                                                method="POST"
                                                action="tindakan.php">

                                                <input type="hidden"
                                                       name="id"
                                                       value="<?= (int) $row['id'] ?>">

                                                <textarea
                                                    name="tindakan"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Masukkan tindakan..."
                                                    required><?= htmlspecialchars($row['tindakan'] ?? '') ?></textarea>

                                            </form>
                                        </td>

                                        <td>
                                            <button
                                                type="submit"
                                                form="formTindakan<?= (int) $row['id'] ?>"
                                                class="btn btn-success btn-sm">

                                                Simpan

                                            </button>
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

<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- DataTables Bootstrap 5 -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {

    $('#tabelTindakan').DataTable({

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data pelanggaran",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data pelanggaran tidak ditemukan",
            emptyTable: "Belum ada data pelanggaran",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[4, 'desc']],

        columnDefs: [
            {
                targets: [0, 7],
                orderable: false,
                searchable: false
            }
        ],

        drawCallback: function () {
            var api = this.api();

            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + api.page.info().start;
            });
        }

    });

});
</script>

</body>
</html>
