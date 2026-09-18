<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/guru.php';
if (!isset($_GET['id_ujian']))
 {
    header('Location: soal_hari_ini.php');
    exit;
}
// Untuk aksi AKTIF
$id_ujian = $_GET['id_ujian'];
 $kode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6));
 $status = 'Aktif';
$updateStatus = mysqli_query($db, "UPDATE ujian_aktif SET status = 'Aktif', token = '$kode' WHERE id_ujian = '$id_ujian'");
header('Location: soal_hari_ini.php');
exit;
