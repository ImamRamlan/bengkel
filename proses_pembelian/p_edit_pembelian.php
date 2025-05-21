<?php
ob_start();
session_start();
require_once '../koneksi.php';

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Cek apakah id_pembelian dan data lainnya ada
if (isset($_POST['id_pembelian'], $_POST['id_pegawai'], $_POST['id_pemasok'], $_POST['tanggal_pembelian'], $_POST['jumlah'], $_POST['total_biaya'])) {
    $id_pembelian = $_POST['id_pembelian'];
    $id_pegawai = $_POST['id_pegawai'];
    $id_pemasok = $_POST['id_pemasok'];
    $tanggal_pembelian = $_POST['tanggal_pembelian'];
    $jumlah = $_POST['jumlah'];
    $total_biaya = $_POST['total_biaya'];

    // Ambil harga per unit dan id_suku_cadang dari pembelian yang akan diubah
    $stmt = $koneksi->prepare("
        SELECT p.id_suku_cadang, s.harga_per_unit, p.jumlah FROM pembelian p
        LEFT JOIN suku_cadang s ON p.id_suku_cadang = s.id_suku_cadang
        WHERE p.id_pembelian = ?
    ");
    $stmt->bind_param("i", $id_pembelian);
    $stmt->execute();
    $result = $stmt->get_result();
    $pembelian = $result->fetch_assoc();

    if ($pembelian) {
        $harga_per_unit = $pembelian['harga_per_unit'];
        $id_suku_cadang = $pembelian['id_suku_cadang'];
        $jumlah_lama = $pembelian['jumlah'];  // Jumlah yang lama sebelum perubahan

        // Hitung total biaya
        $total_biaya = $jumlah * $harga_per_unit;

        // Update data pembelian
        $stmt_update = $koneksi->prepare("
            UPDATE pembelian 
            SET id_pegawai = ?, id_pemasok = ?, tanggal_pembelian = ?, jumlah = ?, total_biaya = ? 
            WHERE id_pembelian = ?
        ");
        $stmt_update->bind_param("iisddi", $id_pegawai, $id_pemasok, $tanggal_pembelian, $jumlah, $total_biaya, $id_pembelian);

        if ($stmt_update->execute()) {
            // Mengurangi stok suku cadang berdasarkan perubahan jumlah
            $jumlah_berubah = $jumlah - $jumlah_lama; // Selisih jumlah pembelian

            if ($jumlah_berubah != 0) {
                $stmt_stok = $koneksi->prepare("UPDATE suku_cadang SET stok = stok - ? WHERE id_suku_cadang = ?");
                $stmt_stok->bind_param("ii", abs($jumlah_berubah), $id_suku_cadang);

                if (!$stmt_stok->execute()) {
                    $_SESSION['error'] = "Gagal mengurangi stok suku cadang.";
                    header('Location: detail_pembelian.php?id_suku_cadang=' . $id_suku_cadang);
                    ob_end_flush();
                    exit();
                }
            }

            $_SESSION['message'] = "Pembelian berhasil diperbarui.";
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat memperbarui pembelian.";
        }
    } else {
        $_SESSION['error'] = "Data pembelian tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "Data tidak lengkap.";
}

// Arahkan kembali ke halaman detail_pembelian berdasarkan id_suku_cadang
if (isset($id_suku_cadang)) {
    header('Location: ../detail_pembelian.php?id_suku_cadang=' . $id_suku_cadang);
} else {
    header('Location: data_pembelian.php');
}

ob_end_flush();
?>
