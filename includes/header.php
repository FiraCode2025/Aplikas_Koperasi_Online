<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

// Info user
$user = $_SESSION['user'] ?? null;
$userName = $user['nama'] ?? 'Administrator';
$userRole = $user['role'] ?? 'Admin';
$userImage = $user['gambar'] ?? 'default.jpg';

// Path foto profil
$profileImagePath = __DIR__ . '/../uploads/profiles/' . $userImage;
$imageSrc = file_exists($profileImagePath) && $userImage !== 'default.jpg'
    ? '/uploads/profiles/' . $userImage . '?v=' . time()
    : 'https://via.placeholder.com/80x80/1f2c3f/ffffff?text=' . strtoupper(substr($userName,0,1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Koperasi | <?= $userName ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background:#f6f8fb; margin:0; font-family:'Segoe UI',sans-serif; }
.sidebar { width:260px; position:fixed; top:0; bottom:0; left:0; background:#112031; color:#fff; display:flex; flex-direction:column; overflow-y:auto; }
.sidebar::-webkit-scrollbar { width:6px; }
.sidebar::-webkit-scrollbar-track { background:#1a2332; }
.sidebar::-webkit-scrollbar-thumb { background:#1f2c3f; border-radius:3px; }
.sidebar a { color:#cbd5e1; text-decoration:none; display:block; padding:12px 18px; border-radius:8px; margin:4px 10px; }
.sidebar a:hover { background:#1f2c3f; color:#fff; }
.sidebar a.active { background:#1f2c3f; color:#fff; }
.profile-section { padding:20px; text-align:center; border-bottom:1px solid rgba(255,255,255,0.1); flex-shrink:0; }
.profile-image { width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,0.1); flex-shrink:0; }
.content { margin-left:260px; padding:24px; min-height:100vh; }
.card { border:0; border-radius:18px; box-shadow:0 6px 18px rgba(0,0,0,.05); transition: transform 0.3s ease, box-shadow 0.3s ease; }
.card:hover { transform:translateY(-5px); box-shadow:0 12px 25px rgba(0,0,0,.1); }
</style>
</head>
<body>

<div class="sidebar">
  <div class="profile-section">
    <img src="<?= $imageSrc ?>" class="profile-image mb-3" alt="Profile">
    <div>
      <strong class="d-block text-white"><?= htmlspecialchars($userName) ?></strong>
      <small class="text-muted"><?= htmlspecialchars($userRole) ?></small>
    </div>
  </div>

  <div class="flex-grow-1 p-2">
    <a href="/index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="/users/index.php" class="<?= str_contains($_SERVER['PHP_SELF'],'users')?'active':'' ?>"><i class="bi bi-people me-2"></i>User</a>
    <a href="/anggota/index.php" class="<?= str_contains($_SERVER['PHP_SELF'],'anggota')?'active':'' ?>"><i class="bi bi-person-bounding-box me-2"></i>Nasabah</a>
    <a href="/simpanan/index.php" class="<?= str_contains($_SERVER['PHP_SELF'],'simpanan')?'active':'' ?>"><i class="bi bi-piggy-bank me-2"></i>Simpanan</a>
    <a href="/pinjaman/index.php" class="<?= str_contains($_SERVER['PHP_SELF'],'pinjaman')?'active':'' ?>"><i class="bi bi-cash-coin me-2"></i>Pinjaman</a>
    <a href="/penarikan/index.php" class="<?= str_contains($_SERVER['PHP_SELF'],'penarikan')?'active':'' ?>"><i class="bi bi-wallet2 me-2"></i>Penarikan</a>
    <a href="/laporan/index.php" class="<?= str_contains($_SERVER['PHP_SELF'],'laporan')?'active':'' ?>"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a>
    <a href="/roles/index.php" class="<?= str_contains($_SERVER['PHP_SELF'],'roles')?'active':'' ?>"><i class="bi bi-shield-lock me-2"></i>Role</a>
    <a href="/profil.php" class="<?= basename($_SERVER['PHP_SELF'])=='profil.php'?'active':'' ?>"><i class="bi bi-person me-2"></i>Profil</a>
    <a href="/pengaturan.php" class="<?= basename($_SERVER['PHP_SELF'])=='pengaturan.php'?'active':'' ?>"><i class="bi bi-gear me-2"></i>Pengaturan</a>
    <a href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
  </div>
</div>

<div class="content">