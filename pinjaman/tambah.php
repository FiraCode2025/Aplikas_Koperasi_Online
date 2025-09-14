<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $anggota_id = $_POST['anggota_id'];
    $jumlah = $_POST['jumlah'];
    $bunga_persen = $_POST['bunga_persen'];
    $lama_bulan = $_POST['lama_bulan'];
    $tanggal = $_POST['tanggal'];

    // Hitung tanggal jatuh tempo di sisi server
    $jatuh_tempo = date('Y-m-d', strtotime("+$lama_bulan months", strtotime($tanggal)));

    $status = 'diajukan'; // Status awal pinjaman adalah diajukan
    $created_by = $_SESSION['user']['username'];

    // Validasi data
    if (empty($anggota_id) || empty($jumlah) || empty($bunga_persen) || empty($lama_bulan) || empty($tanggal)) {
        $error = "Semua field harus diisi.";
    } else {
        // --- Solusi Masalah 2: Generate kode_pinjaman yang unik
        $kode_pinjaman = 'PJM-' . date('Ymd') . '-' . substr(md5(uniqid(rand(), true)), 0, 6);

        // Proses penyimpanan data ke database
        $stmt = $mysqli->prepare("INSERT INTO pinjaman (anggota_id, jumlah, bunga_persen, lama_bulan, tanggal, jatuh_tempo, status, created_by, kode_pinjaman) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ididsssss", $anggota_id, $jumlah, $bunga_persen, $lama_bulan, $tanggal, $jatuh_tempo, $status, $created_by, $kode_pinjaman);

        if ($stmt->execute()) {
            // Redirect ke halaman daftar pinjaman jika berhasil
            header("Location: /pinjaman/index.php");
            exit;
        } else {
            $error = "Gagal menambah data pinjaman: " . $stmt->error;
        }
    }
}

// Ambil data anggota untuk dropdown
$anggota_res = $mysqli->query("SELECT id, nama FROM anggota ORDER BY nama ASC");

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Tambah Pinjaman</h4>
    <a href="/pinjaman/index.php" class="btn btn-light btn-sm">Kembali</a>
</div>

<div class="card p-3">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="/pinjaman/tambah.php" method="POST">
        <div class="mb-3">
            <label for="anggota_id" class="form-label">Nasabah</label>
            <select class="form-select" id="anggota_id" name="anggota_id" required>
                <option value="">Pilih Nasabah</option>
                <?php while ($anggota = $anggota_res->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($anggota['id']) ?>"><?= htmlspecialchars($anggota['nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah Pinjaman (Rp.)</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
        </div>
        <div class="mb-3">
            <label for="bunga_persen" class="form-label">Bunga Pinjaman (%)</label>
            <input type="number" class="form-control" id="bunga_persen" name="bunga_persen" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Pinjam</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>
        <div class="mb-3">
            <label for="lama_bulan" class="form-label">Lama Pinjaman (Bulan)</label>
            <input type="number" class="form-control" id="lama_bulan" name="lama_bulan" required>
        </div>
        <div class="mb-3">
            <label for="jatuh_tempo" class="form-label">Jatuh Tempo</label>
            <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tanggalInput = document.getElementById('tanggal');
        const lamaBulanInput = document.getElementById('lama_bulan');
        const jatuhTempoInput = document.getElementById('jatuh_tempo');

        function hitungJatuhTempo() {
            const tanggal = tanggalInput.value;
            const lamaBulan = parseInt(lamaBulanInput.value);

            if (tanggal && !isNaN(lamaBulan) && lamaBulan > 0) {
                const date = new Date(tanggal);
                date.setMonth(date.getMonth() + lamaBulan);

                // Format tanggal ke YYYY-MM-DD
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                jatuhTempoInput.value = `${year}-${month}-${day}`;
            } else {
                jatuhTempoInput.value = '';
            }
        }

        tanggalInput.addEventListener('change', hitungJatuhTempo);
        lamaBulanInput.addEventListener('input', hitungJatuhTempo);
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>