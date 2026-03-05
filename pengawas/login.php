<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
if(isset($_SESSION['username'])){
    header("Location: menu.php");
    exit;
}
$error = '';

if($_POST){
  $nis = $_POST['nis'];
  $pass = $_POST['password'];

  $q = $db->prepare("SELECT * FROM pengawas WHERE username=?");
  $q->bind_param("s",$nis);
  $q->execute();
  $r = $q->get_result()->fetch_assoc();

  if($r && password_verify($pass,$r['password'])){
    $_SESSION['username'] = $r['id'];
    $_SESSION['role'] = 'pengawas';
    header("Location: menu.php");
    exit;
  }
  $error="Login gagal";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login CBT</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body">
<h3 class="card-title text-center mb-3">Login CBT <?= $sek_nama;?><br />Ruang <?= $ruang;?></h5>

<?php if($error): ?>
<div class="alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post">
<div class="mb-3">
<input type="text" name="nis" class="form-control" placeholder="Username"  required>
</div>
<div class="mb-3">
<input type="password" name="password" class="form-control" placeholder="Password" required>
</div>

<button>LOGIN</button>

</form>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
