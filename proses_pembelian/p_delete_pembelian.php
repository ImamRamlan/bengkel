<?php
ob_start();
session_start();
require_once '../koneksi.php';

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Cek apakah id_pembelian ada
if (isset($_GET['id_pembelian'])) {
    $id_pembelian = $_GET['id_pembelian'];

    // Ambil data pembelian sebelum dihapus untuk mendapatkan informasi suku cadang dan jumlah
    $stmt = $koneksi->prepare("
        SELECT id_suku_cadang, jumlah FROM pembelian WHERE id_pembelian = ?
    ");
    $stmt->bind_param("i", $id_pembelian);
    $stmt->execute();
    $result = $stmt->get_result();
    $pembelian = $result->fetch_assoc();

    if ($pembelian) {
        $id_suku_cadang = $pembelian['id_suku_cadang'];
        $jumlah = $pembelian['jumlah'];

        // Hapus data pembelian
        $stmt_delete = $koneksi->prepare("DELETE FROM pembelian WHERE id_pembelian = ?");
        $stmt_delete->bind_param("i", $id_pembelian);

        if ($stmt_delete->execute()) {
            // Mengembalikan stok suku cadang setelah penghapusan
            $stmt_stok = $koneksi->prepare("UPDATE suku_cadang SET stok = stok + ? WHERE id_suku_cadang = ?");
            $stmt_stok->bind_param("ii", $jumlah, $id_suku_cadang);

            if ($stmt_stok->execute()) {
                $_SESSION['message'] = "Pembelian berhasil dihapus dan stok suku cadang telah diperbarui.";
            } else {
                $_SESSION['error'] = "Gagal memperbarui stok suku cadang.";
            }
        } else {
            $_SESSION['error'] = "Gagal menghapus data pembelian.";
        }
    } else {
        $_SESSION['error'] = "Data pembelian tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "ID pembelian tidak ditemukan.";
}

// Arahkan kembali ke halaman detail_pembelian berdasarkan id_suku_cadang
header('Location: ../detail_pembelian.php?id_suku_cadang=' . $id_suku_cadang);
ob_end_flush();
?>
