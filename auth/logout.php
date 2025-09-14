<?php
// Mulai sesi
session_start();

// Hancurkan semua data di dalam sesi
session_destroy();

// Alihkan pengguna ke halaman login
// Pastikan path ini mengarah ke halaman login yang benar
header("Location: /login.php"); 
exit; // Pastikan skrip berhenti di sini untuk mencegah eksekusi kode lain
?>