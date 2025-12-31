<?php
include '../inc/config.php';
include 'fungsi_nilai.php';

$id_siswa = $_SESSION['id_siswa'];

// ambil semua jawaban siswa
$q = $db->query("
SELECT s.tipe, j.jawaban, s.kunci
FROM jawaban j
JOIN soal s ON j.id_soal=s.id
WHERE j.id_siswa=$id_siswa
");

$nilai = 0;
$jumlah = 0;

while($r = $q->fetch_assoc()){
    if($r['tipe'] != 'uraian'){
        $nilai += nilai_otomatis(
            $r['tipe'],
            $r['jawaban'],
            $r['kunci']
        );
        $jumlah++;
    }
}
$skor = $jumlah ? round(($nilai/$jumlah)*100) : 0;

// simpan nilai
$db->query("
REPLACE INTO nilai (id_siswa, nilai)
VALUES ($id_siswa, $skor)
");

echo "Nilai sementara: $skor";
