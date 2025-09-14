<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
require __DIR__ . '/../fpdf/fpdf.php';

// Ambil data penarikan
$sql = "SELECT p.id, p.kode_tarik, p.tanggal, p.anggota_id, a.nama AS nasabah, p.jumlah, p.keterangan
        FROM penarikan p
        JOIN anggota a ON p.anggota_id = a.id
        ORDER BY p.tanggal ASC";
$result = $mysqli->query($sql);

$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

// ==== Logo & Header ====
if (file_exists(__DIR__ . '/../img/logo-kiri.jpg')) {
    $pdf->Image(__DIR__ . '/../img/logo-kiri.jpg', 10, 6, 25);
}
if (file_exists(__DIR__ . '/../img/logo-kanan.jpg')) {
    $pdf->Image(__DIR__ . '/../img/logo-kanan.jpg', 175, 6, 25);
}

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,7,'KOPERASI SIMPAN PINJAM "OPEN SOURCE"',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Jl. melati raya no. 9 sempakata, medan selayayang',0,1,'C');
$pdf->Cell(0,6,'simpang pemda, ',0,1,'C');
$pdf->Ln(3);
$pdf->Line(10, 35, 200, 35);
$pdf->Ln(5);

// ==== Judul Laporan ====
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,7,'LAPORAN PENARIKAN',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Periode: Semua Data Penarikan',0,1,'C');
$pdf->Ln(5);

// ==== Header Tabel ====
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25,8,'Tanggal',1,0,'C');
$pdf->Cell(40,8,'Nasabah',1,0,'C');
$pdf->Cell(30,8,'Kode Tarik',1,0,'C');
$pdf->Cell(35,8,'Jumlah',1,0,'C');
$pdf->Cell(35,8,'Sisa Saldo',1,0,'C');
$pdf->Cell(30,8,'Keterangan',1,1,'C');

// ==== Data ====
$pdf->SetFont('Arial','',9);
while ($row = $result->fetch_assoc()) {
    // Hitung saldo
    $q_saldo = $mysqli->query("
        SELECT 
            (SELECT IFNULL(SUM(jumlah),0) FROM simpanan WHERE anggota_id={$row['anggota_id']}) -
            (SELECT IFNULL(SUM(jumlah),0) FROM penarikan WHERE anggota_id={$row['anggota_id']}) AS saldo
    ");
    $saldo = $q_saldo->fetch_assoc()['saldo'];

    $pdf->Cell(25,8,date('d-m-Y', strtotime($row['tanggal'])),1,0,'C');
    $pdf->Cell(40,8,$row['nasabah'],1);
    $pdf->Cell(30,8,$row['kode_tarik'],1,0,'C');
    $pdf->Cell(35,8,'Rp '.number_format($row['jumlah'],0,',','.'),1,0,'R');
    $pdf->Cell(35,8,'Rp '.number_format($saldo,0,',','.'),1,0,'R');
    $pdf->Cell(30,8,$row['keterangan'],1,1);
}

// ==== Tanda Tangan ====
$pdf->Ln(15);
$pdf->SetFont('Arial','',10);
$pdf->Cell(130,5,'',0,0);
$pdf->Cell(60,5,'Pringsewu, '.date('d-m-Y'),0,1,'C');
$pdf->Cell(130,5,'',0,0);
$pdf->Cell(60,5,'Kepala Koperasi',0,1,'C');
$pdf->Ln(20);
$pdf->Cell(130,5,'',0,0);
$pdf->Cell(60,5,'______________________',0,1,'C');

// Output PDF
$pdf->Output('I','laporan_penarikan.pdf');
