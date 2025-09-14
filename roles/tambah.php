<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = $_POST['nama'] ?? null;
$keterangan = $_POST['keterangan'] ?? null;
  $stmt = $mysqli->prepare("INSERT INTO roles (nama, keterangan) VALUES (?, ?)");
  $stmt->bind_param("ss", $nama, $keterangan);
  $ok = $stmt->execute();
  if($ok) $msg='Berhasil disimpan'; else $msg='Gagal menyimpan';
}
include __DIR__ . '/../includes/header.php';
?>
<h4>Tambah Role</h4>
<?php if($msg): ?><div class="alert alert-info"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <div class="mb-3"><label class="form-label">Nama Role</label><input name="nama" class="form-control" required></div><div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control" required></div>
    <button class="btn btn-primary btn-rounded">Simpan</button>
    <a href="/roles/index.php" class="btn btn-light">Kembali</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
