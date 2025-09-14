<?php
session_start();
require __DIR__ . "/db/koneksi.php"; // pastikan ini sesuai dengan lokasi koneksi database

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit;
}

// Fungsi esc() agar data aman ditampilkan di HTML
function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Ambil data user dari session
$user = $_SESSION['user'];
$userId = $user['id']; 
$userName = $user['nama'] ?? $user['name'] ?? 'Administrator'; 
$userRole = $user['role'];
$userImage = $user['gambar'] ?? 'default.jpg';

$message = '';

// Logika unggah file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profil_gambar'])) {
    $file = $_FILES['profil_gambar'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // Max 5MB
                $fileNameNew = 'profile_' . $userId . '_' . uniqid() . '.' . $fileExt;
                $fileDestination = __DIR__ . '/uploads/profiles/' . $fileNameNew;

                // Pastikan direktori ada
                if (!is_dir(__DIR__ . '/uploads/profiles')) {
                    mkdir(__DIR__ . '/uploads/profiles', 0777, true);
                }

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Hapus gambar lama jika ada & bukan default
                    if ($userImage !== 'default.jpg' && file_exists(__DIR__ . '/uploads/profiles/' . $userImage)) {
                        unlink(__DIR__ . '/uploads/profiles/' . $userImage);
                    }

                    // Update database
                    $stmt = $mysqli->prepare("UPDATE users SET gambar = ? WHERE id = ?");
                    $stmt->bind_param("si", $fileNameNew, $userId);
                    $stmt->execute();
                    $stmt->close();

                    // Update session
                    $_SESSION['user']['gambar'] = $fileNameNew;

                    $message = '<div class="alert alert-success">Gambar profil berhasil diperbarui!</div>';
                } else {
                    $message = '<div class="alert alert-danger">Gagal mengunggah file.</div>';
                }
            } else {
                $message = '<div class="alert alert-danger">Ukuran file maksimal 5MB.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Terjadi kesalahan saat upload.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Format file hanya JPG, JPEG, PNG.</div>';
    }
}

// Tentukan path gambar
$profileImagePath = __DIR__ . '/uploads/profiles/' . ($user['gambar'] ?? 'default.jpg');
$imageSrc = file_exists($profileImagePath) && ($user['gambar'] ?? 'default.jpg') !== 'default.jpg' 
    ? 'uploads/profiles/' . $user['gambar'] 
    : 'https://via.placeholder.com/100x100/3498db/ffffff?text=' . strtoupper(substr($userName,0,1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Profil Pengguna</h4>
            </div>
            <div class="card-body">
                <?= $message ?>
                <div class="d-flex flex-column align-items-center mb-4">
                    <img src="<?= $imageSrc ?>" alt="Profil Pengguna" class="rounded-circle mb-3" width="100" height="100">
                    <h5 class="mb-0"><?= esc($userName) ?></h5>
                    <span class="text-muted"><?= esc($userRole) ?></span>
                </div>
                <form action="profil.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="profil_gambar" class="form-label">Ganti Gambar Profil</label>
                        <input class="form-control" type="file" id="profil_gambar" name="profil_gambar" accept="image/png, image/jpeg, image/jpg">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Unggah</button>
                    <a href="/index.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
