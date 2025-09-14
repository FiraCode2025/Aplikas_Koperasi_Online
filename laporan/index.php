<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
include __DIR__ . '/../includes/header.php';
?>
<h4>Laporan</h4>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card p-3">
      <h6>Laporan Simpanan</h6>
      <a class="btn btn-outline-primary btn-sm" href="/laporan/simpanan.php">Buka</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h6>Laporan Pinjaman</h6>
      <a class="btn btn-outline-primary btn-sm" href="/laporan/pinjaman.php">Buka</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h6>Laporan Penarikan</h6>
      <a class="btn btn-outline-primary btn-sm" href="/laporan/penarikan.php">Buka</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
