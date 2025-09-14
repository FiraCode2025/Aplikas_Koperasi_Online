<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anggota_id = $_POST['anggota_id'] ?? null;
    $tanggal = $_POST['tanggal'] ?? null;
    $jenis = $_POST['jenis'] ?? null;
    $jumlah = $_POST['jumlah'] ?? null;
    $bukti_pembayaran_path = null;

    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../img/simpanan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($_FILES['bukti_pembayaran']['name']);
        $extension = strtolower($fileInfo['extension']);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($extension, $allowedExtensions)) {
            $fileName = uniqid('bukti_') . '.' . $extension;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $targetFile)) {
                $bukti_pembayaran_path = '/img/simpanan/' . $fileName;
            }
        }
    }
    
    $last_id = $mysqli->query("SELECT id FROM simpanan ORDER BY id DESC LIMIT 1")->fetch_assoc()['id'] ?? 0;
    $new_id = $last_id + 1;
    $kode_transaksi = 'SMP-' . str_pad($new_id, 4, '0', STR_PAD_LEFT);

    $stmt = $mysqli->prepare("INSERT INTO simpanan (id, kode_transaksi, anggota_id, tanggal, jenis, jumlah, bukti_pembayaran_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $new_id, $kode_transaksi, $anggota_id, $tanggal, $jenis, $jumlah, $bukti_pembayaran_path);

    $ok = $stmt->execute();

    if ($ok) {
        $update_status = $mysqli->prepare("UPDATE anggota SET status = 'aktif' WHERE id = ?");
        $update_status->bind_param("i", $anggota_id);
        $update_status->execute();
        
        $msg = 'Transaksi simpanan berhasil disimpan dan status anggota diperbarui';
        header("Location: index.php?msg=" . urlencode($msg));
        exit();
    } else {
        $msg = 'Gagal menyimpan transaksi: ' . $mysqli->error;
    }
}
$anggota_list = $mysqli->query("SELECT id, nama FROM anggota ORDER BY nama ASC");
include __DIR__ . '/../includes/header.php';
?>
<h4>Tambah Transaksi Simpanan</h4>
<?php if ($msg): ?>
<div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<div class="card p-3">
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nama Anggota</label>
            <select name="anggota_id" class="form-control" required>
                <option value="">Pilih Anggota</option>
                <?php while($anggota = $anggota_list->fetch_assoc()): ?>
                <option value="<?= $anggota['id'] ?>"><?= htmlspecialchars($anggota['nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jenis Simpanan</label>
            <select name="jenis" class="form-control" required>
                <option value="pokok">Pokok</option>
                <option value="wajib">Wajib</option>
                <option value="sukarela">Sukarela</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah (Rp)</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Bukti Pembayaran</label>
            <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*, .pdf" onchange="previewFile(this)">
            
            <!-- Preview Area -->
            <div id="filePreview" class="mt-3" style="display: none;">
                <div class="border rounded p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>Preview:</strong>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                    <div id="imagePreview" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" class="img-fluid border rounded" style="max-width: 300px; height: auto;">
                    </div>
                    <div id="pdfPreview" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf text-danger fs-1 me-3"></i>
                            <div>
                                <div class="fw-bold" id="pdfFileName"></div>
                                <small class="text-muted" id="pdfFileSize"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-3 mt-4">
            <a href="/simpanan/index.php" class="btn btn-light btn-lg">Kembali</a>
            <button class="btn btn-primary btn-lg">Simpan</button>
        </div>
    </form>
</div>

<script>
function previewFile(input) {
    const file = input.files[0];
    const previewContainer = document.getElementById('filePreview');
    const imagePreview = document.getElementById('imagePreview');
    const pdfPreview = document.getElementById('pdfPreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        const fileType = file.type;
        const fileName = file.name;
        const fileSize = formatFileSize(file.size);
        
        // Reset previews
        imagePreview.style.display = 'none';
        pdfPreview.style.display = 'none';
        
        if (fileType.startsWith('image/')) {
            // Preview gambar
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else if (fileType === 'application/pdf') {
            // Preview PDF
            document.getElementById('pdfFileName').textContent = fileName;
            document.getElementById('pdfFileSize').textContent = fileSize;
            pdfPreview.style.display = 'block';
            previewContainer.style.display = 'block';
        }
    } else {
        previewContainer.style.display = 'none';
    }
}

function removeFile() {
    const fileInput = document.querySelector('input[name="bukti_pembayaran"]');
    const previewContainer = document.getElementById('filePreview');
    
    fileInput.value = '';
    previewContainer.style.display = 'none';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>