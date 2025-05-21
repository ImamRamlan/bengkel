<?php
ob_start();
session_start();

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

include 'include/header.php';
$title = "Dashboard Admin | Bengkel";
include 'include/navbar.php';
include 'include/sidebar.php';
include 'koneksi.php'; // Include koneksi database

// Mengambil jumlah data dari setiap tabel yang relevan
$totalPegawai = $koneksi->query("SELECT COUNT(*) AS total FROM pegawai")->fetch_assoc()['total'];
$totalPemasok = $koneksi->query("SELECT COUNT(*) AS total FROM pemasok")->fetch_assoc()['total'];
$totalPembelian = $koneksi->query("SELECT COUNT(*) AS total FROM pembelian")->fetch_assoc()['total'];
$totalPenjualan = $koneksi->query("SELECT COUNT(*) AS total FROM penjualan")->fetch_assoc()['total'];
$totalSukuCadang = $koneksi->query("SELECT COUNT(*) AS total FROM suku_cadang")->fetch_assoc()['total'];
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard Admin</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard Admin</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h4>Selamat datang, <?php echo $_SESSION['username']; ?>!</h4>
                        <p>Semoga hari Anda berjalan dengan baik! Jika ada masalah, silakan hubungi super admin. Terima kasih.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Card untuk Data Pegawai -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $totalPegawai; ?></h3>
                            <p>Data Pegawai</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <a href="data_pegawai.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- Card untuk Data Pemasok -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $totalPemasok; ?></h3>
                            <p>Data Pemasok</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <a href="data_pemasok.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- Card untuk Data Pembelian -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $totalPembelian; ?></h3>
                            <p>Data Pembelian</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <a href="data_pembelian.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- Card untuk Data Penjualan -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $totalPenjualan; ?></h3>
                            <p>Data Penjualan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <a href="data_penjualan.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- Card untuk Data Suku Cadang -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?php echo $totalSukuCadang; ?></h3>
                            <p>Data Suku Cadang</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <a href="data_suku_cadang.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include 'include/footer.php'; ?>
