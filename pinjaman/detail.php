<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$success = '';

// ==================== Proses penyimpanan angsuran ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pinjaman_id'])) {
    $pinjaman_id_post = $_POST['pinjaman_id'];
    $tanggal_angsuran = $_POST['tanggal_angsuran'];
    $jumlah_angsuran = (int)$_POST['jumlah_angsuran'];
    $jumlah_bunga = (int)$_POST['jumlah_bunga'];
    $jumlah_denda = (int)$_POST['jumlah_denda'];
    $total_angsuran = $jumlah_angsuran + $jumlah_bunga + $jumlah_denda;

    // ambil data pinjaman
    $stmt_pinjaman = $mysqli->prepare("SELECT * FROM pinjaman WHERE id = ?");
    $stmt_pinjaman->bind_param("i", $pinjaman_id_post);
    $stmt_pinjaman->execute();
    $pinjaman = $stmt_pinjaman->get_result()->fetch_assoc();

    if ($pinjaman) {
        // cek cicilan terakhir
        $stmt_last = $mysqli->prepare("SELECT * FROM angsuran WHERE pinjaman_id = ? ORDER BY cicilan_ke DESC LIMIT 1");
        $stmt_last->bind_param("i", $pinjaman_id_post);
        $stmt_last->execute();
        $last = $stmt_last->get_result()->fetch_assoc();

        $cicilan_ke = $last ? $last['cicilan_ke'] + 1 : 1;
        $sisa_hutang = $last ? $last['sisa_hutang'] - $jumlah_angsuran : $pinjaman['jumlah'] - $jumlah_angsuran;
        $status_angsuran = $sisa_hutang <= 0 ? 'Lunas' : 'Belum Lunas';

        // upload bukti
        $bukti_path = null;
        if (!empty($_FILES['bukti_pembayaran']['name'])) {
            $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
            $file_name = uniqid() . '_' . $_FILES['bukti_pembayaran']['name'];
            $upload_dir = __DIR__ . '/../uploads/bukti_pembayaran/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $bukti_path = '/uploads/bukti_pembayaran/' . $file_name;
            move_uploaded_file($file_tmp, $_SERVER['DOCUMENT_ROOT'] . $bukti_path);
        }

        $stmt_insert = $mysqli->prepare("INSERT INTO angsuran 
            (pinjaman_id, tanggal, pokok, bunga, denda, total, sisa_hutang, cicilan_ke, status, bukti_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param(
            "isiiiisiss",
            $pinjaman_id_post, $tanggal_angsuran, $jumlah_angsuran, $jumlah_bunga,
            $jumlah_denda, $total_angsuran, $sisa_hutang, $cicilan_ke,
            $status_angsuran, $bukti_path
        );

        if ($stmt_insert->execute()) {
            if ($status_angsuran == 'Lunas') {
                $stmt_update = $mysqli->prepare("UPDATE pinjaman SET status = 'lunas' WHERE id = ?");
                $stmt_update->bind_param("i", $pinjaman_id_post);
                $stmt_update->execute();
            }
            $success = "Angsuran berhasil dibayar!";
        } else {
            $error = "Gagal menyimpan angsuran: " . $mysqli->error;
        }
    }
}

// ==================== Ambil data pinjaman ====================
$stmt = $mysqli->prepare("SELECT p.*, a.nama as nama_anggota 
    FROM pinjaman p 
    JOIN anggota a ON p.anggota_id = a.id 
    WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) die("Pinjaman tidak ditemukan");

// ==================== Ambil daftar angsuran ====================
$angsuran_res = $mysqli->query("SELECT * FROM angsuran WHERE pinjaman_id = $id ORDER BY cicilan_ke ASC");

// ==================== Template ====================
include __DIR__ . '/../includes/header.php';
?>

<div class="card p-4">
    <h4>Detail Pinjaman</h4>

   <!-- Tombol aksi -->
<div class="mb-3">
    <a href="/pinjaman/status.php?id=<?= $data['id'] ?>&status=disetujui" class="btn btn-success me-2">Terima</a>
    <a href="/pinjaman/status.php?id=<?= $data['id'] ?>&status=ditolak" class="btn btn-danger">Tolak Pengajuan</a>
</div>

    <!-- Notifikasi -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Info pinjaman -->
    <div class="row mb-3">
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr><th>Kode</th><td><?= $data['kode_pinjaman'] ?></td></tr>
                <tr><th>Nama</th><td><?= $data['nama_anggota'] ?></td></tr>
                <tr><th>Tgl Pinjam</th><td><?= $data['tanggal'] ?></td></tr>
                <tr><th>Jatuh Tempo</th><td><?= $data['jatuh_tempo'] ?></td></tr>
                <tr><th>Jumlah</th><td>Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr><th>Lama</th><td><?= $data['lama_bulan'] ?> bulan</td></tr>
                <tr><th>Bunga</th><td><?= $data['bunga_persen'] ?> %</td></tr>
                <tr><th>Status</th><td><?= ucfirst($data['status']) ?></td></tr>
                <tr><th>Dibuat Oleh</th><td><?= $data['nama_anggota'] ?></td></tr>
            </table>
        </div>
    </div>

    <hr>

    <!-- Form Angsuran -->
    <h5>Bayar Angsuran</h5>
    <div class="card card-body mb-3">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="pinjaman_id" value="<?= $data['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Tanggal Bayar</label>
                <input type="date" class="form-control" id="tanggalBayar" name="tanggal_angsuran" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Angsuran</label>
                <input type="number" class="form-control" name="jumlah_angsuran" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Bunga Angsuran</label>
                <input type="number" class="form-control" name="jumlah_bunga" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Denda/Hari x 1%</label>
                <input type="number" class="form-control" name="jumlah_denda" value="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Bukti Pembayaran</label>
                <input type="file" class="form-control" name="bukti_pembayaran">
            </div>
            <button type="submit" class="btn btn-success">Konfirmasi Bayar</button>
        </form>
    </div>

    <script>
    // otomatis set tanggal hari ini
    const tanggalBayarInput = document.getElementById('tanggalBayar');
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    tanggalBayarInput.value = `${year}-${month}-${day}`;
    </script>

    <hr>

    <!-- Tabel Angsuran -->
    <h5>Daftar Angsuran</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Pokok</th>
                    <th>Bunga</th>
                    <th>Denda</th>
                    <th>Total</th>
                    <th>Cicilan</th>
                    <th>Status</th>
                    <th>Bukti</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($angsuran_res->num_rows > 0): ?>
                    <?php while($a = $angsuran_res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $a['tanggal'] ?></td>
                        <td>Rp <?= number_format($a['pokok'],0,',','.') ?></td>
                        <td>Rp <?= number_format($a['bunga'],0,',','.') ?></td>
                        <td>Rp <?= number_format($a['denda'],0,',','.') ?></td>
                        <td>Rp <?= number_format($a['total'],0,',','.') ?></td>
                        <td><?= $a['cicilan_ke'] ?></td>
                        <td><?= $a['status'] ?></td>
                        <td>
                            <?php if ($a['bukti_path']): ?>
                                <a href="<?= htmlspecialchars($a['bukti_path']) ?>" target="_blank" class="btn btn-sm btn-primary">Lihat</a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_angsuran.php?id=<?= (int)$a['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="hapus_angsuran.php?id=<?= (int)$a['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin hapus angsuran ini?')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center">Belum ada angsuran</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
