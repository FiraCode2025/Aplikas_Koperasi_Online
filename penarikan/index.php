<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$query = "SELECT p.id, p.tanggal, a.nama as nama_anggota, p.jumlah, p.keterangan
          FROM penarikan p
          JOIN anggota a ON p.anggota_id = a.id
          ORDER BY p.tanggal DESC";
$res = $mysqli->query($query);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Penarikan</h3>
    <div>
      <a href="tambah.php" class="btn btn-primary btn-sm">Tambah</a>
      <a href="cetak_penarikan.php" target="_blank" class="btn btn-success btn-sm">
        <i class="bi bi-printer"></i> Cetak Laporan
      </a>
    </div>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Anggota</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($r = $res->fetch_assoc()): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
            <td><?= htmlspecialchars($r['nama_anggota']) ?></td>
            <td>Rp <?= number_format($r['jumlah'], 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($r['keterangan']) ?></td>
            <td width="160">
              <a class="btn btn-sm btn-outline-secondary"
                 href="edit.php?id=<?= $r['id'] ?>">
                 <i class="bi bi-pencil"></i>
              </a>
              <a class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Hapus data ini?')"
                 href="hapus.php?id=<?= $r['id'] ?>">
                 <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
