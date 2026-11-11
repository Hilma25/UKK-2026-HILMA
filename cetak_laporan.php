<?php

include "config/koneksi.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_pelanggaran.xls");

$data = mysqli_query($koneksi, "
    SELECT 
        s.nis,
        s.nama,
        pk.nama AS pelanggaran
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s ON ps.siswa_id = s.id
    JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_id = pk.id
    ORDER BY ps.id DESC
");

?>

<table border="1">

<tr>
    <th>No</th>
    <th>NIS</th>
    <th>Nama Siswa</th>
    <th>Jenis Pelanggaran</th>
</tr>

<?php

$no = 1;

while ($row = mysqli_fetch_assoc($data)) {

?>

<tr>
    <td><?php echo $no; ?></td>
    <td><?php echo $row['nis']; ?></td>
    <td><?php echo $row['nama']; ?></td>
    <td><?php echo $row['pelanggaran']; ?></td>
</tr>

<?php

$no++;

}

?>

</table>