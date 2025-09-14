<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
include __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penarikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="card shadow-sm p-3">
    <div class="d-flex justify-content-between mb-3">
      <h3>Laporan Penarikan</h3>
      <a href="cetak_penarikan.php" target="_blank" class="btn btn-danger">
        <i class="bi bi-printer"></i> Cetak
      </a>
    </div>

    <table class="table table-bordered table-striped align-middle">
      <thead class="bg-secondary text-white">
        <tr>
          <th>No</th>
          <th>Kode Tarik</th>
          <th>Tanggal</th>
          <th>Nasabah</th>
          <th>Jumlah Penarikan</th>
          <th>Sisa Saldo</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;

        // Ambil data penarikan
        $sql = "SELECT p.id, p.kode_tarik, p.tanggal, a.nama AS nasabah, p.jumlah, p.keterangan, p.anggota_id
                FROM penarikan p
                JOIN anggota a ON p.anggota_id = a.id
                ORDER BY p.tanggal DESC
                LIMIT 2";
        $result = $mysqli->query($sql);

        while ($row = $result->fetch_assoc()):
            // ===== Trik: buat kode tarik otomatis jika kosong =====
            if(empty($row['kode_tarik'])){
                $row['kode_tarik'] = 'T'.str_pad($row['id'], 4, '0', STR_PAD_LEFT);
                // Update di database agar permanen
                $mysqli->query("UPDATE penarikan SET kode_tarik='{$row['kode_tarik']}' WHERE id={$row['id']}");
            }

            // Hitung saldo
            $q_saldo = $mysqli->query("
                SELECT 
                  (SELECT IFNULL(SUM(jumlah),0) FROM simpanan WHERE anggota_id = {$row['anggota_id']}) -
                  (SELECT IFNULL(SUM(jumlah),0) FROM penarikan WHERE anggota_id = {$row['anggota_id']})
                  AS saldo
            ");
            $saldo = $q_saldo->fetch_assoc()['saldo'];
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['kode_tarik'] ?></td>
          <td><?= $row['tanggal'] ?></td>
          <td><?= $row['nasabah'] ?></td>
          <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
          <td>Rp <?= number_format($saldo, 0, ',', '.') ?></td>
          <td><?= $row['keterangan'] ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
