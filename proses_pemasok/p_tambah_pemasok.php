<?php
session_start();
require_once '../koneksi.php';

// Cek apakah form telah di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_pemasok = $_POST['nama_pemasok'];
    $alamat = $_POST['alamat'];
    $no_telepon = $_POST['no_telepon'];
    $perusahaan = $_POST['perusahaan'];

    try {
        // Query untuk menambahkan data pemasok ke database
        $stmt = $koneksi->prepare("INSERT INTO pemasok (nama_pemasok, alamat, no_telepon, perusahaan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama_pemasok, $alamat, $no_telepon, $perusahaan);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Data pemasok berhasil ditambahkan.";
        } else {
            $_SESSION['error'] = "Gagal menambahkan data pemasok.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    // Redirect kembali ke halaman data pemasok
    header('Location: ../data_pemasok.php');
    exit();
} else {
    // Jika user mencoba mengakses langsung file ini tanpa submit form
    $_SESSION['error'] = "Akses tidak valid.";
    header('Location: ../data_pemasok.php');
    exit();
}
?>
