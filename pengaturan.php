<?php
require __DIR__ . '/db/koneksi.php';
include __DIR__ . '/includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}

$user = $_SESSION['user'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass_baru = $_POST['password'] ?? '';

    if (strlen($pass_baru) < 4) {
        $msg = '<div class="alert alert-danger">Password minimal 4 karakter</div>';
    } else {
        $hashed = hash('sha256', $pass_baru);

        $stmt = $mysqli->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param('si', $hashed, $user['id']);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $msg = '<div class="alert alert-success">Password berhasil diubah</div>';
        } else {
            $msg = '<div class="alert alert-warning">Password tidak berubah</div>';
        }
    }
}
?>

<h3>Pengaturan</h3>
<?= $msg ?>
<form method="post">
  <div class="mb-3">
    <label>Password Baru</label>
    <input type="password" class="form-control" name="password" required>
  </div>
  <button class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
