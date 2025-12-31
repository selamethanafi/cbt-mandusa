<?php
include '../inc/config.php';
$id=$_SESSION['id_siswa'];

$db->query("UPDATE ujian SET selesai=NOW(), status='selesai'  WHERE id_siswa=$id");

header("Location: nilai.php");
exit;

