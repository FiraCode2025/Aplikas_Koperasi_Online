<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$id = (int)($_GET['id'] ?? 0);

// Mengambil data dari tabel simpanan dan anggota
$stmt = $mysqli->prepare("SELECT
                            s.*,
                            a.nama as nama_anggota,
                            a.no_ktp as no_ktp_anggota,
                            a.tanggal_lahir as tanggal_lahir_anggota,
                            a.pekerjaan as pekerjaan_anggota,
                            a.email as email_anggota,
                            a.telepon as telepon_anggota,
                            a.agama as agama_anggota,
                            a.jenis_kelamin as jenis_kelamin_anggota,
                            a.alamat as alamat_anggota,
                            a.status as status_anggota,
                            a.tanggal_gabung as tanggal_gabung_anggota,
                            a.foto_path as foto_anggota_path
                          FROM simpanan s
                          JOIN anggota a ON s.anggota_id = a.id
                          WHERE s.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die('Data tidak ditemukan');
}

include __DIR__ . '/../includes/header.php';

// Mendapatkan ekstensi file bukti pembayaran
$buktiPembayaranExtension = pathinfo($data['bukti_pembayaran_path'], PATHINFO_EXTENSION);
?>

<h4>Detail Transaksi Simpanan</h4>
<div class="card p-3">
    <div class="row">
        <div class="col-md-6">
            <h5>Informasi Anggota</h5>
            <div class="card p-3 mb-3 text-center">
                <?php if ($data['foto_anggota_path']): ?>
                    <img src="<?= htmlspecialchars($data['foto_anggota_path']) ?>" alt="Foto Anggota" class="img-fluid rounded mx-auto d-block" style="max-width: 350px;">
                <?php else: ?>
                    <p class="text-muted">Tidak ada foto anggota</p>
                <?php endif; ?>
            </div>
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Nama Anggota</th><td><?= htmlspecialchars($data['nama_anggota']) ?></td></tr>
                    <tr><th>No KTP Anggota</th><td><?= htmlspecialchars($data['no_ktp_anggota']) ?></td></tr>
                    <tr><th>Tanggal Lahir</th><td><?= htmlspecialchars($data['tanggal_lahir_anggota']) ?></td></tr>
                    <tr><th>Pekerjaan</th><td><?= htmlspecialchars($data['pekerjaan_anggota']) ?></td></tr>
                    <tr><th>Email</th><td><?= htmlspecialchars($data['email_anggota']) ?></td></tr>
                    <tr><th>Telepon</th><td><?= htmlspecialchars($data['telepon_anggota']) ?></td></tr>
                    <tr><th>Agama</th><td><?= htmlspecialchars($data['agama_anggota']) ?></td></tr>
                    <tr><th>Jenis Kelamin</th><td><?= htmlspecialchars($data['jenis_kelamin_anggota']) ?></td></tr>
                    <tr><th>Alamat</th><td><?= htmlspecialchars($data['alamat_anggota']) ?></td></tr>
                    <tr><th>Status Anggota</th><td><?= htmlspecialchars($data['status_anggota']) ?></td></tr>
                    <tr><th>Tanggal Gabung</th><td><?= htmlspecialchars($data['tanggal_gabung_anggota']) ?></td></tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h5>Informasi Simpanan</h5>
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Kode Transaksi</th><td><?= htmlspecialchars($data['kode_transaksi']) ?></td></tr>
                    <tr><th>Tanggal Simpanan</th><td><?= htmlspecialchars($data['tanggal']) ?></td></tr>
                    <tr><th>Jenis Simpanan</th><td><?= htmlspecialchars($data['jenis']) ?></td></tr>
                    <tr><th>Jumlah Simpanan</th><td>Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></td></tr>
                    <tr><th>Dibuat Oleh</th><td>penagih</td></tr>
                    <tr><th>Update Oleh</th><td>penagih</td></tr>
                </tbody>
            </table>
            <h6>Bukti Pembayaran</h6>
            <div class="card p-3">
                <?php if ($data['bukti_pembayaran_path']): ?>
                    <?php if (in_array(strtolower($buktiPembayaranExtension), ['jpg', 'jpeg', 'png'])): ?>
                        <a href="<?= htmlspecialchars($data['bukti_pembayaran_path']) ?>" target="_blank">
                            <img src="<?= htmlspecialchars($data['bukti_pembayaran_path']) ?>" alt="Bukti Pembayaran" class="img-fluid" style="max-width: 300px; height: auto;">
                        </a>
                    <?php elseif (strtolower($buktiPembayaranExtension) == 'pdf'): ?>
                        <p>Tautan menuju bukti pembayaran PDF:</p>
                        <a href="<?= htmlspecialchars($data['bukti_pembayaran_path']) ?>" target="_blank" class="btn btn-primary">Lihat Bukti Pembayaran</a>
                    <?php else: ?>
                        <p class="text-muted">Format file tidak didukung.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">Tidak ada bukti pembayaran</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mt-4">
        <a href="/simpanan/index.php" class="btn btn-light btn-lg">Kembali</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>