<?php
session_start();
require_once '../koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Pastikan ID Pemasok dan data formulir telah diterima
if (isset($_POST['id_pemasok']) && isset($_POST['nama_pemasok']) && isset($_POST['perusahaan'])) {
    $id_pemasok = $_POST['id_pemasok'];
    $nama_pemasok = $_POST['nama_pemasok'];
    $alamat = $_POST['alamat'];
    $no_telepon = $_POST['no_telepon'];
    $perusahaan = $_POST['perusahaan'];

    try {
        // Siapkan pernyataan SQL untuk memperbarui data pemasok
        $stmt = $koneksi->prepare("UPDATE pemasok SET nama_pemasok = ?, alamat = ?, no_telepon = ?, perusahaan = ? WHERE id_pemasok = ?");
        $stmt->bind_param("ssssi", $nama_pemasok, $alamat, $no_telepon, $perusahaan, $id_pemasok);

        // Eksekusi pernyataan SQL
        if ($stmt->execute()) {
            $_SESSION['message'] = "Data pemasok berhasil diperbarui.";
            header("Location: ../data_pemasok.php");
            exit();
        } else {
            $_SESSION['error'] = "Gagal memperbarui data pemasok.";
            header("Location: ../edit_pemasok.php?id_pemasok=" . $id_pemasok);
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../edit_pemasok.php?id_pemasok=" . $id_pemasok);
        exit();
    }
} else {
    $_SESSION['error'] = "Data tidak lengkap. Silakan coba lagi.";
    header("Location: ../data_pemasok.php");
    exit();
}
?>
