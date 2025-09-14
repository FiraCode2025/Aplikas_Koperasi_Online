<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    die("ID angsuran tidak valid.");
}

// Cek apakah data angsuran ada
$stmt = $mysqli->prepare("SELECT * FROM angsuran WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Data angsuran tidak ditemukan. (Debug: id=$id)");
}

// Hapus angsuran
$stmt_del = $mysqli->prepare("DELETE FROM angsuran WHERE id = ?");
$stmt_del->bind_param("i", $id);

if ($stmt_del->execute()) {
    // Balik ke halaman detail pinjaman setelah hapus
    header("Location: /pinjaman/detail.php?id=" . $data['pinjaman_id']);
    exit;
} else {
    echo "Gagal menghapus angsuran: " . $stmt_del->error;
}
