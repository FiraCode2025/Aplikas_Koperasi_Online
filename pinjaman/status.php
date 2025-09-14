<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

// Mendapatkan ID pinjaman dari URL
$id = (int)($_GET['id'] ?? 0);

// Mendapatkan status yang baru dari URL (contoh: disetujui, ditolak)
$status = $_GET['status'] ?? null;

// Periksa apakah ID dan status valid
if (!$id || !$status) {
    header("Location: /pinjaman/index.php?msg=" . urlencode('Invalid request.'));
    exit();
}

// Ambil nama pengguna yang sedang login untuk kolom 'updated_by'
$updated_by = $_SESSION['nama_pengguna'] ?? 'Administrator';

// Buat query SQL untuk memperbarui status pinjaman
$stmt = $mysqli->prepare("UPDATE pinjaman SET status = ?, updated_by = ? WHERE id = ?");
$stmt->bind_param("ssi", $status, $updated_by, $id);

$ok = $stmt->execute();

if ($ok) {
    $msg = 'Status pinjaman berhasil diubah menjadi ' . $status . '.';
    header("Location: /pinjaman/detail.php?id=" . $id . "&msg=" . urlencode($msg));
    exit();
} else {
    $msg = 'Gagal mengubah status pinjaman: ' . $mysqli->error;
    header("Location: /pinjaman/detail.php?id=" . $id . "&msg=" . urlencode($msg));
    exit();
}