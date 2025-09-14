<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
include __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Simpanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="card shadow-sm p-3">
    <div class="d-flex justify-content-between mb-3">
      <h3>Laporan Simpanan</h3>
      <a href="cetak_laporan_simpanan.php" target="_blank" class="btn btn-danger">
        <i class="bi bi-printer"></i> Cetak
      </a>
    </div>

    <table class="table table-bordered table-striped align-middle">
      <thead class="bg-secondary text-white">
        <tr>
          <th>No</th>
          <th>Kode Transaksi</th>
          <th>Tanggal</th>
          <th>Nasabah</th>
          <th>Jenis</th>
          <th>Jumlah Simpanan</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $sql = "SELECT s.kode_transaksi, s.tanggal, a.nama AS nasabah, 
                       s.jenis, s.jumlah
                FROM simpanan s
                JOIN anggota a ON s.anggota_id = a.id
                ORDER BY s.tanggal DESC
                LIMIT 2";
        $result = $mysqli->query($sql);

        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['kode_transaksi'] ?></td>
          <td><?= $row['tanggal'] ?></td>
          <td><?= $row['nasabah'] ?></td>
          <td><?= ucfirst($row['jenis']) ?></td>
          <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
