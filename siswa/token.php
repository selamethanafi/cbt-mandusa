<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

include '../inc/config.php';

$id = (int)$_GET['id'];
$u = $db->query("SELECT * FROM ujian_aktif WHERE id=$id")->fetch_assoc();

if(!$u) exit('Ujian tidak ditemukan');

if($_SERVER['REQUEST_METHOD']=='POST'){
    $token = strtoupper(trim($_POST['token']));

    if($token == $u['token']){
        $_SESSION['ujian_id'] = $u['id'];
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
<meta http-equiv="refresh" content="30">

<title>CBT Ujian</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<div class="card shadow-sm">
<div class="card-body">

<h4><?= $u['nama_ujian'] ?></h4>
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


