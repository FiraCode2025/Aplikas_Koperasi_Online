<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$id = (int)($_GET['id'] ?? 0);
$mysqli->query("DELETE FROM anggota WHERE id={$id}");
header("Location: /anggota/index.php?msg=" . urlencode("Data nasabah berhasil dihapus"));
exit();
?>