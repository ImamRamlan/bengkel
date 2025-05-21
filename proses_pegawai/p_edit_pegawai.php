<?php
session_start();
require_once '../../koneksi.php';

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Memeriksa apakah id_pegawai ada di URL
if (!isset($_GET['id_pegawai'])) {
    $_SESSION['error'] = "ID Pegawai tidak valid.";
    header('Location: ../data_pegawai.php');
    exit();
}

$id_pegawai = $_GET['id_pegawai'];

// Mengambil data pegawai berdasarkan id_pegawai
try {
    $stmt = $koneksi->prepare("SELECT username, kata_sandi, nama_lengkap, role FROM pegawai WHERE id_pegawai = ?");
    $stmt->bind_param("i", $id_pegawai);
    $stmt->execute();
    $result = $stmt->get_result();

    // Memastikan pegawai ditemukan
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Pegawai tidak ditemukan.";
        header('Location: ../data_pegawai.php');
        exit();
    }

    $data_pegawai = $result->fetch_assoc();
} catch (Exception $e) {
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    header('Location: ../data_pegawai.php');
    exit();
}

// Memeriksa apakah data telah dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $username = $_POST['username'];
    $kata_sandi = $_POST['kata_sandi'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $role = $_POST['role'];

    // Memeriksa apakah username telah diubah
    $old_username = $data_pegawai['username'];

    try {
        // Memeriksa apakah username sudah ada
        $stmt = $koneksi->prepare("SELECT id_pegawai FROM pegawai WHERE username = ? AND id_pegawai != ?");
        $stmt->bind_param("si", $username, $id_pegawai);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error'] = "Username sudah digunakan oleh pegawai lain.";
            header('Location: ../edit_pegawai.php?id_pegawai=' . $id_pegawai);
            exit();
        }

        // Update data pegawai (username, nama_lengkap, dan role)
        $stmt = $koneksi->prepare("UPDATE pegawai SET username = ?, nama_lengkap = ?, role = ? WHERE id_pegawai = ?");
        $stmt->bind_param("sssi", $username, $nama_lengkap, $role, $id_pegawai);
        $stmt->execute();

        // Jika kata_sandi baru diberikan, maka update kata_sandi
        if (!empty($kata_sandi)) {
            $update_password_stmt = $koneksi->prepare("UPDATE pegawai SET kata_sandi = ? WHERE id_pegawai = ?");
            $update_password_stmt->bind_param("si", $kata_sandi, $id_pegawai);
            $update_password_stmt->execute();
            $update_password_stmt->close();
        }

        // Jika username, role, atau kata_sandi diubah, logout secara otomatis
        if ($username != $old_username || $role != $data_pegawai['role']) {
            session_destroy(); // Mengeluarkan pengguna dari sesi
            session_start(); // Memulai sesi baru
            $_SESSION['message'] = "Username, role, atau kata sandi telah diubah. Silakan login kembali.";
            header('Location: ../login.php'); // Mengarahkan ke halaman login
            exit();
        }

        $_SESSION['message'] = "Pegawai berhasil diperbarui.";
        header('Location: ../data_pegawai.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header('Location: ../edit_pegawai.php?id_pegawai=' . $id_pegawai);
        exit();
    }
}
?>
