<?php
session_start();
require_once 'koneksi.php';
$title = "Edit Data Pegawai | Bengkel Rinus";

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Memeriksa apakah id_pegawai ada di URL
if (!isset($_GET['id_pegawai'])) {
    $_SESSION['error'] = "ID Pegawai tidak valid.";
    header('Location: data_pegawai.php');
    exit();
}

$id_pegawai = $_GET['id_pegawai'];

// Mengambil data pegawai berdasarkan id_pegawai
try {
    $stmt = $koneksi->prepare("SELECT username, kata_sandi, nama_lengkap, role FROM pegawai WHERE id_pegawai = ?");
    $stmt->bind_param("i", $id_pegawai);
    $stmt->execute();
    $result = $stmt->get_result();

    // Memastikan pegawai ditemukan
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Pegawai tidak ditemukan.";
        header('Location: data_pegawai.php');
        exit();
    }

    $data_pegawai = $result->fetch_assoc();
} catch (Exception $e) {
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    header('Location: data_pegawai.php');
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
                    <h1 class="m-0">Edit Pegawai</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Pegawai</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i>Pesan</h5>
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Pesan</h5>
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Pegawai</h3>
                        </div>
                        <div class="card-body">
                            <form action="proses_pegawai/p_edit_pegawai.php?id_pegawai=<?php echo $id_pegawai; ?>" method="post">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($data_pegawai['username']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="kata_sandi">Password (Kosongkan jika tidak ingin diubah)</label>
                                    <input type="password" id="kata_sandi" name="kata_sandi" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="nama_lengkap">Nama Lengkap</label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" value="<?php echo htmlspecialchars($data_pegawai['nama_lengkap']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select id="role" name="role" class="form-control" required>
                                        <option value="Admin" <?php echo $data_pegawai['role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="Pegawai" <?php echo $data_pegawai['role'] === 'Pegawai' ? 'selected' : ''; ?>>Pegawai</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="data_pegawai.php" class="btn btn-warning">Kembali</a>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php require_once 'include/footer.php'; ?>
