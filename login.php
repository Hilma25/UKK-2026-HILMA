
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Aplikasi Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container min-vh-100 d-flex justify-content-center align-items-center">

        <div class="row justify-content-center w-100">

            <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

                <!-- CARD LOGIN -->
                <div class="card border-0 shadow-lg rounded-4">

                    <div class="card-body p-4 p-md-5">

                        <!-- Logo dan Judul -->
                        <div class="text-center mb-4">

                            <div class="bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center mb-3"
                                 style="width: 85px; height: 85px;">

                                <i class="bi bi-mortarboard-fill fs-1"></i>

                            </div>

                            <h3 class="fw-bold text-dark mb-2">
                                Selamat Datang!
                            </h3>

                            <h5 class="text-primary fw-semibold">
                                Aplikasi Pelanggaran Siswa
                            </h5>

                            <p class="text-muted small mt-2 mb-0">
                                Silakan login untuk mengakses sistem
                            </p>

                        </div>

                        <hr class="mb-4">

                        <!-- Pesan Error -->
                        <?php if (isset($_SESSION['pesan_error'])): ?>

                            <div class="alert alert-danger alert-dismissible fade show"
                                 role="alert">

                                <i class="bi bi-exclamation-circle-fill me-2"></i>

                                <?= htmlspecialchars($_SESSION['pesan_error'], ENT_QUOTES, 'UTF-8'); ?>

                                <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="Close"></button>

                            </div>

                            <?php unset($_SESSION['pesan_error']); ?>

                        <?php endif; ?>

                        <!-- FORM LOGIN -->
                        <form action="proses_login.php" method="POST">

                            <!-- Email -->
                            <div class="mb-3">

                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope-fill text-primary me-1"></i>
                                    Email Address
                                </label>

                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control form-control-lg"
                                       placeholder="Masukkan email Anda"
                                       autocomplete="username"
                                       required>

                            </div>

                            <!-- Password -->
                            <div class="mb-4">

                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill text-primary me-1"></i>
                                    Password
                                </label>

                                <div class="input-group">

                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control form-control-lg border-end-0"
                                           placeholder="Masukkan password Anda"
                                           autocomplete="current-password"
                                           required>

                                    <!-- Tombol Ikon Mata -->
                                    <button class="btn btn-light border border-start-0"
                                            type="button"
                                            id="togglePassword"
                                            aria-label="Tampilkan password">

                                        <i class="bi bi-eye" id="iconPassword"></i>

                                    </button>

                                </div>

                            </div>

                            <!-- Tombol Login -->
                            <button type="submit"
                                    class="btn btn-primary btn-lg w-100 fw-semibold">

                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                LOGIN SEKARANG

                            </button>

                        </form>

                        <!-- Footer -->
                        <div class="text-center mt-4">

                            <p class="text-muted small mb-1">
                                Sistem Informasi Pengelolaan
                                Pelanggaran dan Kedisiplinan Siswa
                            </p>

                            <hr>

                            <small class="text-secondary">
                                &copy; <?= date('Y'); ?> Aplikasi Pelanggaran Siswa
                            </small>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript Tampilkan / Sembunyikan Password -->
    <script>
        const password = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const iconPassword = document.getElementById('iconPassword');

        togglePassword.addEventListener('click', function () {

            if (password.type === 'password') {

                // Tampilkan password
                password.type = 'text';

                iconPassword.classList.remove('bi-eye');
                iconPassword.classList.add('bi-eye-slash');

                togglePassword.setAttribute(
                    'aria-label',
                    'Sembunyikan password'
                );

            } else {

                // Sembunyikan password
                password.type = 'password';

                iconPassword.classList.remove('bi-eye-slash');
                iconPassword.classList.add('bi-eye');

                togglePassword.setAttribute(
                    'aria-label',
                    'Tampilkan password'
                );

            }

        });
    </script>

</body>
</html>
