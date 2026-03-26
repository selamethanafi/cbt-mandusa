<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

include '../inc/config.php';
if(!isset($_SESSION['id_siswa'], $_SESSION['kelas'])){
    header("Location: login.php");
    exit;
}
$id = (int)$_GET['id'];
$u = $db->query("SELECT * FROM ujian_aktif WHERE id_ujian=$id")->fetch_assoc();

if(!$u) exit('Ujian tidak ditemukan');

if($_SERVER['REQUEST_METHOD']=='POST'){
    $token = strtoupper(trim($_POST['token']));

    if($token == $u['token']){
        $_SESSION['ujian_id'] = $u['id_ujian'];
        header("Location: ujian.php");
        exit;
    } else {
        $err = "Token salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>CBT Ujian</title>
<link rel="stylesheet" href="../css/style.css?<?= date("Y-m-dH:I:s");?>">
</head>
<body>
<div class="container-fluid">
<div class="card shadow-sm">
<div class="card-body">

<h4>Nama Tes <?= $u['nama_soal'] ?></h4>
<?php if(isset($err)) echo '<div class="alert-danger">'.$err.'</div>'; ?>
<form method="post">
<input class="form-control mb-2" name="token" placeholder="Masukkan token ujian" required>
<button class="btn btn-success">Masuk Ujian</button>
</form>
</div>
</div>
</div>
</body>
</html>


