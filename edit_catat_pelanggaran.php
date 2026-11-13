<?php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ====================================
// CEK ID CATATAN
// ====================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: catat_pelanggaran.php");
    exit;
}

$id = (int) $_GET['id'];

// ====================================
// AMBIL DATA CATATAN PELANGGARAN
// ====================================

$stmt = mysqli_prepare($koneksi, "
    SELECT
        ps.*,
        s.nis,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan,
        ta.nama AS tahun_ajaran,
        p.nama AS nama_pelanggaran
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_siswa s
        ON ps.siswa_id = s.id
    LEFT JOIN t_kelas k
        ON ps.kelas_id = k.id
    LEFT JOIN t_tahun_ajaran ta
        ON ps.tahun_ajaran_id = ta.id
    LEFT JOIN t_pelanggaran p
        ON ps.pelanggaran_id = p.id
    WHERE ps.id = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['pesan_error'] = "Data catatan pelanggaran tidak ditemukan.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// ====================================
// CEK STATUS SELESAI
// ====================================

if ($data['status'] === 'Selesai') {
    $_SESSION['pesan_error'] = "Catatan yang sudah selesai tidak dapat diedit.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// ====================================
// DAFTAR JENIS PELANGGARAN
// ====================================

$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin
     FROM t_pelanggaran
     WHERE status_aktif = 1
        OR id = " . (int) $data['pelanggaran_id'] . "
     ORDER BY nama ASC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Catat Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Edit Catat Pelanggaran</h2>

            <!-- PESAN ERROR -->

            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger">

                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>

                </div>

            <?php } ?>

            <!-- FORM EDIT -->

            <div class="card">

                <div class="card-header bg-primary text-light">
                    Form Edit Catat Pelanggaran
                </div>

                <div class="card-body">

                    <form action="proses_edit_catat_pelanggaran.php?id=<?php echo $id; ?>"
                          method="POST"
                          id="formEditPelanggaran">

                        <div class="row">

                            <!-- KELAS -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Kelas
                                </label>

                                <input type="text"
                                       class="form-control"
                                       value="<?php
                                       echo htmlspecialchars(
                                           ($data['nama_kelas'] ?? '-') . ' - ' .
                                           ($data['tingkat'] ?? '-') . ' - ' .
                                           ($data['jurusan'] ?? '-')
                                       );
                                       ?>"
                                       readonly>

                            </div>

                            <!-- NAMA SISWA -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Nama Siswa
                                </label>

                                <input type="text"
                                       class="form-control"
                                       value="<?php
                                       echo htmlspecialchars(
                                           ($data['nis'] ?? '-') . ' - ' .
                                           ($data['nama_siswa'] ?? '-')
                                       );
                                       ?>"
                                       readonly>

                            </div>

                            <!-- TAHUN AJARAN -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Tahun Ajaran
                                </label>

                                <input type="text"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($data['tahun_ajaran'] ?? '-'); ?>"
                                       readonly>

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
                                            value="<?php echo $p['id']; ?>"
                                            data-poin="<?php echo $p['poin']; ?>"
                                            <?php echo ((int) $data['pelanggaran_id'] === (int) $p['id']) ? 'selected' : ''; ?>>

                                            <?php
                                            echo htmlspecialchars($p['nama']) .
                                                ' - ' . $p['poin'] . ' poin';
                                            ?>

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
                                       value="<?php echo htmlspecialchars($data['tanggal']); ?>"
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

                                    <option value="Ditindaklanjuti"
                                        <?php echo $data['status'] === 'Ditindaklanjuti' ? 'selected' : ''; ?>>
                                        Ditindaklanjuti
                                    </option>

                                    <option value="Proses"
                                        <?php echo $data['status'] === 'Proses' ? 'selected' : ''; ?>>
                                        Proses
                                    </option>

                                    <option value="Selesai"
                                        <?php echo $data['status'] === 'Selesai' ? 'selected' : ''; ?>>
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
                                          rows="3"><?php echo htmlspecialchars($data['keterangan'] ?? ''); ?></textarea>

                            </div>

                        </div>

                        <!-- TOMBOL -->

                        <button type="submit"
                                class="btn btn-primary">

                            Simpan Perubahan

                        </button>

                        <a href="catat_pelanggaran.php"
                           class="btn btn-secondary">

                            Kembali

                        </a>

                    </form>

                </div>

            </div>

        </main>

    </div>

</div>

<script>

// ====================================
// POIN OTOMATIS
// ====================================

const pelanggaran = document.getElementById('pelanggaran');
const poin = document.getElementById('poin');

pelanggaran.addEventListener('change', function () {

    const pilihan = this.options[this.selectedIndex];

    if (pilihan.value !== '') {
        poin.value = pilihan.dataset.poin;
    } else {
        poin.value = '';
    }

});

</script>

</body>
</html>