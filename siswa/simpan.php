<?php
include '../inc/config.php';
if(!isset($_SESSION['id_siswa'], $_SESSION['kelas'])){
    header("Location: login.php");
    exit;
}
$id_siswa = $_SESSION['id_siswa'];
$id_ujian = $_SESSION['ujian_id'];
$id_soal = $_POST['id_soal'];
$no = $_POST['no'];
$jawaban = $_POST['jawaban'] ?? '';

if(is_array($jawaban)){
    //$jawaban = json_encode($jawaban);
    $jawaban = json_encode($_POST['jawaban'], JSON_UNESCAPED_UNICODE);

}
echo $jawaban;
$q = $db->prepare("REPLACE INTO jawaban VALUES(NULL,?,?,?,?,'0')");
if (!$q) {
    die("Prepare error: " . $db->error);
}
$q->bind_param("iiis",$id_siswa,$id_ujian,$id_soal,$jawaban);
$q->execute();

$no++;
header("Location: ujian.php?no=".$no);
