<?php
session_start();
require_once '../koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Pastikan ID pemasok diterima melalui parameter URL
if (isset($_GET['id_pemasok'])) {
    $id_pemasok = $_GET['id_pemasok'];

    try {
        // Siapkan pernyataan SQL untuk menghapus data pemasok
        $stmt = $koneksi->prepare("DELETE FROM pemasok WHERE id_pemasok = ?");
        $stmt->bind_param("i", $id_pemasok);

        // Eksekusi pernyataan SQL
        if ($stmt->execute()) {
            $_SESSION['message'] = "Data pemasok berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus data pemasok.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    // Arahkan kembali ke halaman data_pemasok.php
    header("Location: ../data_pemasok.php");
    exit();
} else {
    $_SESSION['error'] = "ID pemasok tidak ditemukan.";
    header("Location: ../data_pemasok.php");
    exit();
}
?>
