<?php
include '../inc/config.php';
$id_siswa = $_SESSION['id_siswa'];

$id_soal = $_POST['id_soal'];
$no = $_POST['no'];
$jawaban = $_POST['jawaban'] ?? '';

if(is_array($jawaban)){
    $jawaban = json_encode($jawaban);
}

$q = $db->prepare("REPLACE INTO jawaban VALUES(NULL,?,?,?)");
$q->bind_param("iis",$id_siswa,$id_soal,$jawaban);
$q->execute();
$no++;
header("Location: ujian.php?no=".$no);
