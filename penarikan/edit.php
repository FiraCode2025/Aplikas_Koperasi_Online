<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$id = (int)($_GET['id'] ?? 0);
$data = $mysqli->query("SELECT * FROM penarikan WHERE id={$id}")->fetch_assoc();
if(!$data){ die('Data tidak ditemukan'); }
$tanggal = $data['tanggal'] ?? '';
$anggota_id = $data['anggota_id'] ?? '';
$jumlah = $data['jumlah'] ?? '';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $tanggal = $_POST['tanggal'] ?? null;$anggota_id = $_POST['anggota_id'] ?? null;$jumlah = $_POST['jumlah'] ?? null;
  $stmt = $mysqli->prepare("UPDATE penarikan SET tanggal = ?, anggota_id = ?, jumlah = ? WHERE id=?");
  $stmt->bind_param("ssis", $tanggal, $anggota_id, $jumlah, $id);
  $ok=$stmt->execute();
  if($ok) $msg='Berhasil diupdate'; else $msg='Gagal update';
}
include __DIR__ . '/../includes/header.php';
?>
<h4>Edit Penarikan</h4>
<?php if($msg): ?><div class="alert alert-info"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= esc($tanggal) ?>" required></div>
<div class="mb-3">
  <label class="form-label">Anggota</label>
  <select name="anggota_id" class="form-select" required>
    <option value="">- pilih anggota -</option>
    <?php $ags = $mysqli->query("SELECT id, nama FROM anggota ORDER BY nama ASC"); while($a=$ags->fetch_assoc()): ?>
      <option value="<?= $a['id'] ?>" <?= ($a['id']==$anggota_id)?'selected':'' ?>><?= esc($a['nama']) ?></option>
    <?php endwhile; ?>
  </select>
</div><div class="mb-3"><label class="form-label">Jumlah</label><input type="number" name="jumlah" class="form-control" value="<?= esc($jumlah) ?>" step="1" min="0" required></div>
    <button class="btn btn-primary btn-rounded">Update</button>
    <a href="/penarikan/index.php" class="btn btn-light">Kembali</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
