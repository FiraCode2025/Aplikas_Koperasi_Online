<?php
require __DIR__ . '/db/koneksi.php';
require __DIR__ . '/middleware/auth.php';

// Hitung data
$totAnggota   = $mysqli->query("SELECT COUNT(*) c FROM anggota")->fetch_assoc()['c'] ?? 0;
$totSimpanan  = $mysqli->query("SELECT IFNULL(SUM(jumlah),0) s FROM simpanan")->fetch_assoc()['s'] ?? 0;
$totPinjaman  = $mysqli->query("SELECT IFNULL(SUM(jumlah),0) s FROM pinjaman")->fetch_assoc()['s'] ?? 0;
$totPenarikan = $mysqli->query("SELECT IFNULL(SUM(jumlah),0) s FROM penarikan")->fetch_assoc()['s'] ?? 0;

// Info user
$user = $_SESSION['user'] ?? null;
$userName = $user['nama'] ?? 'Administrator';
$userRole = $user['role'] ?? 'Admin';
$userImage = $user['gambar'] ?? 'default.jpg';

// Path foto profil
$profileImagePath = __DIR__ . '/uploads/profiles/' . $userImage;
$imageSrc = file_exists($profileImagePath) && $userImage !== 'default.jpg'
    ? 'uploads/profiles/' . $userImage . '?v=' . time()
    : 'https://via.placeholder.com/80x80/3498db/ffffff?text=' . strtoupper(substr($userName,0,1));

// Data chart bulanan
$monthlyData = [];
$currentYear = date('Y');
for ($i=1; $i<=12; $i++) {
    $simpanan = $mysqli->query("SELECT IFNULL(SUM(jumlah),0) s FROM simpanan WHERE YEAR(tanggal)='$currentYear' AND MONTH(tanggal)='$i'")->fetch_assoc()['s'] ?? 0;
    $pinjaman = $mysqli->query("SELECT IFNULL(SUM(jumlah),0) s FROM pinjaman WHERE YEAR(tanggal)='$currentYear' AND MONTH(tanggal)='$i'")->fetch_assoc()['s'] ?? 0;
    $penarikan = $mysqli->query("SELECT IFNULL(SUM(jumlah),0) s FROM penarikan WHERE YEAR(tanggal)='$currentYear' AND MONTH(tanggal)='$i'")->fetch_assoc()['s'] ?? 0;
    $monthlyData[] = ['simpanan'=>$simpanan, 'pinjaman'=>$pinjaman, 'penarikan'=>$penarikan];
}

