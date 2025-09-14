<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal    = $_POST['tanggal'] ?? null;
    $anggota_id = $_POST['anggota_id'] ?? null;
    $jumlah     = $_POST['jumlah'] ?? null;
    $keterangan = $_POST['keterangan'] ?? null;

    $stmt = $mysqli->prepare("INSERT INTO penarikan (tanggal, anggota_id, jumlah, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $tanggal, $anggota_id, $jumlah, $keterangan);
    $ok = $stmt->execute();
    if ($ok) {
        $msg = 'Berhasil disimpan';
    } else {
        $msg = 'Gagal menyimpan';
    }
}
include __DIR__ . '/../includes/header.php';
?>
<h4>Tambah Penarikan</h4>
<?php if ($msg): ?><div class="alert alert-info"><?= esc($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Tanggal</label>
      <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Anggota</label>
      <select name="anggota_id" class="form-select" required>
        <option value="">- pilih anggota -</option>
        <?php
        $ags = $mysqli->query("SELECT id, nama FROM anggota ORDER BY nama ASC");
        while ($a = $ags->fetch_assoc()): ?>
          <option value="<?= $a['id'] ?>"><?= esc($a['nama']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Jumlah penarikan</label>
      <input type="number" name="jumlah" class="form-control" step="1" min="0" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Keterangan</label>
      <input type="text" name="keterangan" class="form-control">
    </div>
    <button class="btn btn-primary btn-rounded">Simpan</button>
    <a href="/penarikan/index.php" class="btn btn-light">Kembali</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
