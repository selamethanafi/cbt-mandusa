<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$waktu = 10;
if(isset($_GET['id_siswa']))
{
$id_siswa = $_GET['id_siswa'];
}
else
{
$id_siswa = '0';
}
if(isset($_GET['id_ujian']))
{
$id_ujian = $_GET['id_ujian'];
}
else
{
$id_ujian = '0';
}
$query_nilai = $db->query("SELECT * FROM `ujian` WHERE `id_siswa` = '$id_siswa' and `id_ujian` = '$id_ujian'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Waktu</title>
<link rel="stylesheet" href="../css/style.css">
<?php
while($hu = mysqli_fetch_assoc($query_nilai))
{
	$id_ujian = $hu['id_ujian'];
	$id_siswa = $hu['id_siswa'];
	$mulai = $hu['mulai'];
	$selesai = $hu['selesai'];
	$sekarang = date("Y-m-d H:i:s");
	$waktu1 = date("Y-m-d H:i:s");
	$waktu2 = date("Y-m-d H:i:s", strtotime($waktu1 . " -110 minutes"));
	$waktu3 = date("Y-m-d H:i:s", strtotime($waktu1 . " +10 minutes"));	
	//echo $waktu2.' '.$waktu3;
	$db->query("update `ujian` set `mulai` = '$waktu2', `selesai` = '$waktu3', `status` = 'aktif' WHERE `id_ujian` = '$id_ujian' and `id_siswa` = '$id_siswa'");
	$db->query("delete FROM `nilai` WHERE `id_ujian` = '$id_ujian' and `id_siswa` = '$id_siswa'");
	echo 'Berhasil';
}
?>



