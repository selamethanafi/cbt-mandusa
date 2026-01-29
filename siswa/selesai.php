<?php
include '../inc/config.php';
if(!isset($_SESSION['id_siswa'], $_SESSION['kelas'])){
    header("Location: login.php");
    exit;
}
$skr = date("Y-m-d H:i:s");
$id=$_SESSION['id_siswa'];
$id_ujian = $_SESSION['ujian_id'];
$u = $db->query("SELECT * FROM ujian_aktif WHERE id_ujian=$id_ujian")->fetch_assoc();
$durasi = $u['waktu_ujian']; // menit
$uj = $db->query("SELECT * FROM ujian WHERE id_siswa=$id and id_ujian = $id_ujian")->fetch_assoc();
$sisa = strtotime($uj['selesai']) - time();
$sisa = $sisa / 60;
if($sisa < 80)
{
	$db->query("UPDATE ujian SET selesai=NOW(), status='selesai'  WHERE id_siswa=$id and id_ujian = $id_ujian");
	header("Location: nilai.php");
	}
	else
	{
		header("Location: ujian.php");	
	}
exit;

