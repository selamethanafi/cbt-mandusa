<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$id = isset($_GET['id']) 
    ? $db->real_escape_string($_GET['id']) 
    : '';

$id_siswa = isset($_GET['id_siswa']) 
    ? $db->real_escape_string($_GET['id_siswa']) 
    : '';

$id_ujian = isset($_GET['id_ujian']) 
    ? $db->real_escape_string($_GET['id_ujian']) 
    : '';

if ($id != '') {

    $query = "
        DELETE FROM jawaban
        WHERE id = '$id'
    ";

    $db->query($query);
}

// Kembali ke halaman detail
header(
    "Location: jawaban.php?id_siswa=" .
    urlencode($id_siswa) .
    "&id_ujian=" .
    urlencode($id_ujian)
);

exit;