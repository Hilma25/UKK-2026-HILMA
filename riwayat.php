
<?php
// riwayat.php

// ==========================================
// 1. SESSION DAN KONEKSI
// ==========================================
include 'includes/cek_session.php';
include 'config/koneksi.php';

// ==========================================
// 2. MENGAMBIL INPUT PENCARIAN DAN FILTER
// ==========================================
$cari = isset($_GET['cari'])
    ? mysqli_real_escape_string($koneksi, trim($_GET['cari']))
    : '';

$status = isset($_GET['status'])
    ? mysqli_real_escape_string($koneksi, trim($_GET['status']))
    : '';

// ==========================================
// 3. MENGAMBIL ID USER LOGIN
// ==========================================
$id_user = (int) ($_SESSION['id_user'] ?? $_SESSION['id'] ?? 0);

// ==========================================
// 4. QUERY DATA RIWAYAT
// ==========================================
$sql = "SELECT p.*
        FROM t_pelanggaran_siswa p
        INNER JOIN t_guru g ON p.guru_id = g.id
        WHERE g.user_id = $id_user";

// ==========================================
// 5. PENCARIAN
// ==========================================
if ($cari != '') {
    $sql .= " AND (
        p.nama_siswa LIKE '%$cari%'
        OR p.nama_pelanggaran LIKE '%$cari%'
        OR p.nama_kelas LIKE '%$cari%'
        OR p.nama_guru LIKE '%$cari%'
    )";
}

// ==========================================
// 6. FILTER STATUS
// ==========================================
if ($status != '') {
    $sql .= " AND p.status = '$status'";
}

// ==========================================
// 7. URUTKAN DATA TERBARU
// ==========================================
$sql .= " ORDER BY p.tanggal DESC, p.id DESC";

// ==========================================
// 8. MENJALANKAN QUERY
// ==========================================
$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Gagal mengambil data: ' . mysqli_error($koneksi));
}

// Menghitung jumlah data
$jumlah = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Riwayat Pelanggaran Siswa</title>

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

            <!-- JUDUL HALAMAN -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2>Riwayat Pelanggaran Siswa</h2>
                    <p class="text-muted mb-0">
                        Daftar catatan pelanggaran siswa yang telah dicatat.
                    </p>
                </div>

                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>
            </div>

            <!-- JUMLAH DATA -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="text-muted">Jumlah Riwayat Ditampilkan</h6>
                    <h3 class="fw-bold text-primary">
                        <?= $jumlah; ?> data
                    </h3>
                </div>
            </div>

            <!-- PENCARIAN DAN FILTER -->
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pencarian dan Filter Riwayat</h5>
                </div>

                <div class="card-body">

                    <form method="GET" action="riwayat.php">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="cari" class="form-label">
                                    Cari Riwayat
                                </label>

                                <input
                                    type="text"
                                    name="cari"
                                    id="cari"
                                    class="form-control"
                                    placeholder="Nama siswa, pelanggaran, kelas, guru..."
                                    value="<?= htmlspecialchars($cari); ?>">
                            </div>

                            <div class="col-md-3">
                                <label for="status" class="form-label">
                                    Filter Status
                                </label>

                                <select name="status" id="status"
                                        class="form-select">

                                    <option value="">Semua Status</option>

                                    <option value="Ditindaklanjuti"
                                        <?= $status == 'Ditindaklanjuti' ? 'selected' : ''; ?>>
                                        Ditindaklanjuti
                                    </option>

                                    <option value="Proses"
                                        <?= $status == 'Proses' ? 'selected' : ''; ?>>
                                        Proses
                                    </option>

                                    <option value="Selesai"
                                        <?= $status == 'Selesai' ? 'selected' : ''; ?>>
                                        Selesai
                                    </option>

                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end gap-2">

                                <button type="submit" class="btn btn-primary">
                                    Cari
                                </button>

                                <a href="riwayat.php" class="btn btn-secondary">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>
            </div>

            <!-- TABEL RIWAYAT -->
            <div class="card shadow-sm">

                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Data Riwayat Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelRiwayat"
                               class="table table-striped table-bordered table-hover align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Guru Pencatat</th>
                                    <th>Keterangan</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if ($jumlah > 0) { ?>

                                    <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                                        <tr>

                                            <td></td>

                                            <td>
                                                <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                            </td>

                                            <td class="text-center">
                                                <span class="badge bg-warning text-dark">
                                                    <?= htmlspecialchars((string) ($data['poin'] ?? 0)); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['tindakan'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?php
                                                $status_data = strtolower(
                                                    trim($data['status'] ?? '')
                                                );

                                                if ($status_data == 'selesai') {
                                                    echo '<span class="badge bg-success">Selesai</span>';

                                                } elseif ($status_data == 'proses') {
                                                    echo '<span class="badge bg-warning text-dark">Proses</span>';

                                                } elseif ($status_data == 'ditindaklanjuti') {
                                                    echo '<span class="badge bg-info text-dark">Ditindaklanjuti</span>';

                                                } else {
                                                    echo '<span class="badge bg-secondary">'
                                                        . htmlspecialchars($data['status'] ?? '-')
                                                        . '</span>';
                                                }
                                                ?>
                                            </td>

                                        </tr>

                                    <?php } ?>

                                <?php } else { ?>

                                    <tr>
                                        <td colspan="10" class="text-center">
                                            Belum ada riwayat pelanggaran
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- INISIALISASI DATATABLES -->
<script>
$(document).ready(function () {

    $('#tabelRiwayat').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,

        ordering: true,

        paging: true,

        order: [[1, 'desc']],

        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: 0
            }
        ],

        drawCallback: function () {

            var api = this.api();

            var nomorAwal = api.page.info().start;

            api.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, i) {

                cell.innerHTML = nomorAwal + i + 1;

            });

        },

        language: {

            search: "Cari di tabel:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data riwayat",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Riwayat pelanggaran tidak ditemukan",

            emptyTable: "Belum ada riwayat pelanggaran",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }

        }

    });

});
</script>

</body>
</html>
