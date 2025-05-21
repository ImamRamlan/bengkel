<?php
session_start();
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id_suku_cadang = $_POST['id_suku_cadang'];
    $nama_suku_cadang = $_POST['nama_suku_cadang'];
    $stok = $_POST['stok'];
    $harga_per_unit = $_POST['harga_per_unit'];
    $gambar_lama = $_POST['gambar_lama'];

    // Proses upload gambar jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // Gambar baru
        $gambar = $_FILES['gambar'];
        $gambar_name = time() . '-' . basename($gambar['name']);
        $gambar_tmp = $gambar['tmp_name'];
        $gambar_dir = '../uploads/' . $gambar_name;

        // Validasi file gambar
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $gambar_ext = pathinfo($gambar_name, PATHINFO_EXTENSION);

        if (!in_array($gambar_ext, $valid_extensions)) {
            $_SESSION['error'] = "Format gambar tidak valid! Harus JPG, JPEG, PNG, atau GIF.";
            header("Location: ../edit_suku_cadang.php?id_suku_cadang=" . $id_suku_cadang);
            exit();
        }

        // Cek apakah file berhasil dipindahkan
        if (move_uploaded_file($gambar_tmp, $gambar_dir)) {
            // Hapus gambar lama jika ada
            if ($gambar_lama) {
                unlink('../uploads/' . $gambar_lama);
            }
        } else {
            $_SESSION['error'] = "Gagal meng-upload gambar!";
            header("Location: ../edit_suku_cadang.php?id_suku_cadang=" . $id_suku_cadang);
            exit();
        }
    } else {
        $gambar_name = $gambar_lama;  // Tetap menggunakan gambar lama jika tidak ada gambar baru
    }

    // Update data ke database
    try {
        $stmt = $koneksi->prepare("UPDATE suku_cadang SET nama_suku_cadang = ?, stok = ?, harga_per_unit = ?, gambar = ? WHERE id_suku_cadang = ?");
        $stmt->bind_param("sidsi", $nama_suku_cadang, $stok, $harga_per_unit, $gambar_name, $id_suku_cadang);
        $stmt->execute();

        $_SESSION['message'] = "Data suku cadang berhasil diperbarui!";
        header("Location: ../data_suku_cadang.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: ../edit_suku_cadang.php?id_suku_cadang=" . $id_suku_cadang);
        exit();
    }
} else {
    // Jika bukan POST request
    $_SESSION['error'] = "Request tidak valid!";
    header("Location: ../data_suku_cadang.php");
    exit();
}
?>
