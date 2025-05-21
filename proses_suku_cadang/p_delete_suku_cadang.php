<?php
session_start();
require_once '../koneksi.php';

if (isset($_GET['id_suku_cadang'])) {
    $id_suku_cadang = $_GET['id_suku_cadang'];

    try {
        // Ambil nama gambar dari database
        $stmt = $koneksi->prepare("SELECT gambar FROM suku_cadang WHERE id_suku_cadang = ?");
        $stmt->bind_param("i", $id_suku_cadang);
        $stmt->execute();
        $result = $stmt->get_result();

        // Cek apakah data ditemukan
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $gambar = $data['gambar'];

            // Hapus gambar dari folder uploads jika ada
            if ($gambar) {
                $gambar_path = '../uploads/' . $gambar;
                if (file_exists($gambar_path)) {
                    unlink($gambar_path);
                }
            }

            // Hapus data suku cadang dari database
            $stmt = $koneksi->prepare("DELETE FROM suku_cadang WHERE id_suku_cadang = ?");
            $stmt->bind_param("i", $id_suku_cadang);
            $stmt->execute();

            // Cek apakah berhasil dihapus
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Data suku cadang berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus data suku cadang.";
            }
        } else {
            $_SESSION['error'] = "Data suku cadang tidak ditemukan.";
        }

        // Redirect ke halaman data_suku_cadang.php
        header("Location: ../data_suku_cadang.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: ../data_suku_cadang.php");
        exit();
    }
} else {
    // Jika tidak ada ID yang diberikan
    $_SESSION['error'] = "ID suku cadang tidak valid.";
    header("Location: ../data_suku_cadang.php");
    exit();
}
?>
