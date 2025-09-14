<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pinjaman_id     = $_POST['pinjaman_id'] ?? 0;
    $tanggal_angsuran = date('Y-m-d'); // gunakan format DB
    $jumlah_angsuran = (int)($_POST['jumlah_angsuran'] ?? 0);
    $jumlah_bunga    = (int)($_POST['jumlah_bunga'] ?? 0);
    $jumlah_denda    = (int)($_POST['jumlah_denda'] ?? 0);

    // ambil data pinjaman untuk hitung sisa hutang & cicilan ke-
    $stmt = $mysqli->prepare("SELECT jumlah FROM pinjaman WHERE id=?");
    $stmt->bind_param("i", $pinjaman_id);
    $stmt->execute();
    $pinjaman = $stmt->get_result()->fetch_assoc();

    // ambil cicilan terakhir
    $res = $mysqli->query("SELECT MAX(cicilan_ke) as cicilan_ke, sisa_hutang 
                           FROM angsuran WHERE pinjaman_id=$pinjaman_id");
    $last = $res->fetch_assoc();

    $cicilan_ke = ($last['cicilan_ke'] ?? 0) + 1;
    $sisa_hutang = ($last['sisa_hutang'] ?? $pinjaman['jumlah']) - $jumlah_angsuran;

    if ($sisa_hutang < 0) $sisa_hutang = 0;

    $total_angsuran = $jumlah_angsuran + $jumlah_bunga + $jumlah_denda;
    $status = ($sisa_hutang == 0) ? "LUNAS" : "BELUM LUNAS";

    // upload bukti pembayaran (opsional)
    $bukti_pembayaran = null;
    if (!empty($_FILES['bukti_pembayaran']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES['bukti_pembayaran']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $targetFile)) {
            $bukti_pembayaran = $filename;
        }
    }

    // buat kode angsuran otomatis
    $kode_angsuran = "ANGS-" . date('Ymd') . "-" . uniqid();

    // simpan ke database
    $stmt = $mysqli->prepare("INSERT INTO angsuran 
        (pinjaman_id, kode_angsuran, tanggal_angsuran, sisa_hutang, bunga, cicilan_ke, status, total_angsuran, bukti_pembayaran) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "issdiiiss",
        $pinjaman_id,
        $kode_angsuran,
        $tanggal_angsuran,
        $sisa_hutang,
        $jumlah_bunga,
        $cicilan_ke,
        $status,
        $total_angsuran,
        $bukti_pembayaran
    );

    if ($stmt->execute()) {
        header("Location: detail_pinjaman.php?id=" . $pinjaman_id . "&success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    header("Location: index.php");
    exit;
}
