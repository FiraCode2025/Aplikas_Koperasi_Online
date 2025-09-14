<?php
$config = require __DIR__ . '/../config.php';

$mysqli = new mysqli(
    $config['db_host'],
    $config['db_user'],
    $config['db_pass'],
    $config['db_name']
);

if ($mysqli->connect_errno) {
    die('Koneksi database gagal: ' . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

// Cek dulu sebelum bikin biar ga redeclare
if (!function_exists('esc')) {
    function esc($s) {
        return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Jangan lupa pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
