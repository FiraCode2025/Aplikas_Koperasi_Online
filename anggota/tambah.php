<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect all data from the form
    $nama = $_POST['nama'] ?? null;
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $pekerjaan = $_POST['pekerjaan'] ?? null;
    $email = $_POST['email'] ?? null;
    $alamat = $_POST['alamat'] ?? null;
    $tanggal_gabung = $_POST['tanggal_gabung'] ?? null;
    $password = $_POST['password'] ?? null;
    $no_ktp = $_POST['no_ktp'] ?? null;
    $agama = $_POST['agama'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $telepon = $_POST['telepon'] ?? null;
    
    // Status diatur secara default sebagai 'aktif'
    $status = 'aktif';

    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../img/anggota/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileInfo = pathinfo($_FILES['foto']['name']);
        $extension = strtolower($fileInfo['extension']);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extension, $allowedExtensions)) {
            $fileName = uniqid('img_') . '.' . $extension;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
                $foto_path = '/img/anggota/' . $fileName;
            }
        }
    }

    // Check if no_ktp already exists to prevent duplicates
    $check_stmt = $mysqli->prepare("SELECT id FROM anggota WHERE no_ktp = ?");
    $check_stmt->bind_param("s", $no_ktp);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $msg = 'Gagal menyimpan: No KTP sudah terdaftar.';
    } else {
        // Prepare and execute the SQL query to insert new data
        $stmt = $mysqli->prepare("INSERT INTO anggota (nama, tanggal_lahir, pekerjaan, email, alamat, tanggal_gabung, password, no_ktp, agama, jenis_kelamin, telepon, status, foto_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $nama, $tanggal_lahir, $pekerjaan, $email, $alamat, $tanggal_gabung, $password, $no_ktp, $agama, $jenis_kelamin, $telepon, $status, $foto_path);

        $ok = $stmt->execute();

        if ($ok) {
            $msg = 'Berhasil disimpan';
            header("Location: index.php?msg=" . urlencode($msg));
            exit();
        } else {
            $msg = 'Gagal menyimpan: ' . $mysqli->error;
        }
    }
}
include __DIR__ . '/../includes/header.php';
?>

<h4>Tambah Nasabah</h4>

<?php if ($msg): ?>
<div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card p-3">
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Nama</label>
                <input name="nama" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Pekerjaan</label>
                <input name="pekerjaan" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-5">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" required></textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Gabung</label>
                <input type="date" name="tanggal_gabung" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Masukkan Foto</label>
                <div class="d-flex align-items-center">
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                    <img id="previewFoto" src="#" alt="Pratinjau Foto" class="ms-3 rounded" style="width: 100px; height: 100px; object-fit: cover; display: none;">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Agama</label>
                <select name="agama" class="form-control" required>
                    <option value="">Pilih Agama</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">No KTP</label>
                <input name="no_ktp" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Telepon</label>
                <input name="telepon" class="form-control" required>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-3 mt-4">
            <a href="/anggota/index.php" class="btn btn-light btn-lg">Kembali</a>
            <button class="btn btn-primary btn-lg">Simpan</button>
        </div>
    </form>
</div>

<script>
    const fotoInput = document.getElementById('foto');
    const previewFoto = document.getElementById('previewFoto');

    fotoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewFoto.src = e.target.result;
                previewFoto.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewFoto.src = '#';
            previewFoto.style.display = 'none';
        }
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>