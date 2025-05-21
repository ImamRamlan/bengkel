<?php
ob_start();
session_start();
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Pengguna | Bengkel Rinus</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>Bengkel</b> Rinus</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Daftar untuk mendapatkan akun baru</p>
                <script>
                    function checkInternetConnection(event) {
                        if (!navigator.onLine) {
                            event.preventDefault(); // Hentikan pengiriman formulir
                            alert("Tidak ada koneksi internet. Silakan periksa koneksi Anda dan coba lagi.");
                        }
                    }
                </script>
                <?php if (isset($_GET['error'])) { ?>
                    <div id="error-message" class="alert alert-danger">
                        <strong>Kesalahan!</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                    <br>
                <?php } ?>
                <?php if (isset($_GET['success'])) { ?>
                    <div id="success-message" class="alert alert-success">
                        <strong>Sukses!</strong> <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                    <br>
                <?php } ?>

                <form action="proses_pengguna/p_registrasi.php" method="post" onsubmit="checkInternetConnection(event)">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <!-- pemanggilan kode logo user/orang -->
                                <span class="fas fa-user"></span> 
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="kata_sandi" placeholder="Kata Sandi" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <select class="form-control" name="role" required>
                            <option value="Admin">Admin</option>
                            <option value="Pegawai">Pegawai</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <a href="login.php" class="btn btn-success btn-block">Cancel</a>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/dist/js/adminlte.min.js"></script>
    <script>
        // Menghilangkan pesan setelah 10 detik
        setTimeout(function() {
            var errorMessage = document.getElementById('error-message');
            var successMessage = document.getElementById('success-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000); // 10000 milidetik = 10 detik
    </script>
</body>

</html>