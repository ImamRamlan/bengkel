<?php
session_start();
require_once '../koneksi.php';

// Cek apakah data yang dibutuhkan ada
if (isset($_POST['jumlah_penjualan'], $_POST['total_biaya'], $_POST['id_penjualan'], $_POST['id_suku_cadang'], $_POST['tanggal_penjualan'])) {
    $id_penjualan = $_POST['id_penjualan'];
    $jumlah_penjualan = $_POST['jumlah_penjualan'];
    $total_biaya = $_POST['total_biaya'];
    $id_suku_cadang = $_POST['id_suku_cadang']; // ID suku cadang
    $tanggal_penjualan = $_POST['tanggal_penjualan']; // Tanggal penjualan

    // Mengambil data pembelian terkait dengan penjualan
    $stmt_pembelian = $koneksi->prepare("SELECT id_suku_cadang FROM penjualan WHERE id_penjualan = ?");
    $stmt_pembelian->bind_param("i", $id_penjualan);
    $stmt_pembelian->execute();
    $result_pembelian = $stmt_pembelian->get_result();
    $pembelian = $result_pembelian->fetch_assoc();

    if (!$pembelian) {
        $_SESSION['error'] = "Penjualan terkait tidak ditemukan.";
        header('Location: ../data_penjualan.php');
        exit();
    }

    // Mengambil stok suku cadang berdasarkan id_suku_cadang
    $stmt_suku_cadang = $koneksi->prepare("SELECT stok FROM suku_cadang WHERE id_suku_cadang = ?");
    $stmt_suku_cadang->bind_param("i", $id_suku_cadang);
    $stmt_suku_cadang->execute();
    $result_suku_cadang = $stmt_suku_cadang->get_result();
    $suku_cadang = $result_suku_cadang->fetch_assoc();

    if (!$suku_cadang) {
        $_SESSION['error'] = "Suku cadang tidak ditemukan.";
        header('Location: ../data_penjualan.php');
        exit();
    }

    // Validasi: Pastikan jumlah penjualan tidak lebih besar dari stok yang tersedia
    if ($jumlah_penjualan > $suku_cadang['stok']) {
        $_SESSION['error'] = "Jumlah penjualan melebihi stok yang tersedia.";
        header('Location: ../edit_penjualan.php?id_penjualan=' . $id_penjualan);
        exit();
    }

    // Mengurangi stok suku cadang berdasarkan jumlah penjualan
    $stok_baru = $suku_cadang['stok'] - $jumlah_penjualan;

    // Update stok di tabel suku_cadang
    $stmt_update_stok = $koneksi->prepare("UPDATE suku_cadang SET stok = ? WHERE id_suku_cadang = ?");
    $stmt_update_stok->bind_param("ii", $stok_baru, $id_suku_cadang);
    $stmt_update_stok->execute();

    // Update data penjualan
    $stmt_update = $koneksi->prepare("UPDATE penjualan SET jumlah_penjualan = ?, total_biaya = ?, tanggal_penjualan = ? WHERE id_penjualan = ?");
    $stmt_update->bind_param("dssi", $jumlah_penjualan, $total_biaya, $tanggal_penjualan, $id_penjualan);    

    if ($stmt_update->execute()) {
        $_SESSION['message'] = "Penjualan dan stok berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data penjualan.";
    }

    // Redirect kembali ke halaman detail_penjualan
    header('Location: ../detail_penjualan.php?id_suku_cadang=' . $id_suku_cadang);
    exit();
} else {
    $_SESSION['error'] = "Data tidak lengkap.";
    header('Location: ../data_penjualan.php');
    exit();
}
