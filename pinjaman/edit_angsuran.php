<?php
require __DIR__ . '/../db/koneksi.php';

$id = (int)($_GET['id'] ?? 0);

// Ambil data lama
$stmt = $mysqli->prepare("SELECT * FROM angsuran WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Data angsuran tidak ditemukan");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pokok  = (int)$_POST['pokok'];
    $bunga  = (int)$_POST['bunga'];
    $denda  = (int)$_POST['denda'];
    $status = $_POST['status'];

    $total = $pokok + $bunga + $denda;

    // Upload file baru (opsional)
    $bukti_path = $data['bukti_path'];
    if (!empty($_FILES['bukti']['name'])) {
        $file_tmp  = $_FILES['bukti']['tmp_name'];
        $file_name = uniqid() . '_' . $_FILES['bukti']['name'];
        $upload_dir = __DIR__ . '/../uploads/bukti_pembayaran/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $bukti_path = '/uploads/bukti_pembayaran/' . $file_name;
        move_uploaded_file($file_tmp, $_SERVER['DOCUMENT_ROOT'] . $bukti_path);
    }

    $stmt_upd = $mysqli->prepare("UPDATE angsuran 
        SET pokok=?, bunga=?, denda=?, total=?, status=?, bukti_path=? 
        WHERE id=?");
    $stmt_upd->bind_param("iiiissi", $pokok, $bunga, $denda, $total, $status, $bukti_path, $id);

    if ($stmt_upd->execute()) {
        // redirect ke detail pinjaman
        header("Location: detail.php?id=" . $data['pinjaman_id']);
        exit;
    } else {
        echo "Gagal update: " . $mysqli->error;
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="card p-4">
    <h4>Edit Angsuran</h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Pokok</label>
            <input type="number" class="form-control" name="pokok" value="<?= $data['pokok'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Bunga</label>
            <input type="number" class="form-control" name="bunga" value="<?= $data['bunga'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Denda</label>
            <input type="number" class="form-control" name="denda" value="<?= $data['denda'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select class="form-control" name="status">
                <option <?= $data['status']=='Belum Lunas'?'selected':'' ?>>Belum Lunas</option>
                <option <?= $data['status']=='Lunas'?'selected':'' ?>>Lunas</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Bukti Pembayaran (opsional)</label><br>
            <?php if ($data['bukti_path']): ?>
                <a href="<?= $data['bukti_path'] ?>" target="_blank">Lihat Bukti Lama</a><br>
            <?php endif; ?>
            <input type="file" class="form-control" name="bukti">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="detail.php?id=<?= $data['pinjaman_id'] ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>
