<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$tanggal = $_GET['tanggal'] ?? '';
$pukul = $_GET['jam'] ?? '';
$id_siswa = $_GET['id_siswa'] ?? '';
$data = mysqli_query($db, "SELECT * FROM siswa WHERE id_siswa = '$id_siswa'");
if (mysqli_query($db, "update siswa set `session_token` = NULL WHERE id_siswa = '$id_siswa'")) 
{
	mysqli_query($db, "delete from `reset` where `id_siswa` = '$id_siswa'");
	mysqli_query($db, "update `siswa` set `session_token` = '' where `id_siswa` = '$id_siswa'");
} else 
{

}
header('Location: monitor.php?tanggal='.$tanggal.'&jam='.$pukul);
exit;
	

