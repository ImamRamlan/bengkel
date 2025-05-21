<?php
session_start();
include '../koneksi.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload PHPMailer
require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $kata_sandi = $_POST['kata_sandi'];
    $email = $_POST['email'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $role = $_POST['role'];

    // Cek apakah username atau email sudah ada di database
    $checkQuery = "SELECT * FROM pegawai WHERE username = '$username' OR email = '$email'";
    $checkResult = mysqli_query($koneksi, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Jika username atau email sudah ada
        $error = "Username atau email sudah terdaftar. Silakan gunakan yang lain.";
        header("Location: ../registrasi.php?error=" . urlencode($error));
        exit();
    }
    
    try {
        // Generate verification code
        $verification_code = bin2hex(random_bytes(8)); // Menghasilkan 16 karakter

        // Simpan data pengguna ke database
        $query = "INSERT INTO pegawai (username, email, kata_sandi, nama_lengkap, role, verification_code, is_verified) 
                  VALUES ('$username', '$email', '$kata_sandi', '$nama_lengkap', '$role', '$verification_code', 0)";
        
        if (!mysqli_query($koneksi, $query)) {
            throw new Exception("Kesalahan saat menyimpan data ke database: " . mysqli_error($koneksi));
        }

        // Kirim email verifikasi
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'imamkiller77@gmail.com'; // Ganti dengan email Anda
        $mail->Password   = 'kxhgtasckhixnrcf'; // Ganti dengan password email Anda
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('imamkiller77@gmail.com', 'Bengkel Rinus');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Email';
        $mail->Body    = 'Silakan verifikasi akun Anda dengan mengklik tautan berikut: <a href="http://localhost/bengkel/verifikasi.php?code=' . $verification_code . '">Verifikasi Email</a>';

        if (!$mail->send()) {
            throw new Exception("Kesalahan! Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}. Silakan periksa koneksi internet Anda dan coba lagi.");
        }

        // Jika email berhasil dikirim, lakukan commit
        mysqli_commit($koneksi);
        $success_message = "Pendaftaran berhasil! Silakan cek email Anda untuk verifikasi.";
        header("Location: ../registrasi.php?success=" . urlencode($success_message));
        exit();
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        mysqli_rollback($koneksi);
        $error = $e->getMessage();
        header("Location: ../registrasi.php?error=" . urlencode($error));
        exit();
    }
}
?>
