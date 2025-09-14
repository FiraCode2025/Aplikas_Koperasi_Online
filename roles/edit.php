<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$id = (int)($_GET['id'] ?? 0);
$data = $mysqli->query("SELECT * FROM roles WHERE id={$id}")->fetch_assoc();
if(!$data){ die('Data tidak ditemukan'); }
$nama = $data['nama'] ?? '';
$keterangan = $data['keterangan'] ?? '';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = $_POST['nama'] ?? null;$keterangan = $_POST['keterangan'] ?? null;
  $stmt = $mysqli->prepare("UPDATE roles SET nama = ?, keterangan = ? WHERE id=?");
  $stmt->bind_param("sss", $nama, $keterangan, $id);
  $ok=$stmt->execute();
  if($ok) $msg='Berhasil diupdate'; else $msg='Gagal update';
}
include __DIR__ . '/../includes/header.php';
?>
<h4>Edit Role</h4>
<?php if($msg): ?><div class="alert alert-info"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <div class="mb-3"><label class="form-label">Nama Role</label><input name="nama" class="form-control" value="<?= esc($nama) ?>" required></div><div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control" value="<?= esc($keterangan) ?>" required></div>
    <button class="btn btn-primary btn-rounded">Update</button>
    <a href="/roles/index.php" class="btn btn-light">Kembali</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