// Pie chart anggota
$activeMembers = $mysqli->query("SELECT COUNT(*) c FROM anggota WHERE status='aktif'")->fetch_assoc()['c'] ?? 0;
$nonActiveMembers = $totAnggota - $activeMembers;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Koperasi | Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background:#f6f8fb;
      margin:0;
      padding:0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
    }
    .sidebar {
      width:250px;
      position:fixed;
      top:0;
      bottom:0;
      left:0;
      background:#000000; /* HITAM MURNI */
      color:#fff;
      overflow-y:auto;
      display:flex;
      flex-direction:column;
      box-shadow: 2px 0 10px rgba(0,0,0,0.3);
    }
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar::-webkit-scrollbar-track {
      background: #222222; /* Abu-abu sangat gelap */
    }
    .sidebar::-webkit-scrollbar-thumb {
      background: #28a745;
      border-radius: 3px;
    }
    .sidebar a {
      color:#cbd5e1;
      text-decoration:none;
      display:block;
      padding:12px 18px;
      border-radius:8px;
      margin:4px 10px;
      transition:all 0.3s ease;
      position: relative;
    }
    .sidebar a:hover {
      background:#333333; /* Abu-abu gelap untuk hover */
      color:#fff;
      transform: translateX(5px);
    }
    .sidebar a.active {
      background:#333333; /* Abu-abu gelap sama seperti hover */
      color:#fff;
      box-shadow: none; /* Hapus shadow hijau */
    }
    .content {
      margin-left:250px;
      padding:24px;
      min-height:100vh;
    }
    .card {
      border:0;
      border-radius:18px;
      box-shadow:0 6px 18px rgba(0,0,0,.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      height: 100%;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow:0 12px 25px rgba(0,0,0,.1);
    }
    .profile-section {
      padding:20px;
      text-align:center;
      border-bottom:1px solid rgba(255,255,255,0.1);
      flex-shrink:0;
    }
    .profile-image {
      width:80px;
      height:80px;
      border-radius:50%;
      object-fit:cover;
      border:3px solid #444444; /* Border abu-abu gelap */
      transition: transform 0.3s ease;
    }
    .profile-image:hover {
      transform: scale(1.1);
    }
    .menu-section {
      flex-grow:1;
      padding:10px 0;
    }
    .logout-section {
      margin-top:auto;
      padding:15px;
      border-top:1px solid rgba(255,255,255,0.1);
      flex-shrink:0;
    }
    .logout-section a:hover {
      background: rgba(220, 53, 69, 0.1) !important;
      transform: translateX(5px);
    }
    .info-card-body {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        padding: 15px;
    }
    .info-card-body .small {
        margin-bottom: 5px;
    }
    .info-card-body .fs-4, .info-card-body .fs-5 {
        font-size: 1.5rem !important;
    }

    /* Chart styling */
    .chart-container {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        height: 300px;
    }
    .chart-container canvas {
        max-width: 100% !important;
        max-height: 280px !important;
    }
    
    .chart-row {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    
    .content {
        padding-bottom: 50px;
    }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="profile-section">
    <img src="<?= $imageSrc ?>" class="profile-image mb-3" alt="Profile"
          onerror="this.src='https://via.placeholder.com/80x80/3498db/ffffff?text=<?= strtoupper(substr($userName,0,1)) ?>'">
    <div>
      <strong class="d-block text-white"><?= htmlspecialchars($userName) ?></strong>
      <small class="text-muted"><?= htmlspecialchars($userRole) ?></small>
    </div>
  </div>

  <div class="menu-section">
    <div class="p-2">
      <a href="/index.php" class="<?= ($_SERVER['PHP_SELF']=='/index.php' || $_SERVER['PHP_SELF']=='\\index.php')?'active':'' ?>">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
      </a>
      <a href="/users/index.php">
        <i class="bi bi-people me-2"></i>User
      </a>
      <a href="/anggota/index.php">
        <i class="bi bi-person-bounding-box me-2"></i>Nasabah
      </a>
      <a href="/simpanan/index.php">
        <i class="bi bi-piggy-bank me-2"></i>Simpanan
      </a>
      <a href="/pinjaman/index.php">
        <i class="bi bi-cash-coin me-2"></i>Pinjaman
      </a>
      <a href="/penarikan/index.php">
        <i class="bi bi-wallet2 me-2"></i>Penarikan
      </a>
      <a href="/laporan/index.php">
        <i class="bi bi-file-earmark-text me-2"></i>Laporan
      </a>
      <a href="/roles/index.php">
        <i class="bi bi-shield-lock me-2"></i>Role
      </a>
      <a href="/profil.php">
        <i class="bi bi-person me-2"></i>Profil
      </a>
      <a href="/pengaturan.php">
        <i class="bi bi-gear me-2"></i>Pengaturan
      </a>
    </div>
  </div>

  <div class="logout-section">
    <a href="/logout.php" class="d-block text-danger">
      <i class="bi bi-box-arrow-right me-2"></i>Keluar
    </a>
  </div>
</div>

<div class="content">
  <h3 class="fw-bold mb-4">Selamat Datang, <?= htmlspecialchars($userName) ?></h3>

  <!-- Card Info -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body info-card-body">
          <div class="small text-muted">Anggota</div>
          <div class="fs-4 fw-bold"><?= number_format($totAnggota) ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body info-card-body">
          <div class="small text-muted">Simpanan</div>
          <div class="fs-5 fw-bold">Rp <?= number_format($totSimpanan,0,',','.') ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body info-card-body">
          <div class="small text-muted">Pinjaman</div>
          <div class="fs-5 fw-bold">Rp <?= number_format($totPinjaman,0,',','.') ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body info-card-body">
          <div class="small text-muted">Penarikan</div>
          <div class="fs-5 fw-bold">Rp <?= number_format($totPenarikan,0,',','.') ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart Section -->
  <div class="row g-3 chart-row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-header bg-transparent border-0 pb-0">
          <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Transaksi Koperasi</h6>
        </div>
        <div class="card-body chart-container">
          <canvas id="transactionChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card">
        <div class="card-header bg-transparent border-0 pb-0">
          <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Status Anggota</h6>
        </div>
        <div class="card-body chart-container">
          <canvas id="memberChart"></canvas>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart transaksi
new Chart(document.getElementById('transactionChart'), {
  type:'bar',
  data:{
    labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    datasets:[
      { label:'Simpanan', data:[<?= implode(',', array_column($monthlyData,'simpanan')) ?>], backgroundColor:'#17a2b8' },
      { label:'Pinjaman', data:[<?= implode(',', array_column($monthlyData,'pinjaman')) ?>], backgroundColor:'#007bff' },
      { label:'Penarikan', data:[<?= implode(',', array_column($monthlyData,'penarikan')) ?>], backgroundColor:'#6c757d' }
    ]
  },
  options:{ 
    responsive:true, 
    maintainAspectRatio: true, 
    aspectRatio: 2,
    plugins:{ legend:{ position:'top' } }, 
    scales:{ y:{ beginAtZero:true } } 
  }
});

// Chart anggota
new Chart(document.getElementById('memberChart'), {
  type:'doughnut',
  data:{
    labels:['Aktif','Non-Aktif'],
    datasets:[{ data:[<?= $activeMembers ?>, <?= $nonActiveMembers ?>], backgroundColor:['#28a745','#dc3545'] }]
  },
  options:{ 
    responsive:true, 
    maintainAspectRatio: true,
    aspectRatio: 1,
    plugins:{ legend:{ position:'bottom' } } 
  }
});
</script>
</body>
</html>