<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$id = (int)($_GET['id'] ?? 0);
$mysqli->query("DELETE FROM angsuran WHERE id={$id}");
header("Location: /angsuran/index.php");
