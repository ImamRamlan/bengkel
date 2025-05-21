<?php
ob_start();
session_start();
require_once 'koneksi.php';
$title = "Data Suku Cadang | Bengkel Rinus";

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
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Suku Cadang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Suku Cadang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Display Notification Message -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Pesan</h5>
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['delete'])): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Pesan</h5>
                    <?php echo $_SESSION['delete']; ?>
                    <?php unset($_SESSION['delete']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Pesan</h5>
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <!-- End Notification Message -->
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Suku Cadang</h3>
                        </div>
                        <div class="card-body">
                            <a href="tambah_suku_cadang.php" class="btn btn-primary">TAMBAH DATA <i class="fas fa-solid fa-plus"></i></a>
                            
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Suku Cadang</th>
                                        <th>Stok</th>
                                        <th>Harga per Unit</th>
                                        <th>Gambar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php
                                        $no = 1;
                                        while ($data_suku_cadang = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($data_suku_cadang['nama_suku_cadang']); ?></td>
                                                <td><?php echo htmlspecialchars($data_suku_cadang['stok']); ?></td>
                                                <td>Rp <?php echo number_format($data_suku_cadang['harga_per_unit'], 2, ',', '.'); ?></td>
                                                <td><img src="uploads/<?php echo htmlspecialchars($data_suku_cadang['gambar']); ?>" width="50" alt="Gambar Suku Cadang"></td>
                                                <td>
                                                    <a href="edit_suku_cadang.php?id_suku_cadang=<?php echo $data_suku_cadang['id_suku_cadang']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="proses_suku_cadang/p_delete_suku_cadang.php?id_suku_cadang=<?php echo $data_suku_cadang['id_suku_cadang']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus data ini?')">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php else: ?>
                                        <!-- Baris ini ditampilkan ketika tidak ada data -->
                                        <tr>
                                            <td colspan="6" class="text-center">Data Suku Cadang tidak ada.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Suku Cadang</th>
                                        <th>Stok</th>
                                        <th>Harga per Unit</th>
                                        <th>Gambar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'include/footer.php'; ?>
