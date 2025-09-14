<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$res = $mysqli->query("SELECT p.*, a.nama as nama_anggota FROM pinjaman p JOIN anggota a ON p.anggota_id = a.id ORDER BY p.id DESC");

include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Pinjaman</h4>
    <div class="d-flex gap-2">
        <a href="/pinjaman/tambah.php" class="btn btn-primary btn-rounded"><i class="bi bi-plus"></i> Tambah</a>
    </div>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Pinjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Nasabah</th>
                    <th>Jumlah Pinjam</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res->num_rows > 0): ?>
                    <?php $no = 1; while($r = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r['kode_pinjaman']) ?></td>
                        <td><?= htmlspecialchars($r['tanggal']) ?></td>
                        <td><?= htmlspecialchars($r['nama_anggota']) ?></td>
                        <td>Rp <?= number_format($r['jumlah'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($r['lama_bulan']) ?> bulan</td>
                        <td><?= htmlspecialchars($r['status']) ?></td>
                        <td width="160">
                            <a class="btn btn-sm btn-outline-info" href="/pinjaman/detail.php?id=<?= $r['id'] ?>"><i class="bi bi-eye"></i></a>
                            <a class="btn btn-sm btn-outline-secondary" href="/pinjaman/edit.php?id=<?= $r['id'] ?>"><i class="bi bi-pencil"></i></a>
                            <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')" href="/pinjaman/hapus.php?id=<?= $r['id'] ?>"><i class="bi bi-trash"></i></a>
                            
                            <a class="btn btn-sm btn-outline-primary" href="/pinjaman/laporan/cetak_pinjaman.php?id=<?= $r['id'] ?>" target="_blank" title="Cetak Laporan">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak Ada Transaksi Pinjaman</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>