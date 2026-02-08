<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
if(isset($_SESSION['id_siswa'])){
    header("Location: daftar_ujian.php");
    exit;
}
if(isset($_GET['nopes']))
{
	$get_nopes = $_GET['nopes'];
}
else
{
$get_kode = '';
}
if(isset($_GET['kode']))
{
	$get_kode = $_GET['kode'];
}
else
{
$get_nopes = '';
}

$error = '';

// Proses login
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
  $nis = $_POST['nis'];
  $pass = $_POST['password'];
  $q = $db->prepare("SELECT * FROM siswa WHERE username=? AND password=?");
  $q->bind_param("ss", $nis, $pass);
  $q->execute();

  $result = $q->get_result();

if($result->num_rows > 0){
    $r = $result->fetch_assoc();
    $login_token = session_id();
// cek apakah sudah login di tempat lain
	$cek = $db->query("SELECT session_token  FROM siswa WHERE id_siswa={$r['id_siswa']} ")->fetch_assoc();
	if(!empty($cek['session_token']) && $cek['session_token'] != $login_token){
	    exit('Akun ini sedang digunakan di perangkat lain <a href="kirim_permintaan_reset.php?nopes='.$nis.'&kode='.$pass.'&reset=perangkat">Kirim permintaan Reset</a>');
	}
	// simpan session login
	$db->query("UPDATE siswa SET session_token='$login_token' WHERE id_siswa={$r['id_siswa']}");
	$_SESSION['id_siswa'] = $r['id_siswa'];
	$_SESSION['kelas'] = $r['kelas'];
	header("Location: daftar_ujian.php");
        exit;
    
}
else
{
  $error="Login gagal";
}
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
<h3 class="card-title text-center mb-3">Login CBT <?= $sek_nama;?></h5>

<?php if($error): ?>
<div class="alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
<div class="mb-3">
<input type="text" name="nis" class="form-control" placeholder="Username" value="<?= $get_nopes;?>" required>
</div>
<div class="mb-3">
<input type="password" name="password" class="form-control" placeholder="Password" value="<?= $get_kode;?>" required>
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

