<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
if(isset($_SESSION['username'])){
    header("Location: menu.php");
    exit;
}
$error = '';

if($_POST){
  // ambil input
$username = $_POST['nis'];
$password = $_POST['password'];

// kirim ke SIM
//url_bank_soal
$url = $sianis."/api/login";
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='app_key_ekinerja'");
$da = mysqli_fetch_assoc($ta);
$key_guru = $da['konfigurasi_isi'] ?? '';

$remote_key = $key_guru;
$data = [
    'username' => $username,
    'password' => $password,
    'remote_key' => $remote_key
];

// pakai cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
curl_close($ch);
$result = json_decode($response, true);
// cek hasil
if ($result['tanda'] == 'PA') {

    $_SESSION['nip']  = $result['username'];
    $_SESSION['nama'] = $result['nama'];
    $_SESSION['tanda'] = $result['tanda'];
    
    header("Location: menu.php");

} else {
    header("Location: login.php?error=".$url);;
}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login Guru CBT</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body">
<h3 class="card-title text-center mb-3">Login Guru CBT <?= $sek_nama;?><br />Ruang <?= $ruang;?></h5>

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
