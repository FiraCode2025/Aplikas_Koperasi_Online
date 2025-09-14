<?php
// auth.php
session_start();

// Cek jika pengguna BELUM login (sesi 'user' tidak ada)
if (!isset($_SESSION['user'])) {
    // Alihkan ke halaman login
    header("Location: /login.php");
    exit;
}
?>