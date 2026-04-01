<?php
include '../inc/config.php';
include 'fungsi_nilai.php';
if(!isset($_SESSION['id_siswa'], $_SESSION['kelas'])){
    header("Location: login.php");
    exit;
}
$id_siswa = $_SESSION['id_siswa'];
$id_ujian = $_SESSION['ujian_id'];

/* ===============================
   Ambil jawaban siswa ini saja
================================ */
$q = $db->query("
SELECT j.id,
       j.jawaban,
       s.id AS id_soal,
       s.tipe,
       s.kunci
FROM jawaban j
JOIN soal s ON s.id = j.id_soal
WHERE j.id_ujian = '$id_ujian'
AND j.id_siswa = '$id_siswa'
");
$skor = 0;
$nosoal = 0;
while ($row = $q->fetch_assoc()) {
    $nilai = hitung_nilai($row, $row['jawaban']);

    if ($nilai !== null) {
        $db->query("
            UPDATE jawaban
            SET nilai = '$nilai'
            WHERE id = {$row['id']}
            AND id_siswa = '$id_siswa'
        ");
    }
    $skor = $skor + $nilai;
    $nosoal++;
}

$nilai_akhir = 100* $skor / $nosoal;
/* ===============================
   Tandai ujian selesai
================================ */
$db->query("
UPDATE ujian
SET status = 'Selesai'
WHERE id_ujian = '$id_ujian'
AND id_siswa = '$id_siswa'
");

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Hasil CBT</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid mt-4">
<div class="card shadow-sm">
<div class="card-body">
<h4 class="mb-2">Hasil Tes</h4>
<?php
// simpan nilai
$db->query("
UPDATE ujian
SET status = 'Selesai'
WHERE id_ujian = '$id_ujian'
AND id_siswa = '$id_siswa'
");
$stmt = $db->prepare("
INSERT INTO nilai (id_siswa, id_ujian, nilai) 
VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)
");

$stmt->bind_param("iid", $id_siswa, $id_ujian, $nilai_akhir);
$stmt->execute();

$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='tampil_nilai'");
$da = mysqli_fetch_assoc($ta);
$batas = $da['konfigurasi_isi'] ?? '0';
if($batas > 0)
{
	echo 'Cacah soal '.$nosoal;
	echo '<div class="alert-danger">Nilai sementara: '.round($nilai_akhir,2).'</div>';
	echo '<br />';
}
else
{
	echo 'Cacah soal '.$nosoal;
	echo '<div class="alert-danger">Nilai menunggu dikoreksi oleh guru mapel</div>';
	echo '<br />';
}
echo '<a href="../logout.php" class="btn btn-primary mb-2">Keluar</a>';
?>
</div>
</div>
</div>
</body>
</html>

