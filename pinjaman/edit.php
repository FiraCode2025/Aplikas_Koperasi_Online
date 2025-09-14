<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

// Pastikan session sudah aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil ID pinjaman dari GET
$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    die('ID pinjaman tidak valid.');
}

// Ambil nama user yang login
$updated_by = $_SESSION['user']['name'] ?? 'unknown';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form dan cast INT untuk angka
    $anggota_id   = (int)($_POST['anggota_id'] ?? 0);
    $jumlah       = (int)($_POST['jumlah'] ?? 0);
    $bunga_persen = (int)($_POST['bunga_persen'] ?? 0);
    $lama_bulan   = (int)($_POST['lama_bulan'] ?? 0);
    $tanggal      = $_POST['tanggal'] ?? '';
    $jatuh_tempo  = $_POST['jatuh_tempo'] ?? '';
    $status       = $_POST['status'] ?? '';

    // Validasi
    if (empty($anggota_id) || empty($jumlah) || empty($bunga_persen) || empty($lama_bulan) || empty($tanggal) || empty($jatuh_tempo) || empty($status)) {
        $error = "Semua field harus diisi.";
    } else {
        // Update data pinjaman
        $stmt = $mysqli->prepare("
            UPDATE pinjaman
            SET anggota_id=?, jumlah=?, bunga_persen=?, lama_bulan=?, tanggal=?, jatuh_tempo=?, status=?, updated_by=?
            WHERE id=?
        ");
        if ($stmt === false) {
            die("Prepare failed: " . $mysqli->error);
        }

        $stmt->bind_param(
            "iiiissssi",  // Fixed: 9 type specifiers to match 9 parameters
            $anggota_id,
            $jumlah,
            $bunga_persen,
            $lama_bulan,
            $tanggal,
            $jatuh_tempo,
            $status,
            $updated_by,
            $id
        );

        if ($stmt->execute()) {
            header("Location: /pinjaman/detail.php?id=$id");
            exit;
        } else {
            $error = "Gagal update pinjaman: " . $stmt->error;
        }
    }
}

// Ambil data pinjaman untuk form
$stmt = $mysqli->prepare("SELECT * FROM pinjaman WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) die('Data pinjaman tidak ditemukan.');

// Ambil data anggota untuk dropdown
$anggota_res = $mysqli->query("SELECT id, nama FROM anggota ORDER BY nama ASC");

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Edit Pinjaman</h4>
    <a href="/pinjaman/index.php" class="btn btn-light btn-sm">Kembali</a>
</div>

<div class="card p-3">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="/pinjaman/edit.php?id=<?= $id ?>" method="POST">
        <div class="mb-3">
            <label for="anggota_id" class="form-label">Nasabah</label>
            <select class="form-select" id="anggota_id" name="anggota_id" required>
                <option value="">Pilih Nasabah</option>
                <?php while ($anggota = $anggota_res->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($anggota['id']) ?>" <?= ($anggota['id'] == $data['anggota_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($anggota['nama']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah Pinjaman (Rp.)</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= htmlspecialchars($data['jumlah']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="bunga_persen" class="form-label">Bunga Pinjaman (%)</label>
            <input type="number" class="form-control" id="bunga_persen" name="bunga_persen" step="0.01" value="<?= htmlspecialchars($data['bunga_persen']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="lama_bulan" class="form-label">Lama Pinjaman (Bulan)</label>
            <input type="number" class="form-control" id="lama_bulan" name="lama_bulan" value="<?= htmlspecialchars($data['lama_bulan']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Pinjam</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($data['tanggal']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="jatuh_tempo" class="form-label">Tanggal Jatuh Tempo</label>
            <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" value="<?= htmlspecialchars($data['jatuh_tempo']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="diajukan" <?= ($data['status'] == 'diajukan') ? 'selected' : '' ?>>Diajukan</option>
                <option value="disetujui" <?= ($data['status'] == 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                <option value="ditolak" <?= ($data['status'] == 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                <option value="lunas" <?= ($data['status'] == 'lunas') ? 'selected' : '' ?>>Lunas</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>