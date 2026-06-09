<?php
session_start();
include '../inc/config.php';

header('Content-Type: application/json');

$id_siswa = $_SESSION['id_siswa'] ?? 0;
$id_ujian = $_SESSION['ujian_id'] ?? 0;

$q = $db->query("
    SELECT MAX(waktu_menjawab) AS terakhir
    FROM jawaban
    WHERE id_siswa='$id_siswa'
    AND id_ujian='$id_ujian'
");

$r = $q->fetch_assoc();

if(empty($r['terakhir']))
{
    echo json_encode([
        'idle' => false,
        'menit' => 0
    ]);
    exit;
}

$detik = time() - strtotime($r['terakhir']);

echo json_encode([
    'idle' => ($detik > 300),
    'menit' => floor($detik/60),
    'detik' => $detik
]);