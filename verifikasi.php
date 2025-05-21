<?php
session_start();
include 'koneksi.php';

if (isset($_GET['code'])) {
    $verification_code = mysqli_real_escape_string($koneksi, $_GET['code']); // Keamanan: Escape input

    // Cek apakah kode verifikasi ada di database dan belum digunakan
    $query = "SELECT * FROM pegawai WHERE verification_code = '$verification_code' AND is_verified = 0";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        // Jika kode valid, update status is_verified menjadi 1
        $row = mysqli_fetch_assoc($result);
        $update_query = "UPDATE pegawai SET is_verified = 1, verification_code = NULL WHERE id_pegawai = " . (int)$row['id_pegawai']; // Keamanan: Casting ke integer
        
        if (mysqli_query($koneksi, $update_query)) {
            echo "<div style='color: green; font-size: 18px; text-align: center;'>Akun Anda telah diverifikasi!</div>";
        } else {
            echo "<div style='color: red; font-size: 18px; text-align: center;'>Terjadi kesalahan saat memperbarui status verifikasi: " . mysqli_error($koneksi) . "</div>";
        }
    } else {
        echo "<div style='color: red; font-size: 18px; text-align: center;'>Kode verifikasi tidak valid atau sudah digunakan.<br>Kode yang dicari: <strong>" . htmlspecialchars($verification_code) . "</strong></div>";
    }
} else {
    echo "<div style='color: red; font-size: 18px; text-align: center;'>Tidak ada kode verifikasi yang diterima.</div>";
}
?>
