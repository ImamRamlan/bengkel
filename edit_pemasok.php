<?php
session_start();
require_once 'koneksi.php';
$title = "Edit Pemasok | Bengkel Rinus";

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Cek apakah id_pemasok tersedia di URL
if (!isset($_GET['id_pemasok'])) {
    $_SESSION['error'] = "ID pemasok tidak ditemukan.";
    header('Location: data_pemasok.php');
    exit();
}

$id_pemasok = $_GET['id_pemasok'];

try {
    // Ambil data pemasok berdasarkan ID
    $stmt = $koneksi->prepare("SELECT * FROM pemasok WHERE id_pemasok = ?");
    $stmt->bind_param("i", $id_pemasok);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Cek apakah data ditemukan
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Data pemasok tidak ditemukan.";
        header('Location: data_pemasok.php');
        exit();
    }

    $data_pemasok = $result->fetch_assoc();
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: data_pemasok.php');
    exit();
}

require_once 'include/header.php';
require_once 'include/navbar.php';
require_once 'include/sidebar.php';
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Pemasok</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Pemasok</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Pesan</h5>
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Pemasok</h3>
                        </div>
                        <div class="card-body">
                            <form action="proses_pemasok/p_edit_pemasok.php" method="post">
                                <input type="hidden" name="id_pemasok" value="<?php echo htmlspecialchars($data_pemasok['id_pemasok']); ?>">

                                <div class="form-group">
                                    <label for="nama_pemasok">Nama Pemasok</label>
                                    <input type="text" id="nama_pemasok" name="nama_pemasok" class="form-control" value="<?php echo htmlspecialchars($data_pemasok['nama_pemasok']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <input type="text" id="alamat" name="alamat" class="form-control" value="<?php echo htmlspecialchars($data_pemasok['alamat']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="no_telepon">No Telepon</label>
                                    <input type="text" id="no_telepon" name="no_telepon" class="form-control" value="<?php echo htmlspecialchars($data_pemasok['no_telepon']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="perusahaan">Perusahaan</label>
                                    <input type="text" id="perusahaan" name="perusahaan" class="form-control" value="<?php echo htmlspecialchars($data_pemasok['perusahaan']); ?>" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="data_pemasok.php" class="btn btn-warning">Kembali</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'include/footer.php'; ?>
