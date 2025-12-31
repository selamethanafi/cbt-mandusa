<?php
include '../inc/config.php';
if(isset($_SESSION['id_siswa'])){
    header("Location: daftar_ujian.php");
    exit;
}

$error = '';

// Proses login
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $nis = $_POST['nis'];
  $pass = $_POST['password'];

  $q = $db->prepare("SELECT * FROM siswa WHERE nis=?");
  $q->bind_param("s",$nis);
  $q->execute();
  $r = $q->get_result()->fetch_assoc();
	// buat ID login unik
	$login_token = session_id();

// cek apakah sudah login di tempat lain
$cek = $db->query("
SELECT session_id 
FROM siswa 
WHERE id={$r['id']}
")->fetch_assoc();

if(!empty($cek['session_id']) && $cek['session_id'] != $login_token){
    exit('Akun ini sedang digunakan di komputer lain');
}

// simpan session login
$db->query("
UPDATE siswa 
SET session_id='$login_token'
WHERE id={$r['id']}
");
  if($r && password_verify($pass,$r['password'])){
    $_SESSION['id_siswa'] = $r['id'];
    $_SESSION['kelas'] = $r['kelas'];
    header("Location: daftar_ujian.php");
    exit;
  }
  $error="Login gagal";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Login CBT</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body">
<h5 class="card-title text-center mb-3">Login CBT</h5>

<?php if($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
<div class="mb-3">
<input type="text" name="nis" class="form-control" placeholder="Username" required>
</div>
<div class="mb-3">
<input type="password" name="password" class="form-control" placeholder="Password" required>
</div>
<button class="btn btn-primary w-100">Login</button>
</form>

</div>
</div>
</div>
</div>
</div>
</body>
</html>

