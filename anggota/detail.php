<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$id = (int)($_GET['id'] ?? 0);
$data = $mysqli->query("SELECT * FROM anggota WHERE id={$id}")->fetch_assoc();
if(!$data){ die('Data tidak ditemukan'); }

$nama = htmlspecialchars($data['nama'] ?? '');
$no_ktp = htmlspecialchars($data['no_ktp'] ?? '');
$alamat = htmlspecialchars($data['alamat'] ?? '');
$status = htmlspecialchars($data['status'] ?? '');
$tanggal_lahir = htmlspecialchars($data['tanggal_lahir'] ?? '');
$pekerjaan = htmlspecialchars($data['pekerjaan'] ?? '');
$email = htmlspecialchars($data['email'] ?? '');
$tanggal_gabung = htmlspecialchars($data['tanggal_gabung'] ?? '');
$agama = htmlspecialchars($data['agama'] ?? '');
$jenis_kelamin = htmlspecialchars($data['jenis_kelamin'] ?? '');
$telepon = htmlspecialchars($data['telepon'] ?? '');
$foto_path = htmlspecialchars($data['foto_path'] ?? '');

// Fetch saldo from simpanan table
$saldo_res = $mysqli->query("SELECT COALESCE(SUM(jumlah), 0) AS total_saldo FROM simpanan WHERE anggota_id = {$id}");
$saldo_data = $saldo_res->fetch_assoc();
$saldo = $saldo_data['total_saldo'] ?? 0;
$formatted_saldo = 'Rp ' . number_format($saldo, 0, ',', '.');

include __DIR__ . '/../includes/header.php';
?>

<h4>Detail Anggota</h4>
<div class="card p-3">
    <div class="row">
        <div class="col-md-5 d-flex flex-column">
            <div class="card flex-grow-1">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-0">
                    <?php if ($foto_path): ?>
                        <img src="<?= $foto_path ?>" alt="Foto Anggota" class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <img src="/img/placeholder.png" alt="Foto Anggota" class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Nama</th><td><?= $nama ?></td></tr>
                    <tr><th>No KTP</th><td><?= $no_ktp ?></td></tr>
                    <tr><th>Email</th><td><?= $email ?></td></tr>
                    <tr><th>Jenis Kelamin</th><td><?= $jenis_kelamin ?></td></tr>
                    <tr><th>Tanggal Lahir</th><td><?= $tanggal_lahir ?></td></tr>
                    <tr><th>Pekerjaan</th><td><?= $pekerjaan ?></td></tr>
                    <tr><th>Alamat</th><td><?= $alamat ?></td></tr>
                    <tr><th>Agama</th><td><?= $agama ?></td></tr>
                    <tr><th>Telepon</th><td><?= $telepon ?></td></tr>
                    <tr><th>Status Anggota</th><td><?= $status ?></td></tr>
                    <tr><th>Saldo</th><td><?= $formatted_saldo ?></td></tr>
                    <tr><th>Tanggal Gabung</th><td><?= $tanggal_gabung ?></td></tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="/anggota/index.php" class="btn btn-light btn-lg">Kembali</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>