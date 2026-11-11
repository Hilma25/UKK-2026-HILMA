
<?php
// catat_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ============================================
// 1. CEK ROLE DAN HAK AKSES
// ============================================

$role = $_SESSION['role'] ?? '';
$id_user = (int) ($_SESSION['id_user'] ?? 0);

$id_guru = 0;

// Jika login sebagai wali kelas
if ($role === 'wali_kelas') {

    // Cari ID guru berdasarkan ID akun login
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_guru WHERE user_id = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);

    $hasil_guru = mysqli_stmt_get_result($stmt);
    $data_guru = mysqli_fetch_assoc($hasil_guru);

    mysqli_stmt_close($stmt);

    if (!$data_guru) {
        die("Data guru tidak ditemukan.");
    }

    $id_guru = (int) $data_guru['id'];
}

// ============================================
// 2. DAFTAR SISWA
// ============================================

if ($role === 'wali_kelas') {

    $siswa = mysqli_query(
        $koneksi,
        "SELECT DISTINCT
            s.id,
            s.nis,
            s.nama
         FROM t_siswa s
         INNER JOIN t_kelas_siswa ks
            ON ks.siswa_id = s.id
         WHERE s.status_aktif = 1
           AND ks.status_aktif = 1
           AND EXISTS (
                SELECT 1
                FROM t_wali_kelas wk
                INNER JOIN t_tahun_ajaran ta
                    ON ta.id = wk.tahun_ajaran_id
                WHERE wk.guru_id = $id_guru
                  AND wk.kelas_id = ks.kelas_id
                  AND wk.tahun_ajaran_id = ks.tahun_ajaran_id
                  AND wk.status_aktif = 1
                  AND ta.status_aktif = 1
           )
         ORDER BY s.nama ASC"
    );

} else {

    $siswa = mysqli_query(
        $koneksi,
        "SELECT id, nis, nama
         FROM t_siswa
         WHERE status_aktif = 1
         ORDER BY nama ASC"
    );
}

// ============================================
// 3. DAFTAR TAHUN AJARAN
// ============================================

if ($role === 'wali_kelas') {

    $tahun = mysqli_query(
        $koneksi,
        "SELECT DISTINCT ta.id, ta.nama
         FROM t_tahun_ajaran ta
         INNER JOIN t_wali_kelas wk
            ON wk.tahun_ajaran_id = ta.id
         WHERE ta.status_aktif = 1
           AND wk.guru_id = $id_guru
           AND wk.status_aktif = 1
         ORDER BY ta.id DESC"
    );

} else {

    $tahun = mysqli_query(
        $koneksi,
        "SELECT id, nama
         FROM t_tahun_ajaran
         WHERE status_aktif = 1
         ORDER BY id DESC"
    );
}

// ============================================
// 4. DAFTAR KELAS
// ============================================

if ($role === 'wali_kelas') {

    $kelas = mysqli_query(
        $koneksi,
        "SELECT DISTINCT
            k.id,
            k.nama,
            k.tingkat,
            k.jurusan
         FROM t_kelas k
         INNER JOIN t_wali_kelas wk
            ON wk.kelas_id = k.id
         INNER JOIN t_tahun_ajaran ta
            ON ta.id = wk.tahun_ajaran_id
         WHERE k.status_aktif = 1
           AND wk.guru_id = $id_guru
           AND wk.status_aktif = 1
           AND ta.status_aktif = 1
         ORDER BY k.nama ASC"
    );

} else {

    $kelas = mysqli_query(
        $koneksi,
        "SELECT id, nama, tingkat, jurusan
         FROM t_kelas
         WHERE status_aktif = 1
         ORDER BY nama ASC"
    );
}

// ============================================
// 5. DAFTAR PELANGGARAN
// ============================================

$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT
        id,
        nama,
        poin,
        pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// ============================================
// 6. DAFTAR GURU
// ============================================

$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// ============================================
// 7. DATA CATATAN PELANGGARAN
// ============================================

$sql = "SELECT
            ps.*,
            ta.nama AS tahun_ajaran,
            s.nis,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan,
            p.nama AS nama_pelanggaran,
            g.nama AS nama_guru

        FROM t_pelanggaran_siswa ps

        LEFT JOIN t_tahun_ajaran ta
            ON ps.tahun_ajaran_id = ta.id

        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id

        LEFT JOIN t_kelas k
            ON ps.kelas_id = k.id

        LEFT JOIN t_pelanggaran p
            ON ps.pelanggaran_id = p.id

        LEFT JOIN t_guru g
            ON ps.guru_id = g.id";

// Batasi data berdasarkan kelas wali kelas
if ($role === 'wali_kelas') {

    $sql .= " WHERE EXISTS (
                SELECT 1
                FROM t_wali_kelas wk
                INNER JOIN t_tahun_ajaran ta_aktif
                    ON ta_aktif.id = wk.tahun_ajaran_id
                WHERE wk.guru_id = $id_guru
                  AND wk.kelas_id = ps.kelas_id
                  AND wk.tahun_ajaran_id = ps.tahun_ajaran_id
                  AND wk.status_aktif = 1
                  AND ta_aktif.status_aktif = 1
              )";
}

$sql .= " ORDER BY ps.id DESC";

$hasil = mysqli_query($koneksi, $sql);

