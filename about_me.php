
<?php
// about_me.php

include 'includes/cek_session.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">

            <?php include 'layout/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 p-4">

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">About Me</h2>
                        <p class="text-muted mb-0">
                            Mengenal lebih dekat pembuat aplikasi.
                        </p>
                    </div>

                    <span class="badge bg-primary fs-6 p-2">
                        <i class="bi bi-person-circle me-1"></i>
                        Developer
                    </span>
                </div>

                <!-- Profil Utama -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">

                        <div class="row align-items-center">

                            <!-- Foto -->
                            <div class="col-lg-4 text-center mb-4 mb-lg-0">

                                <img src="assets/foto_profil.jpg"
                                     alt="Foto Profil"
                                     class="img-fluid rounded-4 shadow-sm"
                                     width="250">

                                <div class="mt-3">
                                    <span class="badge bg-success p-2">
                                        <i class="bi bi-mortarboard me-1"></i>
                                        Siswa RPL
                                    </span>
                                </div>

                            </div>

                            <!-- Perkenalan -->
                            <div class="col-lg-8">

                                <span class="text-primary fw-semibold">
                                    HALO, PERKENALKAN SAYA
                                </span>

                                <h1 class="fw-bold mt-2">
                                    Hilma Himmatunnajah
                                </h1>

                                <h5 class="text-secondary mb-3">
                                    Siswa Rekayasa Perangkat Lunak
                                </h5>

                                <p class="text-muted">
                                    Saya merupakan siswa SMK Muhammadiyah
                                    Kota Tasikmalaya kelas XII RPL 1.
                                    Saya memiliki ketertarikan dalam bidang
                                    teknologi, khususnya pemrograman,
                                    pengembangan website, pengelolaan database,
                                    dan desain antarmuka aplikasi.
                                </p>

                                <p class="text-muted">
                                    Melalui pembelajaran Rekayasa Perangkat
                                    Lunak, saya terus mengembangkan kemampuan
                                    untuk membuat aplikasi yang bermanfaat,
                                    mudah digunakan, dan dapat membantu
                                    menyelesaikan permasalahan sehari-hari.
                                </p>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- Informasi Pribadi -->
                <h4 class="fw-bold mb-3">
                    <i class="bi bi-person-vcard text-primary me-2"></i>
                    Informasi Pribadi
                </h4>

                <div class="row g-3 mb-4">

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="bi bi-person-fill fs-3 text-primary"></i>
                                <p class="text-muted mt-2 mb-1">Nama Lengkap</p>
                                <h6 class="fw-bold">Hilma Himmatunnajah</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="bi bi-building fs-3 text-primary"></i>
                                <p class="text-muted mt-2 mb-1">Sekolah</p>
                                <h6 class="fw-bold">SMK Muhammadiyah Kota Tasikmalaya</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="bi bi-book fs-3 text-primary"></i>
                                <p class="text-muted mt-2 mb-1">Kelas</p>
                                <h6 class="fw-bold">XII RPL 1</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="bi bi-code-slash fs-3 text-primary"></i>
                                <p class="text-muted mt-2 mb-1">Jurusan</p>
                                <h6 class="fw-bold">Rekayasa Perangkat Lunak</h6>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Keahlian -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">

                        <div class="row align-items-center">

                            <div class="col-md-4 mb-3 mb-md-0">
                                <h4 class="fw-bold">
                                    <i class="bi bi-lightbulb text-primary me-2"></i>
                                    Keahlian
                                </h4>

                                <p class="text-muted mb-0">
                                    Teknologi dan keterampilan yang
                                    saya pelajari.
                                </p>
                            </div>

                            <div class="col-md-8">

                                <span class="badge bg-primary fs-6 p-2 m-1">HTML</span>
                                <span class="badge bg-primary fs-6 p-2 m-1">CSS</span>
                                <span class="badge bg-primary fs-6 p-2 m-1">PHP</span>
                                <span class="badge bg-success fs-6 p-2 m-1">MySQL</span>
                                <span class="badge bg-info text-dark fs-6 p-2 m-1">Bootstrap</span>
                                <span class="badge bg-warning text-dark fs-6 p-2 m-1">JavaScript</span>
                                <span class="badge bg-secondary fs-6 p-2 m-1">Design</span>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- Tentang Aplikasi -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">

                        <div class="row">

                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                <i class="bi bi-laptop display-3 text-primary"></i>
                            </div>

                            <div class="col-md-10">

                                <h4 class="fw-bold">
                                    Tentang Aplikasi
                                </h4>

                                <p class="text-muted">
                                    Aplikasi Pelanggaran Siswa merupakan
                                    sistem informasi berbasis web yang
                                    dirancang untuk mempermudah proses
                                    pencatatan, pengelolaan, dan pemantauan
                                    data pelanggaran siswa di lingkungan
                                    sekolah.
                                </p>

                                <p class="text-muted mb-0">
                                    Sistem ini menyediakan pengelolaan data
                                    siswa, guru, kelas, tahun ajaran,
                                    wali kelas, kategori pelanggaran,
                                    penempatan siswa, tindakan, riwayat,
                                    serta rekapitulasi poin pelanggaran
                                    siswa agar administrasi sekolah menjadi
                                    lebih efektif dan terstruktur.
                                </p>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- Tujuan Aplikasi -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-bullseye text-primary me-2"></i>
                            Tujuan Pembuatan Aplikasi
                        </h4>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Mempermudah pencatatan pelanggaran siswa.</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Mengelola data siswa dan guru secara terstruktur.</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Memantau poin pelanggaran setiap siswa.</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                    <span>Menyediakan riwayat dan laporan pelanggaran.</span>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-muted mt-4 mb-3">
                    <small>
                        &copy; 2026 Aplikasi Pelanggaran Siswa
                        <br>
                        Created by <strong>Hilma Himmatunnajah</strong>
                        <i class="bi bi-heart-fill text-danger"></i>
                    </small>
                </div>

            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
