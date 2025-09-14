<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    die('ID pinjaman tidak valid.');
}

// Cek apakah pinjaman memiliki angsuran terkait
$stmt_check = $mysqli->prepare("SELECT COUNT(*) FROM angsuran WHERE pinjaman_id = ?");
$stmt_check->bind_param("i", $id);
$stmt_check->execute();
$stmt_check->bind_result($angsuran_count);
$stmt_check->fetch();
$stmt_check->close();

if ($angsuran_count > 0) {
    // Jika ada angsuran, tidak diizinkan dihapus
    $_SESSION['flash_error'] = 'Data pinjaman tidak bisa dihapus karena memiliki angsuran terkait.';
    header("Location: /pinjaman/index.php");
    exit;
}

// Proses penghapusan data pinjaman
$stmt = $mysqli->prepare("DELETE FROM pinjaman WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = "Data pinjaman berhasil dihapus.";
    header("Location: /pinjaman/index.php");
    exit;
} else {
    $_SESSION['flash_error'] = "Gagal menghapus data pinjaman: " . $stmt->error;
    header("Location: /pinjaman/index.php");
    exit;
}
