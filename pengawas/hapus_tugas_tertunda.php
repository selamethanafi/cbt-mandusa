<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$id_siswa = isset($_GET['id_siswa']) 
    ? $db->real_escape_string($_GET['id_siswa']) 
    : '';

$tes_id = isset($_GET['tes_id']) 
    ? $db->real_escape_string($_GET['tes_id']) 
    : '';

if (($id_siswa != '') and ($tes_id != '')){

    $query = "
        DELETE FROM `siswa_absen` WHERE `tes_id` = '$tes_id' and `nis` = '$id_siswa'";

    $db->query($query);
}

// Kembali ke halaman detail
header(
    "Location: siswa.php?aksi=pw");

exit;
