<?php
//buat_user_guru.php
//jalankan file ini satu kali lewat browser untuk membuat user guru 
include 'config/koneksi.php';

$name = 'guru';
$email = 'guru@gmail.com';
$password = password_hash('guru123', PASSWORD_DEFAULT);
$role = 'guru';

$sql = "INSERT INTO t_users (name, email, password, role)";
$sql .= "VALUES ('$name','$email','$password','$role')";

if (mysqli_query($koneksi, $sql)) {
    echo 'User guru berhasil dibuat. Silakan hapus file ini. ';
} else {
    echo 'Gagal membuat user: ' . mysqli_error($koneksi);
}
