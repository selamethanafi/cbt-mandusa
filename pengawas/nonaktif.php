<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
if (isset($_GET['id_ujian']))
{
	$id_ujian = $_GET['id_ujian'];
	$updateStatus = mysqli_query($db, "UPDATE ujian_aktif SET status = 'Nonaktif' WHERE id_ujian = '$id_ujian'");
}
header('Location: daftar_tes.php');
exit;
