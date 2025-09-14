<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$id = (int)($_GET['id'] ?? 0);
$mysqli->query("DELETE FROM penarikan WHERE id={$id}");
header("Location: /penarikan/index.php");
