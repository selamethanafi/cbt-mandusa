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
$ta = $db->query( "SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'app_key_server_cbt_lokal'");
$da = mysqli_fetch_assoc($ta);
$key = $da['konfigurasi_isi'];
$query_nilai = $db->query("SELECT * FROM `ujian` WHERE `id_siswa` = '$id_siswa' and `id_ujian` = '$id_ujian'");
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='ubk_pusat'");
$da = mysqli_fetch_assoc($ta);
$ubk_pusat = $da['konfigurasi_isi'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mengirim Jawaban Siswa</title>
<link rel="stylesheet" href="../css/style.css">
<?php

while($hu = mysqli_fetch_assoc($query_nilai))
{
	$id_ujian = $hu['id_ujian'];
	$id_siswa = $hu['id_siswa'];
	$qnu = $db->query("SELECT * FROM `ujian_aktif` WHERE `id_ujian` = '$id_ujian'");
	$dnu = mysqli_fetch_assoc($qnu);
	$kode_soal = $dnu['kode_soal'] ?? '';
	$qj = $db->query("SELECT * FROM jawaban WHERE id_siswa = '$id_siswa' AND id_ujian = '$id_ujian' ORDER BY id_soal ASC");
	$data = [];
	while ($row = $qj->fetch_assoc()) {
	    $data[] = $row;
	}
	$url = $ubk_pusat."/tukardata/terima_jawaban.php";
	$payload = [
	    'id_siswa' => $id_siswa,
	    'id_ujian' => $id_ujian,
	    'jawaban'  => $data
	];
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $key
]);
	$response = curl_exec($ch);
	curl_close($ch);
	echo $response;
}
?>



