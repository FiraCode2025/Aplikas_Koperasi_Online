<?php
require_once __DIR__ . '/../fpdf/fpdf.php';
require_once __DIR__ . '/../db/koneksi.php';

$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$logoKiri  = __DIR__ . '/../img/logo-kiri.jpg';
$logoKanan = __DIR__ . '/../img/logo-kanan.jpg';

// Cek dulu biar nggak error
if (file_exists($logoKiri)) {
    $pdf->Image($logoKiri, 10, 6, 25, 25);   // kiri
}
if (file_exists($logoKanan)) {
    $pdf->Image($logoKanan, 175, 6, 25, 25); // kanan
}

// =============== HEADER ===============
$pdf->SetFont('Arial','B',14);
$pdf->Cell(190,7,'KOPERASI MELATI RAYA',0,1,'C');
$pdf->SetFont('Arial','',11);
$pdf->Cell(190,7,'Jl. Contoh Alamat No.123, Kota - Provinsi',0,1,'C');
$pdf->Ln(3);
$pdf->Line(10,35,200,35);
$pdf->Ln(10);

// =============== JUDUL ===============
$pdf->SetFont('Arial','B',13);
$pdf->Cell(190,7,'LAPORAN PENARIKAN',0,1,'C');
$pdf->Ln(7);

// =============== TABEL HEADER ===============
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,7,'No',1,0,'C');
$pdf->Cell(25,7,'Tanggal',1,0,'C');
$pdf->Cell(40,7,'Nasabah',1,0,'C');
$pdf->Cell(25,7,'Kode Tarik',1,0,'C');
$pdf->Cell(30,7,'Jumlah',1,0,'C');
$pdf->Cell(30,7,'Sisa Saldo',1,0,'C');
$pdf->Cell(30,7,'Keterangan',1,1,'C');

// =============== AMBIL DATA ===============
$no=1;
$query = $mysqli->query("
    SELECT p.tanggal, a.nama AS nasabah, p.kode_tarik, p.jumlah, p.sisa_saldo, p.keterangan 
    FROM penarikan p 
    JOIN anggota a ON p.anggota_id=a.id
");
$pdf->SetFont('Arial','',10);
while($row = $query->fetch_assoc()){
    $pdf->Cell(10,7,$no++,1,0,'C');
    $pdf->Cell(25,7,date('d-m-Y', strtotime($row['tanggal'])),1,0,'C');
    $pdf->Cell(40,7,$row['nasabah'],1,0);
    $pdf->Cell(25,7,$row['kode_tarik'],1,0,'C');
    $pdf->Cell(30,7,'Rp '.number_format($row['jumlah'],0,',','.'),1,0,'R');
    $pdf->Cell(30,7,'Rp '.number_format($row['sisa_saldo'],0,',','.'),1,0,'R');
    $pdf->Cell(30,7,$row['keterangan'],1,1);
}

// =============== TANDA TANGAN ===============
$pdf->Ln(15);
$pdf->Cell(120,7,'',0,0);
$pdf->Cell(70,7,'Jakarta, '.date('d-m-Y'),0,1,'C');
$pdf->Cell(120,7,'',0,0);
$pdf->Cell(70,7,'Ketua Koperasi',0,1,'C');
$pdf->Ln(20);
$pdf->Cell(120,7,'',0,0);
$pdf->Cell(70,7,'(_____________)',0,1,'C');

$pdf->Output();
?>
