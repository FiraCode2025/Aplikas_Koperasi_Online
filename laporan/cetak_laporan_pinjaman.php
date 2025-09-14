<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../fpdf/fpdf.php';

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// ===== Kop Surat =====
// Logo kiri
$pdf->Image(__DIR__ . '/../img/logo-kiri.jpg', 15, 10, 20);
// Logo kanan
$pdf->Image(__DIR__ . '/../img/logo-kanan.jpg', 175, 10, 20);

// Judul
$pdf->SetFont('Arial','B',14);
$pdf->Cell(190,7,'KOPERASI MELATI RAYA ',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(190,5,'Jl. Contoh Alamat No.123, Kota - Provinsi',0,1,'C');
$pdf->Cell(190,5,'Telp: (021) 123456 | Email: info@koperasi.com',0,1,'C');
$pdf->Ln(3);

// Garis bawah
$pdf->Line(10,35,200,35);
$pdf->Ln(10);

// ===== Judul Laporan =====
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,7,'LAPORAN PINJAMAN',0,1,'C');
$pdf->Ln(5);

// ===== Header Tabel =====
$pdf->SetFont('Arial','B',9);
$pdf->Cell(10,10,'#',1,0,'C');
$pdf->Cell(25,10,'Kode Pinjam',1,0,'C');
$pdf->Cell(25,10,'Tanggal',1,0,'C');
$pdf->Cell(40,10,'Anggota',1,0,'C');
$pdf->Cell(30,10,'Jumlah Pinjam',1,0,'C');
$pdf->Cell(20,10,'Lama',1,0,'C');
$pdf->Cell(20,10,'Bunga',1,0,'C');
$pdf->Cell(20,10,'Status',1,1,'C');

// ===== Isi Data =====
$pdf->SetFont('Arial','',9);
$no = 1;

$sql = "SELECT 
            p.kode_pinjaman, 
            p.tanggal, 
            a.nama AS anggota, 
            p.jumlah, 
            p.lama_bulan, 
            p.bunga_persen, 
            p.status
        FROM pinjaman p
        JOIN anggota a ON p.anggota_id = a.id
        ORDER BY p.tanggal DESC";

$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10,8,$no++,1,0,'C');
    $pdf->Cell(25,8,$row['kode_pinjaman'],1,0,'C');
    $pdf->Cell(25,8,$row['tanggal'],1,0,'C');
    $pdf->Cell(40,8,$row['anggota'],1,0,'L');
    $pdf->Cell(30,8,'Rp '.number_format($row['jumlah'],0,',','.'),1,0,'R');
    $pdf->Cell(20,8,$row['lama_bulan'].' bln',1,0,'C');
    $pdf->Cell(20,8,$row['bunga_persen'].'%',1,0,'C');
    $pdf->Cell(20,8,ucfirst($row['status']),1,1,'C');
}

// ===== Tanda Tangan =====
$pdf->Ln(15);
$pdf->SetFont('Arial','',10);
$pdf->Cell(130,5,'',0,0);
$pdf->Cell(60,5,'Kepala Koperasi',0,1,'C');
$pdf->Ln(20);
$pdf->Cell(130,5,'',0,0);
$pdf->Cell(60,5,'__________________',0,1,'C');

$pdf->Output('I','laporan_pinjaman.pdf');
?>
