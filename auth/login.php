<?php
require __DIR__ . '/../db/koneksi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $email = $_POST['email'] ?? '';
  $pass  = $_POST['password'] ?? '';
  $stmt = $mysqli->prepare("SELECT id, name, email, role FROM users WHERE email=? AND password=? LIMIT 1");
  $stmt->bind_param('ss', $email, $pass);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  if ($res){
    session_start();
    $_SESSION['user'] = $res;
    header("Location: /index.php");
    exit;
  }else{
    $error = 'Email/password salah';
  }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - Koperasi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow rounded-4">
          <div class="card-body p-4 text-center">

           <img src="/logo-kiri.jpg" 
           alt="Foto Profil" 
           class="rounded-circle mb-3 shadow-sm border"
           style="width:120px; height:120px; object-fit:cover;">
            <h4 class="mb-3">Login</h4>

            <?php if($error): ?>
              <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="text-start">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
              </div>
              <button class="btn btn-dark w-100">Masuk</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