// Periksa kesalahan query
if (
    !$siswa ||
    !$tahun ||
    !$kelas ||
    !$pelanggaran ||
    !$guru ||
    !$hasil
) {
    die("Terjadi kesalahan mengambil data: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Catat Pelanggaran Siswa</title>

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

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Catat Pelanggaran Siswa</h2>

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


            <!-- FORM CATAT PELANGGARAN -->
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        Form Catat Pelanggaran
                    </h5>

                </div>

                <div class="card-body">

                    <form action="proses_simpan_pelanggaran.php"
                          method="POST">

                        <div class="row">

                            <!-- SISWA -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Siswa
                                </label>

                                <select name="siswa_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Siswa --
                                    </option>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                        <option value="<?= $s['id'] ?>">

                                            <?= htmlspecialchars(
                                                $s['nis'] . ' - ' . $s['nama']
                                            ) ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!-- TAHUN AJARAN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tahun Ajaran
                                </label>

                                <select name="tahun_ajaran_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Tahun Ajaran --
                                    </option>

                                    <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>

                                        <option value="<?= $ta['id'] ?>">

                                            <?= htmlspecialchars($ta['nama']) ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!-- KELAS -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Kelas
                                </label>

                                <select name="kelas_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Kelas --
                                    </option>

                                    <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>

                                        <option value="<?= $k['id'] ?>">

                                            <?= htmlspecialchars(
                                                $k['nama'] . ' - ' .
                                                $k['tingkat'] . ' - ' .
                                                $k['jurusan']
                                            ) ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!-- JENIS PELANGGARAN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Jenis Pelanggaran
                                </label>

                                <select name="pelanggaran_id"
                                        id="pelanggaran"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Pelanggaran --
                                    </option>

                                    <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>

                                        <option
                                            value="<?= $p['id'] ?>"
                                            data-poin="<?= $p['poin'] ?>"
                                            data-kategori="<?= $p['pelanggaran_kategori_id'] ?>">

                                            <?= htmlspecialchars(
                                                $p['nama'] . ' (' .
                                                $p['poin'] . ' poin)'
                                            ) ?>

                                        </option>

                                    <?php } ?>

                                </select>

                                <input type="hidden"
                                       name="pelanggaran_kategori_id"
                                       id="kategori">

                            </div>


                            <!-- GURU PELAPOR -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Guru Pelapor
                                </label>

                                <select name="guru_id"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        -- Pilih Guru --
                                    </option>

                                    <?php while ($g = mysqli_fetch_assoc($guru)) { ?>

                                        <option value="<?= $g['id'] ?>">

                                            <?= htmlspecialchars($g['nama']) ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <!-- TANGGAL -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tanggal
                                </label>

                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?= date('Y-m-d') ?>"
                                       required>

                            </div>


                            <!-- POIN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Poin Pelanggaran
                                </label>

                                <input type="number"
                                       name="poin"
                                       id="poin"
                                       class="form-control"
                                       min="0"
                                       required>

                            </div>


                            <!-- STATUS -->
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


                            <!-- KETERANGAN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Keterangan
                                </label>

                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="3"></textarea>

                            </div>


                            <!-- TINDAKAN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tindakan
                                </label>

                                <textarea name="tindakan"
                                          class="form-control"
                                          rows="3"></textarea>

                            </div>

                        </div>


                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">

                            Simpan Pelanggaran

                        </button>

                        <a href="dashboard.php" class="btn btn-secondary">

                            Kembali

                        </a>

                    </form>

                </div>

            </div>


            <!-- TABEL DATA CATAT PELANGGARAN -->
            <div class="card shadow-sm">

                <div class="card-header bg-dark text-white">

                    <h5 class="mb-0">
                        Data Catat Pelanggaran
                    </h5>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelPelanggaran"
                               class="table table-striped table-bordered table-hover align-middle w-100">

                            <thead class="table-dark">

                                <tr>

                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Pelanggaran</th>
                                    <th>Guru</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                    <tr>

                                        <td></td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $row['nama_siswa'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $row['nama_kelas'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $row['tahun_ajaran'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $row['nama_pelanggaran'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $row['nama_guru'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $row['tanggal'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= (int) $row['poin'] ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $row['tindakan'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $row['keterangan'] ?? '-'
                                            ) ?>
                                        </td>


                                        <!-- STATUS -->
                                        <td>

                                            <?php

                                            $status = $row['status'] ?? '';

                                            $badge = 'secondary';

                                            if ($status == 'Ditindaklanjuti') {

                                                $badge = 'warning';

                                            } elseif ($status == 'Proses') {

                                                $badge = 'primary';

                                            } elseif ($status == 'Selesai') {

                                                $badge = 'success';

                                            }

                                            ?>

                                            <span class="badge bg-<?= $badge ?>">

                                                <?= htmlspecialchars(
                                                    $status ?: '-'
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- AKSI -->
                                        <td>

                                            <a href="edit_catat_pelanggaran.php?id=<?= (int) $row['id'] ?>"
                                               class="btn btn-warning btn-sm mb-1">

                                                Edit

                                            </a>

                                            <a href="hapus_catat_pelanggaran.php?id=<?= (int) $row['id'] ?>"
                                               class="btn btn-danger btn-sm mb-1"
                                               onclick="return confirm('Yakin ingin menghapus pelanggaran ini?')">

                                                Hapus

                                            </a>

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


<!-- BOOTSTRAP JAVASCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>


<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- DATATABLES -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>


<!-- OTOMATIS MENGISI POIN DAN KATEGORI -->
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


<!-- INISIALISASI DATATABLES -->
<script>

$(document).ready(function () {

    $('#tabelPelanggaran').DataTable({

        language: {

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data catatan pelanggaran",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data catatan pelanggaran tidak ditemukan",

            emptyTable: "Belum ada data catatan pelanggaran",

            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Berikutnya",

                previous: "Sebelumnya"

            }

        },

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[1, 'asc']],

        columnDefs: [

            {

                targets: [0, 11],

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
