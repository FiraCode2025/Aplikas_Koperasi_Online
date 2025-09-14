<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
include __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Pinjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="card shadow-sm p-3">
    <div class="d-flex justify-content-between mb-3">
      <h3>Laporan Pinjaman</h3>
      <a href="cetak_laporan_pinjaman.php" target="_blank" class="btn btn-danger">
        <i class="bi bi-printer"></i> Cetak
      </a>
    </div>

    <table class="table table-bordered table-striped align-middle">
      <thead class="bg-secondary text-white">
        <tr>
          <th>No</th>
          <th>Kode Pinjaman</th>
          <th>Tanggal</th>
          <th>Nasabah</th>
          <th>Jumlah Pinjaman</th>
          <th>Status</th>
          <th>Sisa Hutang</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $sql = "SELECT p.id, p.kode_pinjaman, p.tanggal, a.nama AS nasabah, 
                       p.jumlah, p.status
                FROM pinjaman p
                JOIN anggota a ON p.anggota_id = a.id
                ORDER BY p.tanggal DESC
                LIMIT 2";
        $result = $mysqli->query($sql);

        while ($row = $result->fetch_assoc()):
            $q_hutang = $mysqli->query("
                SELECT (p.jumlah - IFNULL(SUM(angs.total),0)) AS sisa
                FROM pinjaman p
                LEFT JOIN angsuran angs ON p.id = angs.pinjaman_id
                WHERE p.id = {$row['id']}
                GROUP BY p.jumlah
            ");
            $sisa = $q_hutang->fetch_assoc()['sisa'] ?? $row['jumlah'];
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['kode_pinjaman'] ?></td>
          <td><?= $row['tanggal'] ?></td>
          <td><?= $row['nasabah'] ?></td>
          <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
          <td><?= ucfirst($row['status']) ?></td>
          <td>Rp <?= number_format($sisa, 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
