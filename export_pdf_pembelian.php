<?php
require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';
require_once 'koneksi.php';

// Periksa apakah tanggal awal dan akhir diset
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : null;
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : null;

// Validasi nilai tanggal
$tanggal_awal_display = $tanggal_awal ?? 'Semua';
$tanggal_akhir_display = $tanggal_akhir ?? 'Semua';

// Query data pembelian berdasarkan rentang tanggal
$sql = "SELECT p.id_pembelian, p.tanggal_pembelian, p.jumlah, p.total_biaya, ps.nama_pemasok
        FROM pembelian p
        JOIN pemasok ps ON p.id_pemasok = ps.id_pemasok";

if ($tanggal_awal && $tanggal_akhir) {
    $sql .= " WHERE p.tanggal_pembelian BETWEEN ? AND ?";
}

$stmt = $koneksi->prepare($sql);
if ($tanggal_awal && $tanggal_akhir) {
    $stmt->bind_param("ss", $tanggal_awal, $tanggal_akhir);
}
$stmt->execute();
$result = $stmt->get_result();

// Inisialisasi TCPDF
$pdf = new TCPDF();
$pdf->setPrintHeader(false); // Nonaktifkan header default
$pdf->setPrintFooter(false); // Nonaktifkan footer default
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

// Judul dokumen
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Pembelian Suku Cadang', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Tanggal: $tanggal_awal_display - $tanggal_akhir_display", 0, 1, 'C');

// Spasi setelah judul
$pdf->Ln(10);

// Header tabel
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'ID Pembelian', 1, 0, 'C', 1);
$pdf->Cell(40, 8, 'Tanggal Pembelian', 1, 0, 'C', 1);
$pdf->Cell(50, 8, 'Nama Pemasok', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Jumlah', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Total Biaya', 1, 1, 'C', 1);

// Isi tabel
$pdf->SetFont('helvetica', '', 10);
$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10, 8, $no++, 1, 0, 'C');
    $pdf->Cell(30, 8, $row['id_pembelian'], 1, 0, 'C');
    $pdf->Cell(40, 8, $row['tanggal_pembelian'], 1, 0, 'C');
    $pdf->Cell(50, 8, $row['nama_pemasok'], 1, 0, 'L');
    $pdf->Cell(30, 8, $row['jumlah'], 1, 0, 'C');
    $pdf->Cell(30, 8, 'Rp ' . number_format($row['total_biaya'], 2, ',', '.'), 1, 1, 'R');
}

// Jika tidak ada data
if ($result->num_rows === 0) {
    $pdf->Cell(0, 8, 'Tidak ada data untuk rentang tanggal ini.', 1, 1, 'C');
}

// Output file PDF
$pdf->Output('Laporan_Pembelian.pdf', 'I');
?>
