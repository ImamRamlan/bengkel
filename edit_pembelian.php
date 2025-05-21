<?php
ob_start();
session_start();
require_once 'koneksi.php';
$title = "Edit Pembelian | Bengkel Rinus";

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

$id_suku_cadang = isset($_GET['id_suku_cadang']) ? $_GET['id_suku_cadang'] : null;

// Cek apakah id_pembelian ada
if (!isset($_GET['id_pembelian'])) {
    $_SESSION['error'] = "ID pembelian tidak ditemukan.";
    header('Location: data_pembelian.php');
    exit();
}

$id_pembelian = $_GET['id_pembelian'];

// Ambil data pembelian berdasarkan id_pembelian
$stmt = $koneksi->prepare("
    SELECT p.id_pembelian, p.id_pegawai, p.id_pemasok, p.tanggal_pembelian, 
           p.jumlah, p.total_biaya, s.nama_suku_cadang, s.harga_per_unit, 
           s.stok, e.nama_lengkap, s.id_suku_cadang AS id_suku_cadang
    FROM pembelian p
    LEFT JOIN suku_cadang s ON p.id_suku_cadang = s.id_suku_cadang
    LEFT JOIN pegawai e ON p.id_pegawai = e.id_pegawai
    WHERE p.id_pembelian = ?
");

$stmt->bind_param("i", $id_pembelian);
$stmt->execute();
$result = $stmt->get_result();
$pembelian = $result->fetch_assoc();

if (!$pembelian) {
    $_SESSION['error'] = "Data pembelian tidak ditemukan.";
    header('Location: data_pembelian.php');
    exit();
}

require_once 'include/header.php';
require_once 'include/navbar.php';
require_once 'include/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Pembelian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="data_pembelian.php">Data Pembelian</a></li>
                        <li class="breadcrumb-item active">Edit Pembelian</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Pembelian</h3>
                </div>
                <form action="proses_pembelian/p_edit_pembelian.php" method="POST">
                    <div class="card-body">
                        <input type="hidden" name="id_pembelian" value="<?= $pembelian['id_pembelian']; ?>">

                        <div class="form-group">
                            <label for="id_pegawai">Pegawai</label>
                            <input type="hidden" name="id_pegawai" value="<?= htmlspecialchars($pembelian['id_pegawai']); ?>" readonly>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($pembelian['nama_lengkap']); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="id_pemasok">Pemasok</label>
                            <select name="id_pemasok" id="id_pemasok" class="form-control" required>
                                <?php
                                // Ambil data pemasok
                                $stmt_pemasok = $koneksi->prepare("SELECT id_pemasok, nama_pemasok FROM pemasok");
                                $stmt_pemasok->execute();
                                $result_pemasok = $stmt_pemasok->get_result();
                                while ($pemasok = $result_pemasok->fetch_assoc()) {
                                    $selected = ($pembelian['id_pemasok'] == $pemasok['id_pemasok']) ? "selected" : "";
                                    echo "<option value='" . $pemasok['id_pemasok'] . "' $selected>" . htmlspecialchars($pemasok['nama_pemasok']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_pembelian">Tanggal Pembelian</label>
                            <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="form-control" value="<?= htmlspecialchars($pembelian['tanggal_pembelian']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="nama_suku_cadang">Suku Cadang</label>
                            <input type="text" id="nama_suku_cadang" class="form-control" value="<?= htmlspecialchars($pembelian['nama_suku_cadang']); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="harga_per_unit">Harga Per Unit</label>
                            <input type="text" id="harga_per_unit" class="form-control" value="Rp <?= number_format($pembelian['harga_per_unit'], 2, ',', '.'); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Jumlah Pembelian</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" value="<?= $pembelian['jumlah']; ?>" required min="1" max="<?= $pembelian['stok']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="total_biaya">Total Biaya</label>
                            <input type="number" name="total_biaya" id="total_biaya" class="form-control" value="<?= $pembelian['total_biaya']; ?>" readonly>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="detail_pembelian.php?id_suku_cadang=<?= $pembelian['id_suku_cadang']; ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>

                <script>
                    // Menghitung total biaya saat jumlah pembelian diubah
                    document.getElementById('jumlah').addEventListener('input', function() {
                        var jumlah = this.value;
                        var harga_per_unit = <?= $pembelian['harga_per_unit']; ?>;
                        var total_biaya = jumlah * harga_per_unit;
                        document.getElementById('total_biaya').value = total_biaya.toFixed(2);
                    });
                </script>
            </div>
        </div>
    </section>
</div>

<?php
require_once 'include/footer.php';
ob_end_flush();
?>
