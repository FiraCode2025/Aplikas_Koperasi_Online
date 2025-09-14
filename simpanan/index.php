<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$res = $mysqli->query("SELECT s.*, a.nama as nama_anggota FROM simpanan s JOIN anggota a ON s.anggota_id = a.id ORDER BY s.id DESC");

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Simpanan</h4>
    <a href="/simpanan/tambah.php" class="btn btn-primary btn-rounded"><i class="bi bi-plus"></i> Tambah</a>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Transaksi</th>
                    <th>Tanggal</th>
                    <th>Anggota</th>
                    <th>Jenis Simpanan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($r = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r['kode_transaksi']) ?></td>
                        <td><?= htmlspecialchars($r['tanggal']) ?></td>
                        <td><?= htmlspecialchars($r['nama_anggota']) ?></td>
                        <td><?= htmlspecialchars($r['jenis']) ?></td>
                        <td>Rp <?= number_format($r['jumlah'], 0, ',', '.') ?></td>
                        <td width="160">
                            <a class="btn btn-sm btn-outline-info" href="/simpanan/detail.php?id=<?= $r['id'] ?>">Lihat</a>
                            <a class="btn btn-sm btn-outline-secondary" href="/simpanan/edit.php?id=<?= $r['id'] ?>"><i class="bi bi-pencil"></i></a>
                            <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')" href="/simpanan/hapus.php?id=<?= $r['id'] ?>"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>