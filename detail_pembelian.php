<?php
ob_start();
session_start();
require_once 'koneksi.php';
$title = "Detail Pembelian | Bengkel Rinus";

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
            header('Location: data_pembelian.php');
            exit();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "ID suku cadang tidak ditemukan.";
    header('Location: data_pembelian.php');
    exit();
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Pembelian - <?= htmlspecialchars($suku_cadang['nama_suku_cadang']); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="data_pembelian.php">Data Pembelian</a></li>
                        <li class="breadcrumb-item active">Detail Pembelian</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Pesan Sukses atau Error -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Pesan</h5>
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Pembelian</h3>
                            <a href="#" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#tambahPembelianModal">Tambah Pembelian</a>
                        </div>
                        <div class="card-body">
                            <!-- Form Filter Tanggal -->
                            <form method="GET" action="" class="form-inline mb-3">
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
                                <!-- Tombol Export PDF -->
                                <a href="export_pdf_pembelian.php?id_suku_cadang=<?= $id_suku_cadang; ?>&tanggal_awal=<?= htmlspecialchars($_GET['tanggal_awal'] ?? ''); ?>&tanggal_akhir=<?= htmlspecialchars($_GET['tanggal_akhir'] ?? ''); ?>" class="btn btn-success">Export PDF</a>
                            </form>


                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Pembelian</th>
                                        <th>Pegawai</th>
                                        <th>Pemasok</th>
                                        <th>Tanggal Pembelian</th>
                                        <th>Jumlah</th>
                                        <th>Total Biaya</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Menyiapkan kondisi untuk filter tanggal
                                    $tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
                                    $tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

                                    // Menyiapkan query pembelian dengan filter tanggal jika ada
                                    $query = "
                                        SELECT p.id_pembelian, e.nama_lengkap AS pegawai_name, ps.nama_pemasok, 
                                               p.tanggal_pembelian, p.jumlah, p.total_biaya
                                        FROM pembelian p
                                        LEFT JOIN pegawai e ON p.id_pegawai = e.id_pegawai
                                        LEFT JOIN pemasok ps ON p.id_pemasok = ps.id_pemasok
                                        WHERE p.id_suku_cadang = ?
                                    ";

                                    if ($tanggal_awal && $tanggal_akhir) {
                                        $query .= " AND p.tanggal_pembelian BETWEEN ? AND ?";
                                    } elseif ($tanggal_awal) {
                                        $query .= " AND p.tanggal_pembelian >= ?";
                                    } elseif ($tanggal_akhir) {
                                        $query .= " AND p.tanggal_pembelian <= ?";
                                    }

                                    // Menyiapkan statement pembelian
                                    $stmt_pembelian = $koneksi->prepare($query);

                                    // Mengikat parameter
                                    if ($tanggal_awal && $tanggal_akhir) {
                                        $stmt_pembelian->bind_param("iss", $id_suku_cadang, $tanggal_awal, $tanggal_akhir);
                                    } elseif ($tanggal_awal) {
                                        $stmt_pembelian->bind_param("is", $id_suku_cadang, $tanggal_awal);
                                    } elseif ($tanggal_akhir) {
                                        $stmt_pembelian->bind_param("is", $id_suku_cadang, $tanggal_akhir);
                                    } else {
                                        $stmt_pembelian->bind_param("i", $id_suku_cadang);
                                    }

                                    $stmt_pembelian->execute();
                                    $result_pembelian = $stmt_pembelian->get_result();

                                    if ($result_pembelian->num_rows > 0) {
                                        $no = 1;
                                        while ($pembelian = $result_pembelian->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($pembelian['id_pembelian']); ?></td>
                                                <td><?= htmlspecialchars($pembelian['pegawai_name']); ?></td>
                                                <td><?= htmlspecialchars($pembelian['nama_pemasok']); ?></td>
                                                <td><?= htmlspecialchars($pembelian['tanggal_pembelian']); ?></td>
                                                <td><?= htmlspecialchars($pembelian['jumlah']); ?></td>
                                                <td>Rp <?= number_format($pembelian['total_biaya'], 2, ',', '.'); ?></td>
                                                <td>
                                                    <a href="edit_pembelian.php?id_pembelian=<?= htmlspecialchars($pembelian['id_pembelian']); ?>"
                                                        class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="proses_pembelian/p_delete_pembelian.php?id_pembelian=<?= htmlspecialchars($pembelian['id_pembelian']); ?>"
                                                        class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>Tidak ada data pembelian</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal Tambah Pembelian -->
<div class="modal fade" id="tambahPembelianModal" tabindex="-1" role="dialog" aria-labelledby="tambahPembelianModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPembelianModalLabel">Tambah Pembelian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form untuk menambah pembelian -->
                <form action="proses_pembelian/p_add_pembelian.php" method="POST">
                    <input type="hidden" name="id_suku_cadang" value="<?= $id_suku_cadang; ?>">
                    <div class="form-group">
                        <label for="id_pegawai">Pegawai</label>
                        <input type="text" name="id_pegawai" id="id_pegawai" class="form-control" value="<?= $_SESSION['user_id']; ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="id_pemasok">Pemasok</label>
                        <select name="id_pemasok" id="id_pemasok" class="form-control" required>
                            <?php
                            // Mengambil data pemasok untuk pilihan
                            $stmt_pemasok = $koneksi->prepare("SELECT id_pemasok, nama_pemasok FROM pemasok");
                            $stmt_pemasok->execute();
                            $result_pemasok = $stmt_pemasok->get_result();
                            while ($pemasok = $result_pemasok->fetch_assoc()) {
                                echo "<option value='" . $pemasok['id_pemasok'] . "'>" . htmlspecialchars($pemasok['nama_pemasok']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pembelian">Tanggal Pembelian</label>
                        <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok Tersedia</label>
                        <input type="text" id="stok" class="form-control" value="<?= $suku_cadang['stok']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="harga_per_unit">Harga Per Unit</label>
                        <input type="text" id="harga_per_unit" class="form-control" value="Rp <?= number_format($suku_cadang['harga_per_unit'], 2, ',', '.'); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah Pembelian</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="total_biaya">Total Biaya</label>
                        <input type="number" name="total_biaya" id="total_biaya" class="form-control" required readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>

                <script>
                    // Update total biaya ketika jumlah pembelian diubah
                    document.getElementById("jumlah").addEventListener("input", function() {
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
<?php require_once 'include/footer.php'; ?>