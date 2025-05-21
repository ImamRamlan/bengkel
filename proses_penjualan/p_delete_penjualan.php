<?php
session_start();
require_once '../koneksi.php';

// Cek apakah ID penjualan tersedia
if (isset($_GET['id_penjualan'])) {
    $id_penjualan = $_GET['id_penjualan'];

    // Ambil data penjualan untuk mendapatkan id_suku_cadang dan jumlah_penjualan
    $stmt_penjualan = $koneksi->prepare("SELECT id_suku_cadang, jumlah_penjualan FROM penjualan WHERE id_penjualan = ?");
    $stmt_penjualan->bind_param("i", $id_penjualan);
    $stmt_penjualan->execute();
    $result_penjualan = $stmt_penjualan->get_result();
    $penjualan = $result_penjualan->fetch_assoc();

    if ($penjualan) {
        $id_suku_cadang = $penjualan['id_suku_cadang'];
        $jumlah_penjualan = $penjualan['jumlah_penjualan'];

        // Update stok suku cadang dengan menambahkan kembali jumlah penjualan
        $stmt_update_stok = $koneksi->prepare("UPDATE suku_cadang SET stok = stok + ? WHERE id_suku_cadang = ?");
        $stmt_update_stok->bind_param("ii", $jumlah_penjualan, $id_suku_cadang);
        $stmt_update_stok->execute();

        // Hapus data penjualan
        $stmt_delete = $koneksi->prepare("DELETE FROM penjualan WHERE id_penjualan = ?");
        $stmt_delete->bind_param("i", $id_penjualan);
        if ($stmt_delete->execute()) {
            $_SESSION['message'] = "Data penjualan berhasil dihapus dan stok diperbarui.";
        } else {
            $_SESSION['error'] = "Gagal menghapus data penjualan.";
        }
    } else {
        $_SESSION['error'] = "Data penjualan tidak ditemukan.";
    }

    // Redirect kembali ke halaman detail penjualan berdasarkan id_suku_cadang
    header('Location: ../detail_penjualan.php?id_suku_cadang=' . $id_suku_cadang);
    exit();
} else {
    $_SESSION['error'] = "ID Penjualan tidak ditemukan.";
    header('Location: ../detail_penjualan.php?id_suku_cadang=' . $id_suku_cadang);
    exit();
}
