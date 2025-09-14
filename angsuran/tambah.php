<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $tanggal = $_POST['tanggal'] ?? null;
$pinjaman_id = $_POST['pinjaman_id'] ?? null;
$cicilan_ke = $_POST['cicilan_ke'] ?? null;
$pokok = $_POST['pokok'] ?? null;
$bunga = $_POST['bunga'] ?? null;
$total = $_POST['total'] ?? null;
  $stmt = $mysqli->prepare("INSERT INTO angsuran (tanggal, pinjaman_id, cicilan_ke, pokok, bunga, total) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("siiiii", $tanggal, $pinjaman_id, $cicilan_ke, $pokok, $bunga, $total);
  $ok = $stmt->execute();
  if($ok) $msg='Berhasil disimpan'; else $msg='Gagal menyimpan';
}
include __DIR__ . '/../includes/header.php';
?>
<h4>Tambah Angsuran</h4>
<?php if($msg): ?><div class="alert alert-info"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <div class="mb-3"><label class="form-label">Tanggal Bayar</label><input type="date" name="tanggal" class="form-control" required></div><div class="mb-3"><label class="form-label">Pinjaman ID</label><input type="number" name="pinjaman_id" class="form-control" step="1" min="0" required></div><div class="mb-3"><label class="form-label">Cicilan Ke</label><input type="number" name="cicilan_ke" class="form-control" step="1" min="0" required></div><div class="mb-3"><label class="form-label">Pokok Bayar</label><input type="number" name="pokok" class="form-control" step="1" min="0" required></div><div class="mb-3"><label class="form-label">Bunga Bayar</label><input type="number" name="bunga" class="form-control" step="1" min="0" required></div><div class="mb-3"><label class="form-label">Total Bayar</label><input type="number" name="total" class="form-control" step="1" min="0" required></div>
    <button class="btn btn-primary btn-rounded">Simpan</button>
    <a href="/angsuran/index.php" class="btn btn-light">Kembali</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
