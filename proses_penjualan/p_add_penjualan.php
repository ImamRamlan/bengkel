<?php
session_start();
require_once '../koneksi.php';

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Pastikan data yang diterima ada
if (isset($_POST['id_suku_cadang'], $_POST['tanggal_penjualan'], $_POST['jumlah_penjualan'], $_POST['total_biaya'])) {
    $id_suku_cadang = $_POST['id_suku_cadang'];
    $tanggal_penjualan = $_POST['tanggal_penjualan'];
    $jumlah_penjualan = $_POST['jumlah_penjualan'];
    $total_biaya = $_POST['total_biaya'];

    // Mulai transaksi untuk menjaga integritas data
    $koneksi->begin_transaction();

    try {
        // 1. Ambil data stok dari suku cadang
        $stmt_stok = $koneksi->prepare("SELECT stok FROM suku_cadang WHERE id_suku_cadang = ?");
        $stmt_stok->bind_param("i", $id_suku_cadang);
        $stmt_stok->execute();
        $result_stok = $stmt_stok->get_result();
        $suku_cadang = $result_stok->fetch_assoc();

        if ($suku_cadang) {
            $stok_saat_ini = $suku_cadang['stok'];

            // 2. Periksa apakah stok cukup untuk jumlah penjualan
            if ($stok_saat_ini == 0) {
                // Jika stok kosong, beri pesan bahwa stok kosong
                throw new Exception("Stok kosong, silahkan melakukan pembelian terlebih dahulu.");
            } elseif ($stok_saat_ini >= $jumlah_penjualan) {
                // 3. Kurangi stok
                $stok_terbaru = $stok_saat_ini - $jumlah_penjualan;

                // Update stok suku cadang
                $stmt_update_stok = $koneksi->prepare("UPDATE suku_cadang SET stok = ? WHERE id_suku_cadang = ?");
                $stmt_update_stok->bind_param("ii", $stok_terbaru, $id_suku_cadang);
                $stmt_update_stok->execute();

                // 4. Simpan data penjualan
                $stmt_tambah_penjualan = $koneksi->prepare("INSERT INTO penjualan (id_suku_cadang, tanggal_penjualan, jumlah_penjualan, total_biaya) VALUES (?, ?, ?, ?)");
                $stmt_tambah_penjualan->bind_param("isid", $id_suku_cadang, $tanggal_penjualan, $jumlah_penjualan, $total_biaya);
                $stmt_tambah_penjualan->execute();

                // Commit transaksi
                $koneksi->commit();

                $_SESSION['message'] = "Penjualan berhasil ditambahkan.";
                header('Location: ../detail_penjualan.php?id_suku_cadang=' . $id_suku_cadang);
                exit();
            } else {
                throw new Exception("Stok tidak cukup untuk melakukan penjualan.");
            }
        } else {
            throw new Exception("Suku cadang tidak ditemukan.");
        }
    } catch (Exception $e) {
        // Jika terjadi error, rollback transaksi
        $koneksi->rollback();
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header('Location: ../detail_penjualan.php?id_suku_cadang=' . $id_suku_cadang);
        exit();
    }
} else {
    $_SESSION['error'] = "Data tidak lengkap.";
    header('Location: ../data_penjualan.php');
    exit();
}
?>
