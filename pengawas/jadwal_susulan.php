<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

if (!$db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
$id_siswa = isset($_GET['id_siswa'])
    ? (int) $_GET['id_siswa']
    : 0;

$id_ujian = isset($_GET['id_ujian'])
    ? (int) $_GET['id_ujian']
    : 0;

$ta = mysqli_query($db, "select * from `cbt_konfigurasi` where `konfigurasi_kode` = 'waktu_akhir'");
$da = mysqli_fetch_assoc($ta);
$waktu_akhir = $da['konfigurasi_isi'];
$waa = date("Y-m-d").' '.$waktu_akhir;
$updateStatus = mysqli_query($db, "UPDATE ujian_aktif SET tanggal = '$waa' WHERE id_ujian = '$id_ujian'");
$url = $sianis.'/cbt/updatepeserta/'.$key.'/'.$id_siswa;
if((!empty($key)) and (!empty($sianis)))
{
	$json = via_curl($url);    
	if($json)
	{
       	foreach($json as $dm)
		{
    		$pesan = $dm['pesan'];
			if($pesan == 'ada')
			{
				$username = $dm['nopes'];
				$password = $dm['password'];
				$db->query("UPDATE `siswa` SET `password`='$password' WHERE `id_siswa` = '$id_siswa'");
			}
		}
	}
}
header('Location: siswa.php');
exit;
