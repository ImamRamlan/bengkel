<?php
session_start();
require_once 'koneksi.php';

// Cek apakah id_suku_cadang ada dalam query string
if (!isset($_GET['id_suku_cadang']) || empty($_GET['id_suku_cadang'])) {
    $_SESSION['error'] = "ID suku cadang tidak ditemukan!";
    header("Location: data_suku_cadang.php");
    exit();
}

$id_suku_cadang = $_GET['id_suku_cadang'];

// Ambil data suku cadang berdasarkan ID
$stmt = $koneksi->prepare("SELECT * FROM suku_cadang WHERE id_suku_cadang = ?");
$stmt->bind_param("i", $id_suku_cadang);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Suku cadang tidak ditemukan!";
    header("Location: data_suku_cadang.php");
    exit();
}

$data_suku_cadang = $result->fetch_assoc();
?>

<?php require_once 'include/header.php'; ?>
<?php require_once 'include/navbar.php'; ?>
<?php require_once 'include/sidebar.php'; ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Suku Cadang</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Suku Cadang</li>
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

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Suku Cadang</h3>
                        </div>
                        <div class="card-body">
                            <form action="proses_suku_cadang/p_edit_suku_cadang.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id_suku_cadang" value="<?php echo $data_suku_cadang['id_suku_cadang']; ?>">

                                <div class="form-group">
                                    <label for="nama_suku_cadang">Nama Suku Cadang</label>
                                    <input type="text" id="nama_suku_cadang" name="nama_suku_cadang" class="form-control" value="<?php echo htmlspecialchars($data_suku_cadang['nama_suku_cadang']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="stok">Stok</label>
                                    <input type="number" id="stok" name="stok" class="form-control" value="<?php echo $data_suku_cadang['stok']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="harga_per_unit">Harga Per Unit</label>
                                    <input type="number" id="harga_per_unit" name="harga_per_unit" class="form-control" value="<?php echo $data_suku_cadang['harga_per_unit']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputFile">Gambar Suku Cadang</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="exampleInputFile" name="gambar" required>
                                            <label class="custom-file-label" for="exampleInputFile">Pilih File</label>
                                        </div>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Upload</span>
                                        </div>
                                    </div>

                                    <br>
                                    <?php if ($data_suku_cadang['gambar']): ?>
                                        <img src="uploads/<?php echo $data_suku_cadang['gambar']; ?>" alt="Gambar Suku Cadang" width="150">
                                    <?php else: ?>
                                        <p>No image available</p>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="data_suku_cadang.php" class="btn btn-warning">Kembali</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'include/footer.php'; ?>
<script>
    $(function() {
        bsCustomFileInput.init();
    });
</script>
