<?php
session_start();
require_once 'koneksi.php';

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Pastikan parameter id_penjualan ada
if (isset($_GET['id_penjualan'])) {
    $id_penjualan = $_GET['id_penjualan'];

    // Mengambil data penjualan berdasarkan id_penjualan
    $stmt_penjualan = $koneksi->prepare("SELECT id_penjualan, id_suku_cadang, tanggal_penjualan, jumlah_penjualan, total_biaya FROM penjualan WHERE id_penjualan = ?");
    $stmt_penjualan->bind_param("i", $id_penjualan);
    $stmt_penjualan->execute();
    $result_penjualan = $stmt_penjualan->get_result();
    $penjualan = $result_penjualan->fetch_assoc();

    if (!$penjualan) {
        $_SESSION['error'] = "Penjualan tidak ditemukan.";
        header('Location: ../data_penjualan.php');
        exit();
    }

    // Ambil data suku cadang terkait
    $id_suku_cadang = $penjualan['id_suku_cadang'];
    $stmt_suku_cadang = $koneksi->prepare("SELECT id_suku_cadang, nama_suku_cadang, stok, harga_per_unit FROM suku_cadang WHERE id_suku_cadang = ?");
    $stmt_suku_cadang->bind_param("i", $id_suku_cadang);
    $stmt_suku_cadang->execute();
    $result_suku_cadang = $stmt_suku_cadang->get_result();
    $suku_cadang = $result_suku_cadang->fetch_assoc();
} else {
    $_SESSION['error'] = "ID penjualan tidak ditemukan.";
    header('Location: ../data_penjualan.php');
    exit();
}

require_once 'include/header.php';
require_once 'include/navbar.php';
require_once 'include/sidebar.php';
?>

<!-- HTML Form untuk Edit Penjualan -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Penjualan - <?= htmlspecialchars($suku_cadang['nama_suku_cadang']); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="data_penjualan.php">Data Penjualan</a></li>
                        <li class="breadcrumb-item active">Edit Penjualan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Pesan</h5>
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Pesan</h5>
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Penjualan</h3>
                        </div>
                        <form action="proses_penjualan/p_edit_penjualan.php" method="POST">
                            <div class="card-body">
                                <input type="hidden" name="id_suku_cadang" value="<?= htmlspecialchars($penjualan['id_suku_cadang']); ?>">
                                <input type="hidden" name="id_penjualan" value="<?= htmlspecialchars($penjualan['id_penjualan']); ?>">

                                <div class="form-group">
                                    <label for="tanggal_penjualan">Tanggal Penjualan</label>
                                    <input type="date" name="tanggal_penjualan" id="tanggal_penjualan" class="form-control" value="<?= htmlspecialchars($penjualan['tanggal_penjualan']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="jumlah_penjualan">Jumlah Penjualan</label>
                                    <input type="number" name="jumlah_penjualan" id="jumlah_penjualan" class="form-control" value="<?= htmlspecialchars($penjualan['jumlah_penjualan']); ?>" required min="1" max="<?= $suku_cadang['stok']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="harga_per_unit">Harga per Unit</label>
                                    <input type="number" name="harga_per_unit" id="harga_per_unit" class="form-control" value="<?= htmlspecialchars($suku_cadang['harga_per_unit']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="total_biaya">Total Biaya</label>
                                    <input type="number" name="total_biaya" id="total_biaya" class="form-control" value="<?= htmlspecialchars($penjualan['total_biaya']); ?>" required readonly>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="detail_penjualan.php?id_suku_cadang=<?= $penjualan['id_suku_cadang']; ?>" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Update total biaya ketika jumlah penjualan diubah
    document.getElementById("jumlah_penjualan").addEventListener("input", function() {
        var jumlah = this.value;
        var harga_per_unit = <?= $suku_cadang['harga_per_unit']; ?>;
        var total_biaya = jumlah * harga_per_unit;
        document.getElementById("total_biaya").value = total_biaya.toFixed(2);
    });
</script>