<?php
ob_start();
session_start();
require_once 'koneksi.php';
$title = "Data Pembelian | Bengkel Rinus";

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'include/header.php';
require_once 'include/navbar.php';
require_once 'include/sidebar.php';

try {
    // Mengambil data dari tabel suku cadang
    $stmt = $koneksi->prepare("SELECT id_suku_cadang, nama_suku_cadang, stok, harga_per_unit, gambar FROM suku_cadang");
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pembelian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pembelian</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
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

            <div class="row">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($suku_cadang = $result->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <img class="card-img-top" src="uploads/<?php echo htmlspecialchars($suku_cadang['gambar']); ?>" alt="Gambar" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($suku_cadang['nama_suku_cadang']); ?></h5>
                                    <p class="card-text">
                                        Stok: <?php echo htmlspecialchars($suku_cadang['stok']); ?><br>
                                        Harga: Rp <?php echo number_format($suku_cadang['harga_per_unit'], 2, ',', '.'); ?>
                                    </p>
                                    <a href="detail_pembelian.php?id_suku_cadang=<?php echo $suku_cadang['id_suku_cadang']; ?>" class="btn btn-secondary mt-2 btn-block">Detail</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">Data Suku Cadang tidak tersedia.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require_once 'include/footer.php'; ?>