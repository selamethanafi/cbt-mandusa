<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

include '../inc/config.php';
if(!isset($_SESSION['id_siswa'], $_SESSION['kelas'])){
    header("Location: login.php");
    exit;
}
$id = (int)$_GET['id'];
$id_siswa = $_SESSION['id_siswa'];
$u = $db->query("SELECT * FROM ujian_aktif WHERE id_ujian=$id")->fetch_assoc();
$kode_soal = $u['kode_soal'];
if (strpos($kode_soal, '_') === false) 
{
    $hasil = $kode_soal;
}
else
{
     $bagian = explode('_', $kode_soal);
	$hasil = $bagian[1];
}
$stmt = $db->prepare(
    "SELECT *
     FROM `siswa_absen`
     WHERE `nis` = ? and `tes_id` = ?"
);

$stmt->bind_param('ii', $id_siswa,$hasil);
$stmt->execute();
$result = $stmt->get_result();
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
<h4>Kode Tes <?= $u['kode_soal'] ?></h4>
<?php
 if ($result->num_rows > 0) {

        echo '<div">';
        echo '<h1>Soal tidak tersedia, karena ada tugas / tes yang belum dikerjakan.</h1>';
        echo '</div>';

    }
else
{
if(isset($err)) echo '<div class="alert-danger">'.$err.'</div>'; ?>
<form method="post">
<input class="form-control mb-2" name="token" placeholder="Masukkan token ujian" required>
<button class="btn btn-success">Masuk Ujian</button>
</form>
</div>
</div>
</div>
<?php
}
?>
</body>
</html>


