<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$hal = 0;
if(isset($_GET['hal']))
{
	$hal = $_GET['hal'];
	
}
if(isset($_GET['id']))
{
	$id = $_GET['id'];
	
}

$query = "delete from `ujian_aktif` where `id_ujian` = '$id'";
$result = mysqli_query($db, $query);
header('Location: daftar_tes_hapus.php?hal='.$hal);
exit;
