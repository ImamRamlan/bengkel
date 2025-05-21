<?php
session_start();
require_once '../../koneksi.php';

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $role = 'Pegawai'; // Atur role secara default

    // Cek apakah username sudah ada di database
    $stmt = $koneksi->prepare("SELECT * FROM pegawai WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika username sudah ada, kirim pesan error
        $_SESSION['error'] = "Username sudah digunakan. Silakan pilih username lain.";
        header("Location: ../tambah_pegawai.php");
        exit();
    } else {
        // Jika username belum ada, lanjutkan untuk menyimpan data
        $stmt = $koneksi->prepare("INSERT INTO pegawai (username, kata_sandi, nama_lengkap, role, session_token) VALUES (?, ?, ?, ?, ?)");
        $session_token = bin2hex(random_bytes(32)); // Generate session token
        $stmt->bind_param("sssss", $username, $password, $nama_lengkap, $role, $session_token);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Data pegawai berhasil ditambahkan.";
            header("Location: ../data_pegawai.php");
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat menambahkan data pegawai.";
            header("Location: ../tambah_pegawai.php");
        }
    }
    $stmt->close();
    $koneksi->close();
} else {
    header("Location: ../tambah_pegawai.php");
}
?>
