<?php
// Koneksi database & library FPDF
require_once('../../db/koneksi.php');
require_once('../../fpdf/fpdf.php'); 

// Fungsi untuk load gambar dengan validasi format
function addImageSafe($pdf, $file, $x, $y, $w = 0, $h = 0) {
    if (file_exists($file)) {
        $info = @getimagesize($file);
        if ($info !== false) {
            $mime = $info['mime'];
            if ($mime === 'image/png' || $mime === 'image/jpeg') {
                $pdf->Image($file, $x, $y, $w, $h);
            }
        }
    }
}

// Ambil ID pinjaman dari parameter URL
$id = (int)($_GET['id'] ?? 0);

// Ambil data pinjaman + anggota
$stmt = $mysqli->prepare("SELECT 
    p.*, 
    a.nama AS nama_anggota, 
    a.alamat AS alamat_anggota 
FROM pinjaman p 
JOIN anggota a ON p.anggota_id = a.id 
WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pinjaman = $stmt->get_result()->fetch_assoc();

if (!$pinjaman) {
    die('Data pinjaman tidak ditemukan');
}

// Ambil data angsuran terkait
$angsuran_res = $mysqli->query("SELECT * FROM angsuran WHERE pinjaman_id = " . $pinjaman['id']);

// Buat objek PDF baru
$pdf = new FPDF();
$pdf->AddPage();

// ================= HEADER =================
addImageSafe($pdf, '../../img/logo-kiri.jpg', 10, 8, 25);
addImageSafe($pdf, '../../img/logo-kanan.jpg', 175, 8, 25);

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0, 10, 'LAPORAN ANGSURAN PINJAMAN', 0, 1, 'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0, 8, 'KOPERASI SIMPAN PINJAM "OPEN SOURCE"', 0, 1, 'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(0, 6, 'JJl. Contoh Alamat No.123, Kota - Provinsi', 0, 1, 'C');
$pdf->Cell(0, 6, 'medan selayang simpang pemda', 0, 1, 'C');
$pdf->Ln(3);
$pdf->Line(10, 42, 200, 42);
$pdf->Ln(5);

// ================= DETAIL PINJAMAN =================
$pdf->SetFont('Arial','',10);
$pdf->Cell(50, 6, 'NO. ANGGOTA', 0);       $pdf->Cell(1, 6, ':', 0); $pdf->Cell(0, 6, $pinjaman['anggota_id'], 0, 1);
$pdf->Cell(50, 6, 'NAMA ANGGOTA', 0);     $pdf->Cell(1, 6, ':', 0); $pdf->Cell(0, 6, $pinjaman['nama_anggota'], 0, 1);
$pdf->Cell(50, 6, 'ALAMAT', 0);           $pdf->Cell(1, 6, ':', 0); $pdf->Cell(0, 6, $pinjaman['alamat_anggota'], 0, 1);
// Bagian yang telah diperbaiki
$pdf->Cell(50, 6, 'BESAR PINJAMAN', 0);   $pdf->Cell(1, 6, ':', 0); $pdf->Cell(0, 6, 'Rp. ' . number_format($pinjaman['jumlah'], 2, ',', '.'), 0, 1);
// Akhir bagian yang diperbaiki
$pdf->Cell(50, 6, 'TANGGAL PENCAIRAN', 0);$pdf->Cell(1, 6, ':', 0); $pdf->Cell(0, 6, date('d F Y', strtotime($pinjaman['tanggal'])), 0, 1);
$pdf->Cell(50, 6, 'TANGGAL JATUH TEMPO', 0);$pdf->Cell(1, 6, ':', 0);$pdf->Cell(0, 6, date('d F Y', strtotime($pinjaman['jatuh_tempo'])), 0, 1);
$pdf->Cell(50, 6, 'JATUH TEMPO', 0);      $pdf->Cell(1, 6, ':', 0); $pdf->Cell(0, 6, $pinjaman['lama_bulan'] . ' Bulan', 0, 1);
$pdf->Ln(10);

// ================= TABEL ANGSURAN =================
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30, 8, 'Angsuran Pinjaman', 1, 0, 'C');
$pdf->Cell(35, 8, 'Tanggal Pembayaran', 1, 0, 'C');
$pdf->Cell(25, 8, 'Angsuran (Rp.)', 1, 0, 'C');
$pdf->Cell(20, 8, 'Bunga (Rp.)', 1, 0, 'C');
$pdf->Cell(20, 8, 'Denda (Rp.)', 1, 0, 'C');
$pdf->Cell(35, 8, 'Sisa Hutang Pokok (Rp.)', 1, 0, 'C');
$pdf->Cell(25, 8, 'Status', 1, 1, 'C');

$pdf->SetFont('Arial','',8);
$total_angsuran = 0;

if ($angsuran_res->num_rows > 0) {
    while ($angsuran = $angsuran_res->fetch_assoc()) {
        $pdf->Cell(30, 7, 'Angsuran ke-' . $angsuran['cicilan_ke'], 1, 0, 'C');
        $pdf->Cell(35, 7, date('d F Y', strtotime($angsuran['tanggal'])), 1, 0, 'C');
        $pdf->Cell(25, 7, number_format($angsuran['pokok'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(20, 7, number_format($angsuran['bunga'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(20, 7, number_format($angsuran['denda'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(35, 7, number_format($angsuran['sisa_hutang'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(25, 7, $angsuran['status'], 1, 1, 'C');

        $total_angsuran += $angsuran['total'];
    }
} else {
    // Tampilan jika belum ada angsuran, sesuai gambar
    $pdf->Cell(30, 7, 'Angsuran ke-1', 1, 0, 'C');
    $pdf->Cell(35, 7, 'Format tanggal tidak valid', 1, 0, 'C');
    $pdf->Cell(25, 7, 'Rp.0,00', 1, 0, 'R');
    $pdf->Cell(20, 7, 'Rp.0,00', 1, 0, 'R');
    $pdf->Cell(20, 7, 'Rp.0,00', 1, 0, 'R');
    $pdf->Cell(35, 7, 'Rp.0,00', 1, 0, 'R');
    $pdf->Cell(25, 7, 'Belum Lunas', 1, 1, 'C');
}

// Jumlah Total Angsuran
$pdf->SetFont('Arial','B',10);
$pdf->Cell(165, 10, 'Jumlah Total Angsuran:', 0, 0, 'R');
$pdf->Cell(40, 10, number_format($total_angsuran, 2, ',', '.'), 0, 1, 'L');
$pdf->Ln(15);

// ================= TANDA TANGAN =================
$pdf->SetX(120);
$pdf->Cell(0, 5, 'Pringsewu, ' . date('d F Y'), 0, 1);
$pdf->SetX(120);
$pdf->Cell(0, 5, 'Kepala Koperasi', 0, 1);
$pdf->Ln(20);
$pdf->SetX(120);
$pdf->Cell(0, 5, '______________________', 0, 1);

$pdf->Output();
?>