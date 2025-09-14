<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$id = (int)($_GET['id'] ?? 0);
$data = $mysqli->query("SELECT * FROM users WHERE id={$id}")->fetch_assoc();
if(!$data){ die('Data tidak ditemukan'); }
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = $_POST['name'] ?? null;$email = $_POST['email'] ?? null;$password = $_POST['password'] ?? null;$role = $_POST['role'] ?? null;
  $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id=?");
  $stmt->bind_param("sssss", $name, $email, $password, $role, $id);
  $ok=$stmt->execute();
  if($ok) $msg='Berhasil diupdate'; else $msg='Gagal update';
}
include __DIR__ . '/../includes/header.php';
?>
<h4>Edit Users</h4>
<?php if($msg): ?><div class="alert alert-info"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" value="<?= esc($name) ?>" required></div><div class="mb-3"><label class="form-label">Email</label><input name="email" class="form-control" value="<?= esc($email) ?>" required></div><div class="mb-3"><label class="form-label">Password (hash SHA256)</label><input name="password" class="form-control" value="<?= esc($password) ?>" required></div><div class="mb-3"><label class="form-label">Role</label><input name="role" class="form-control" value="<?= esc($role) ?>" required></div>
    <button class="btn btn-primary btn-rounded">Update</button>
    <a href="/users/index.php" class="btn btn-light">Kembali</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
