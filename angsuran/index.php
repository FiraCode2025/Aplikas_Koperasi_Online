<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$res = $mysqli->query("SELECT * FROM angsuran ORDER BY id DESC");
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Angsuran</h4>
  <a href="/angsuran/tambah.php" class="btn btn-primary btn-rounded"><i class="bi bi-plus"></i> Tambah</a>
</div>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th></th><th>Tanggal Bayar</th><th>Pinjaman ID</th><th>Cicilan Ke</th><th>Pokok Bayar</th><th>Bunga Bayar</th><th>Total Bayar</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $no=1; while($r = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= esc($r['tanggal']) ?></td><td><?= esc($r['pinjaman_id']) ?></td><td><?= esc($r['cicilan_ke']) ?></td><td><?= esc($r['pokok']) ?></td><td><?= esc($r['bunga']) ?></td><td><?= esc($r['total']) ?></td>
          <td width="160">
            <a class="btn btn-sm btn-outline-secondary" href="/angsuran/edit.php?id=<?= $r['id'] ?>"><i class="bi bi-pencil"></i></a>
            <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')" href="/angsuran/hapus.php?id=<?= $r['id'] ?>"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
