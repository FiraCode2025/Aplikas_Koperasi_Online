<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$id = (int)($_GET['id'] ?? 0);
$data = $mysqli->query("SELECT * FROM angsuran WHERE id={$id}")->fetch_assoc();
if(!$data){ die('Data tidak ditemukan'); }
$tanggal = $data['tanggal'] ?? '';
$pinjaman_id = $data['pinjaman_id'] ?? '';
$cicilan_ke = $data['cicilan_ke'] ?? '';
$pokok = $data['pokok'] ?? '';
$bunga = $data['bunga'] ?? '';
$total = $data['total'] ?? '';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $tanggal = $_POST['tanggal'] ?? null;$pinjaman_id = $_POST['pinjaman_id'] ?? null;$cicilan_ke = $_POST['cicilan_ke'] ?? null;$pokok = $_POST['pokok'] ?? null;$bunga = $_POST['bunga'] ?? null;$total = $_POST['total'] ?? null;
  $stmt = $mysqli->prepare("UPDATE angsuran SET tanggal = ?, pinjaman_id = ?, cicilan_ke = ?, pokok = ?, bunga = ?, total = ? WHERE id=?");
  $stmt->bind_param("siiiiis", $tanggal, $pinjaman_id, $cicilan_ke, $pokok, $bunga, $total, $id);
  $ok=$stmt->execute();
  if($ok) $msg='Berhasil diupdate'; else $msg='Gagal update';
}
include __DIR__ . '/../includes/header.php';
?>
<h4>Edit Angsuran</h4>
<?php if($msg): ?><div class="alert alert-info"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <div class="mb-3"><label class="form-label">Tanggal Bayar</label><input type="date" name="tanggal" class="form-control" value="<?= esc($tanggal) ?>" required></div><div class="mb-3"><label class="form-label">Pinjaman ID</label><input type="number" name="pinjaman_id" class="form-control" value="<?= esc($pinjaman_id) ?>" step="1" min="0" required></div><div class="mb-3"><label class="form-label">Cicilan Ke</label><input type="number" name="cicilan_ke" class="form-control" value="<?= esc($cicilan_ke) ?>" step="1" min="0" required></div><div class="mb-3"><label class="form-label">Pokok Bayar</label><input type="number" name="pokok" class="form-control" value="<?= esc($pokok) ?>" step="1" min="0" required></div><div class="mb-3"><label class="form-label">Bunga Bayar</label><input type="number" name="bunga" class="form-control" value="<?= esc($bunga) ?>" step="1" min="0" required></div><div class="mb-3"><label class="form-label">Total Bayar</label><input type="number" name="total" class="form-control" value="<?= esc($total) ?>" step="1" min="0" required></div>
    <button class="btn btn-primary btn-rounded">Update</button>
    <a href="/angsuran/index.php" class="btn btn-light">Kembali</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
