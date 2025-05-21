<?php
session_start();
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_suku_cadang = $_POST['nama_suku_cadang'];
    $stok = $_POST['stok'];
    $harga_per_unit = $_POST['harga_per_unit'];

    // Proses upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // Gambar
        $gambar = $_FILES['gambar'];
        $gambar_name = time() . '-' . basename($gambar['name']);
        $gambar_tmp = $gambar['tmp_name'];
        $gambar_dir = '../uploads/' . $gambar_name;

        // Validasi file gambar
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $gambar_ext = pathinfo($gambar_name, PATHINFO_EXTENSION);

        if (!in_array($gambar_ext, $valid_extensions)) {
            $_SESSION['error'] = "Format gambar tidak valid! Harus JPG, JPEG, PNG, atau GIF.";
            header("Location: ../tambah_suku_cadang.php");
            exit();
        }

        // Cek apakah file berhasil dipindahkan
        if (move_uploaded_file($gambar_tmp, $gambar_dir)) {
            // Jika gambar berhasil di-upload, simpan data ke database
            try {
                $stmt = $koneksi->prepare("INSERT INTO suku_cadang (nama_suku_cadang, stok, harga_per_unit, gambar) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sids", $nama_suku_cadang, $stok, $harga_per_unit, $gambar_name);
                $stmt->execute();

                $_SESSION['message'] = "Suku cadang berhasil ditambahkan!";
                header("Location: ../data_suku_cadang.php");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
                header("Location: ../tambah_suku_cadang.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Gagal meng-upload gambar!";
            header("Location: ../tambah_suku_cadang.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Gambar tidak ditemukan atau ada kesalahan saat meng-upload!";
        header("Location: ../tambah_suku_cadang.php");
        exit();
    }
} else {
    // Jika bukan POST request
    $_SESSION['error'] = "Request tidak valid!";
    header("Location: ../tambah_suku_cadang.php");
    exit();
}
?>
