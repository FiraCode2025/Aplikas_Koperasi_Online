<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $role = $_POST['role'] ?? null;

    // Cek email duplikat
    $check = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $msg = 'Email sudah terdaftar!';
    } else {
        // Hash password sebelum disimpan (penting untuk keamanan)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password_hash, $role);

        if ($stmt->execute()) {
            $msg = 'Berhasil disimpan';
        } else {
            $msg = 'Gagal menyimpan: ' . $stmt->error;
        }
        $stmt->close();
    }

    $check->close();
}

include __DIR__ . '/../includes/header.php';
?>
<h4>Tambah Users</h4>
<?php if($msg): ?><div class="alert alert-info"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Nama</label>
      <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Role</label>
      <input name="role" class="form-control" required>
    </div>
    <button class="btn btn-primary btn-rounded">Simpan</button>
    <a href="/users/index.php" class="btn btn-light">Kembali</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
