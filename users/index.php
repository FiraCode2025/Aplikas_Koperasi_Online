<?php
require __DIR__ . '/../db/koneksi.php';
require __DIR__ . '/../middleware/auth.php';
$res = $mysqli->query("SELECT * FROM users ORDER BY id DESC");
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Users</h4>
  <a href="/users/tambah.php" class="btn btn-primary btn-rounded"><i class="bi bi-plus"></i> Tambah</a>
</div>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th></th><th>Nama</th><th>Email</th><th>Password (hash SHA256)</th><th>Role</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php $no=1; while($r = $res->fetch_assoc()): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= esc($r['name']) ?></td><td><?= esc($r['email']) ?></td><td><?= esc($r['password']) ?></td><td><?= esc($r['role']) ?></td>
          <td width="160">
            <a class="btn btn-sm btn-outline-secondary" href="/users/edit.php?id=<?= $r['id'] ?>"><i class="bi bi-pencil"></i></a>
            <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')" href="/users/hapus.php?id=<?= $r['id'] ?>"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
