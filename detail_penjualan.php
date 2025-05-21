<?php
ob_start();
session_start();
require_once 'koneksi.php';
$title = "Detail Penjualan | Bengkel Rinus";

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'include/header.php';
require_once 'include/navbar.php';
require_once 'include/sidebar.php';

// Cek apakah parameter id_suku_cadang ada
if (isset($_GET['id_suku_cadang'])) {
    $id_suku_cadang = $_GET['id_suku_cadang'];

    try {
        // Mengambil data suku cadang berdasarkan id_suku_cadang
        $stmt_suku_cadang = $koneksi->prepare("SELECT id_suku_cadang, nama_suku_cadang, stok, harga_per_unit, gambar FROM suku_cadang WHERE id_suku_cadang = ?");
        $stmt_suku_cadang->bind_param("i", $id_suku_cadang);
        $stmt_suku_cadang->execute();
        $result_suku_cadang = $stmt_suku_cadang->get_result();
        $suku_cadang = $result_suku_cadang->fetch_assoc();

        if (!$suku_cadang) {
            $_SESSION['message'] = "Suku cadang tidak ditemukan.";
            header('Location: data_penjualan.php');
            exit();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "ID suku cadang tidak ditemukan.";
    header('Location: data_penjualan.php');
    exit();
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Penjualan - <?= htmlspecialchars($suku_cadang['nama_suku_cadang']); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="data_penjualan.php">Data Penjualan</a></li>
                        <li class="breadcrumb-item active">Detail Penjualan</li>
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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Penjualan</h3>
                            <a href="#" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#tambahPenjualanModal">Tambah Penjualan</a>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="" class="form-inline mb-3" >
                                <input type="hidden" name="id_suku_cadang" value="<?= $id_suku_cadang; ?>">
                                <div class="form-group mr-2">
                                    <label for="tanggal_awal" class="mr-2">Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="<?= isset($_GET['tanggal_awal']) ? htmlspecialchars($_GET['tanggal_awal']) : ''; ?>">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="tanggal_akhir" class="mr-2">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="<?= isset($_GET['tanggal_akhir']) ? htmlspecialchars($_GET['tanggal_akhir']) : ''; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="?id_suku_cadang=<?= $id_suku_cadang; ?>" class="btn btn-secondary mr-2">Reset Filter</a>
                                <button type="submit" formaction="export_pdf_penjualan.php" class="btn btn-danger">Export PDF</button>
                            </form>


                            <!-- Tabel Data Penjualan -->
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Penjualan</th>
                                        <th>Tanggal Penjualan</th>
                                        <th>Jumlah Penjualan</th>
                                        <th>Total Biaya</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Ambil parameter tanggal jika ada
                                    $tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : null;
                                    $tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : null;

                                    // Query untuk mengambil data penjualan
                                    $query = "
                                        SELECT p.id_penjualan, p.tanggal_penjualan, p.jumlah_penjualan, p.total_biaya
                                        FROM penjualan p
                                        WHERE p.id_suku_cadang = ?
                                    ";
                                    $params = [$id_suku_cadang];

                                    // Tambahkan filter tanggal jika parameter tanggal diisi
                                    if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                                        $query .= " AND p.tanggal_penjualan BETWEEN ? AND ?";
                                        $params[] = $tanggal_awal;
                                        $params[] = $tanggal_akhir;
                                    }
                                    $stmt_penjualan = $koneksi->prepare($query);
                                    $stmt_penjualan->bind_param(str_repeat("s", count($params)), ...$params);
                                    $stmt_penjualan->execute();
                                    $result_penjualan = $stmt_penjualan->get_result();

                                    // Tampilkan data
                                    if ($result_penjualan->num_rows > 0) {
                                        $no = 1;
                                        while ($penjualan = $result_penjualan->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($penjualan['id_penjualan']); ?></td>
                                                <td><?= htmlspecialchars($penjualan['tanggal_penjualan']); ?></td>
                                                <td><?= htmlspecialchars($penjualan['jumlah_penjualan']); ?></td>
                                                <td>Rp <?= number_format($penjualan['total_biaya'], 2, ',', '.'); ?></td>
                                                <td>
                                                    <a href="edit_penjualan.php?id_penjualan=<?= htmlspecialchars($penjualan['id_penjualan']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="proses_penjualan/p_delete_penjualan.php?id_penjualan=<?= htmlspecialchars($penjualan['id_penjualan']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data penjualan untuk suku cadang ini.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Penjualan</th>
                                        <th>Tanggal Penjualan</th>
                                        <th>Jumlah Penjualan</th>
                                        <th>Total Biaya</th>
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

<!-- Modal Tambah Penjualan -->
<!-- Modal Tambah Penjualan -->
<div class="modal fade" id="tambahPenjualanModal" tabindex="-1" role="dialog" aria-labelledby="tambahPenjualanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPenjualanModalLabel">Tambah Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="proses_penjualan/p_add_penjualan.php" method="POST">
                    <input type="hidden" name="id_suku_cadang" value="<?= $id_suku_cadang; ?>">
                    <div class="form-group">
                        <label for="tanggal_penjualan">Tanggal Penjualan</label>
                        <input type="date" name="tanggal_penjualan" id="tanggal_penjualan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_penjualan">Nama Suku Cadang</label>
                        <input type="text" class="form-control" required readonly value="<?= htmlspecialchars($suku_cadang['nama_suku_cadang']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_penjualan">Stok</label>
                        <input type="text" class="form-control" required readonly value="<?= htmlspecialchars($suku_cadang['stok']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="jumlah_penjualan">Jumlah Penjualan</label>
                        <input type="number" name="jumlah_penjualan" id="jumlah_penjualan" class="form-control" required min="1" max="<?= $suku_cadang['stok']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="total_biaya">Total Biaya</label>
                        <input type="number" name="total_biaya" id="total_biaya" class="form-control" required readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>

                <script>
                    // Update total biaya ketika jumlah penjualan diubah
                    document.getElementById("jumlah_penjualan").addEventListener("input", function() {
                        var jumlah = this.value;
                        var harga_per_unit = <?= $suku_cadang['harga_per_unit']; ?>;
                        var total_biaya = jumlah * harga_per_unit;
                        document.getElementById("total_biaya").value = total_biaya.toFixed(2);
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
?>