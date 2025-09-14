<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$id = (int)($_GET['id'] ?? 0);
$data = $mysqli->query("SELECT * FROM anggota WHERE id={$id}")->fetch_assoc();
if(!$data){ die('Data tidak ditemukan'); }

$nama = $data['nama'] ?? '';
$no_ktp = $data['no_ktp'] ?? '';
$alamat = $data['alamat'] ?? '';
$status = $data['status'] ?? '';
$tanggal_lahir = $data['tanggal_lahir'] ?? '';
$pekerjaan = $data['pekerjaan'] ?? '';
$email = $data['email'] ?? '';
$tanggal_gabung = $data['tanggal_gabung'] ?? '';
$password = $data['password'] ?? '';
$agama = $data['agama'] ?? '';
$jenis_kelamin = $data['jenis_kelamin'] ?? '';
$telepon = $data['telepon'] ?? '';

$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nama = $_POST['nama'] ?? null;
    $no_ktp = $_POST['no_ktp'] ?? null;
    $alamat = $_POST['alamat'] ?? null;
    $status = $_POST['status'] ?? null;
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $pekerjaan = $_POST['pekerjaan'] ?? null;
    $email = $_POST['email'] ?? null;
    $tanggal_gabung = $_POST['tanggal_gabung'] ?? null;
    $password = $_POST['password'] ?? null;
    $agama = $_POST['agama'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $telepon = $_POST['telepon'] ?? null;

    $stmt = $mysqli->prepare("UPDATE anggota SET nama = ?, no_ktp = ?, alamat = ?, status = ?, tanggal_lahir = ?, pekerjaan = ?, email = ?, tanggal_gabung = ?, password = ?, agama = ?, jenis_kelamin = ?, telepon = ? WHERE id=?");
    $stmt->bind_param("sssssssssssss", $nama, $no_ktp, $alamat, $status, $tanggal_lahir, $pekerjaan, $email, $tanggal_gabung, $password, $agama, $jenis_kelamin, $telepon, $id);
    
    $ok=$stmt->execute();
    if($ok) $msg='Berhasil diupdate'; else $msg='Gagal update';
}

include __DIR__ . '/../includes/header.php';
?>
<h4>Edit Nasabah</h4>
<?php if($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="card p-3">
    <form method="post">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Nama</label>
                <input name="nama" class="form-control" value="<?= htmlspecialchars($nama) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($tanggal_lahir) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Pekerjaan</label>
                <input name="pekerjaan" class="form-control" value="<?= htmlspecialchars($pekerjaan) ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($alamat) ?></textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Gabung</label>
                <input type="date" name="tanggal_gabung" class="form-control" value="<?= htmlspecialchars($tanggal_gabung) ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" value="<?= htmlspecialchars($password) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Agama</label>
                <select name="agama" class="form-control" required>
                    <option value="">Pilih Agama</option>
                    <option value="Islam" <?= ($agama == 'Islam') ? 'selected' : '' ?>>Islam</option>
                    <option value="Kristen" <?= ($agama == 'Kristen') ? 'selected' : '' ?>>Kristen</option>
                    <option value="Katolik" <?= ($agama == 'Katolik') ? 'selected' : '' ?>>Katolik</option>
                    <option value="Hindu" <?= ($agama == 'Hindu') ? 'selected' : '' ?>>Hindu</option>
                    <option value="Buddha" <?= ($agama == 'Buddha') ? 'selected' : '' ?>>Buddha</option>
                    <option value="Konghucu" <?= ($agama == 'Konghucu') ? 'selected' : '' ?>>Konghucu</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki" <?= ($jenis_kelamin == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= ($jenis_kelamin == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">No KTP</label>
                <input name="no_ktp" class="form-control" value="<?= htmlspecialchars($no_ktp) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Telepon</label>
                <input name="telepon" class="form-control" value="<?= htmlspecialchars($telepon) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Status (aktif/non-aktif)</label>
                <input name="status" class="form-control" value="<?= htmlspecialchars($status) ?>" required>
            </div>
        </div>
        <button class="btn btn-primary btn-rounded">Update</button>
        <a href="/anggota/index.php" class="btn btn-light">Kembali</a>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>