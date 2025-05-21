<?php
require_once '../koneksi.php';  // Ganti dengan path yang sesuai untuk file koneksi Anda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_suku_cadang = $_POST['id_suku_cadang'];
    $id_pegawai = $_POST['id_pegawai'];
    $id_pemasok = $_POST['id_pemasok'];
    $tanggal_pembelian = $_POST['tanggal_pembelian'];
    $jumlah = $_POST['jumlah'];
    $total_biaya = $_POST['total_biaya'];

    // Ambil data stok dari tabel suku_cadang
    $stmt_suku_cadang = $koneksi->prepare("SELECT stok FROM suku_cadang WHERE id_suku_cadang = ?");
    $stmt_suku_cadang->bind_param("i", $id_suku_cadang);
    $stmt_suku_cadang->execute();
    $result_suku_cadang = $stmt_suku_cadang->get_result();

    if ($result_suku_cadang->num_rows > 0) {
        $suku_cadang = $result_suku_cadang->fetch_assoc();
        $stok = $suku_cadang['stok'];

        // Mulai transaksi
        $koneksi->begin_transaction();
        try {
            // Insert data pembelian ke tabel pembelian
            $stmt = $koneksi->prepare("INSERT INTO pembelian (id_pegawai, id_pemasok, id_suku_cadang, tanggal_pembelian, jumlah, total_biaya) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisid", $id_pegawai, $id_pemasok, $id_suku_cadang, $tanggal_pembelian, $jumlah, $total_biaya);
            $stmt->execute();

            // Update stok suku cadang (stok bertambah)
            $new_stok = $stok + $jumlah;
            $stmt_update_stok = $koneksi->prepare("UPDATE suku_cadang SET stok = ? WHERE id_suku_cadang = ?");
            $stmt_update_stok->bind_param("ii", $new_stok, $id_suku_cadang);
            $stmt_update_stok->execute();

            // Commit transaksi
            $koneksi->commit();

            $_SESSION['message'] = "Pembelian berhasil ditambahkan, stok suku cadang telah diperbarui!";
            header('Location: ../detail_pembelian.php?id_suku_cadang=' . $id_suku_cadang);
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $koneksi->rollback();
            $_SESSION['message'] = "Terjadi kesalahan: " . $e->getMessage();
            header('Location: ../detail_pembelian.php?id_suku_cadang=' . $id_suku_cadang);
            exit();
        }
    } else {
        $_SESSION['message'] = "Suku cadang tidak ditemukan.";
        header('Location: ../detail_pembelian.php?id_suku_cadang=' . $id_suku_cadang);
        exit();
    }
}
?>
