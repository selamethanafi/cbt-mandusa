<?php
include '../inc/config.php';
if(!isset($_SESSION['id_siswa'], $_SESSION['kelas'])){
    header("Location: login.php");
    exit;
}
$id_siswa = $_SESSION['id_siswa'];
$id_ujian = $_SESSION['ujian_id'];
$id_soal = (int) $_POST['id_soal'];
$no = (int) $_POST['no'];
$nomer_soal = (int) $_POST['nomer_soal'];
$jawaban = $_POST['jawaban'] ?? '';

if (is_array($jawaban)) {
    $jawaban = json_encode($jawaban, JSON_UNESCAPED_UNICODE);
}

$q = $db->prepare("
    REPLACE INTO jawaban
    (id, id_siswa, id_ujian, id_soal, jawaban, nomer_soal, waktu_menjawab)
    VALUES
    (NULL, ?, ?, ?, ?, ?, NOW())
");

if (!$q) {
    die("Prepare error: " . $db->error);
}

$q->bind_param(
    "iiiss",
    $id_siswa,
    $id_ujian,
    $id_soal,
    $jawaban,
    $nomer_soal
);

if (!$q->execute()) {
    die("Execute error: " . $q->error);
}

$q->close();
$no++;
header("Location: ujian.php?no=".$no);
