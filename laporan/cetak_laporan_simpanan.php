<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../fpdf/fpdf.php';

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// ========================= HEADER =========================
$pdf->Image(__DIR__ . '/../img/logo-kiri.jpg', 15, 8, 25);   // Logo kiri
$pdf->Image(__DIR__ . '/../img/logo-kanan.jpg', 170, 8, 25); // Logo kanan


$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,7,'KOPERASI SIMPAN PINJAM "OPEN SOURCE"',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Jl. Contoh Alamat No.123, Kota - Provinsi',0,1,'C');
$pdf->Cell(0,6,'simpang pemda, ',0,1,'C');
$pdf->Ln(3);
$pdf->Line(10, 35, 200, 35);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'LAPORAN SIMPANAN', 0, 1, 'C');
$pdf->SetFont('Arial', '', 11);

// Periode laporan (ambil bulan ini, misalnya)
$periodeAwal = '13 ' . date('F Y');
$periodeAkhir = date('t F Y');
$pdf->Cell(0, 6, "Periode: $periodeAwal - $periodeAkhir", 0, 1, 'C');
$pdf->Ln(5);

// ========================= HEADER TABEL =========================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, 'Tanggal', 1, 0, 'C');
$pdf->Cell(40, 10, 'Nasabah', 1, 0, 'C');
$pdf->Cell(40, 10, 'Kode Transaksi', 1, 0, 'C');
$pdf->Cell(40, 10, 'Transaksi', 1, 0, 'C');
$pdf->Cell(40, 10, 'Jenis Simpanan', 1, 1, 'C');

// ========================= ISI DATA =========================
$pdf->SetFont('Arial', '', 10);
$sql = "SELECT s.kode_transaksi, s.tanggal, a.nama AS nasabah, 
               s.jenis, s.jumlah
        FROM simpanan s
        JOIN anggota a ON s.anggota_id = a.id
        ORDER BY s.tanggal ASC";
$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 8, date('d M Y', strtotime($row['tanggal'])), 1, 0, 'C');
    $pdf->Cell(40, 8, $row['nasabah'], 1, 0, 'L');
    $pdf->Cell(40, 8, $row['kode_transaksi'], 1, 0, 'C');
    $pdf->Cell(40, 8, 'Rp ' . number_format($row['jumlah'], 0, ',', '.'), 1, 0, 'R');
    $pdf->Cell(40, 8, ucfirst($row['jenis']), 1, 1, 'C');
}
// === Tanda Tangan ===
$pdf->Ln(15);
$pdf->SetFont('Arial','',10);
$pdf->Cell(120);
$pdf->Cell(70,5,'Kota, '.date('d-m-Y'),0,1,'C');
$pdf->Cell(120);
$pdf->Cell(70,5,'Kepala Koperasi',0,1,'C');
$pdf->Ln(20);
$pdf->Cell(120);
$pdf->SetFont('Arial','U',10);
$pdf->Cell(70,5,'(___________________)',0,1,'C');

$pdf->Output('I','laporan_pinjaman.pdf');
?>

// Output
$pdf->Output('I', 'laporan_simpanan.pdf');
?>
