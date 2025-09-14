<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$msg = $_GET['msg'] ?? '';

// Fetch data for members, including a calculated saldo from simpanan table
$res = $mysqli->query("
    SELECT 
        a.id, a.nama, a.jenis_kelamin, a.pekerjaan, a.alamat, a.no_ktp, a.status, a.created_at AS update_at, 
        COALESCE(SUM(s.jumlah), 0) AS saldo
    FROM 
        anggota a
    LEFT JOIN 
        simpanan s ON a.id = s.anggota_id
    GROUP BY 
        a.id
    ORDER BY 
        a.created_at DESC
");

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Nasabah</h4>
    <a href="/anggota/tambah.php" class="btn btn-primary btn-rounded"><i class="bi bi-plus"></i> Tambah</a>
</div>

<?php if ($msg): ?>
<div class="alert alert-success d-flex align-items-center" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    <div>
        <?= htmlspecialchars($msg) ?>
    </div>
</div>
<?php endif; ?>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>JenKel</th>
                    <th>Pekerjaan</th>
                    <th>No KTP</th>
                    <th>Alamat</th>
                    <th>Saldo</th>
                    <th>Status</th>
                    <th>Update_at</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <td><?= htmlspecialchars($r['jenis_kelamin']) ?></td>
                    <td><?= htmlspecialchars($r['pekerjaan']) ?></td>
                    <td><?= htmlspecialchars($r['no_ktp']) ?></td>
                    <td><?= htmlspecialchars($r['alamat']) ?></td>
                    <td><?= 'Rp ' . number_format($r['saldo'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td><?= htmlspecialchars($r['update_at']) ?></td>
                    <td width="160">
                        <a class="btn btn-sm btn-outline-secondary" href="/anggota/detail.php?id=<?= $r['id'] ?>">Lihat</a>
                        <a class="btn btn-sm btn-outline-warning" href="/anggota/edit.php?id=<?= $r['id'] ?>"><i class="bi bi-pencil"></i></a>
                        <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')" href="/anggota/hapus.php?id=<?= $r['id'] ?>"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>