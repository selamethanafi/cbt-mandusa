<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
include '../peserta/fungsi_nilai.php';
$id_siswa = (int) $_GET['id_siswa'];
$id_ujian = (int) $_GET['id_ujian'];
$tanggal=$_GET['tanggal'] ?? '';
$jam = $_GET['jam'] ?? '';
$ts = $db->query("SELECT * FROM `soal` WHERE `id_ujian` = '$id_ujian' order by `nomer_soal`");
$no = 1;
while($ds=$ts->fetch_assoc())
{
	$id_soal = $ds['id'];
	$nomer_soal = $ds['nomer_soal'];
	$tj = $db->query("SELECT * FROM `jawaban` WHERE `id_siswa` = '$id_siswa' and `id_soal` = '$id_soal'");
	$cacah = mysqli_num_rows($tj);
	//echo $no.'. '.$id_soal.' '.$nomer_soal.' '.$cacah.'<br />';
	if($cacah == 0)
	{
		$jawaban = '?';
		$stmt2 = $db->prepare("INSERT INTO `jawaban`(`id_siswa`, `id_ujian`, `id_soal`, `jawaban`, `nomer_soal`) VALUES (?, ?, ?,?, ?)");
		$stmt2->bind_param("iiisi", $id_siswa, $id_ujian, $id_soal, $jawaban,$nomer_soal);
		$stmt2->execute();
	}
	$no++;
}


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
        $id_jawaban = (int)$row['id'];

        $db->query("
            UPDATE jawaban
            SET nilai = '$nilai'
            WHERE id = $id_jawaban
            AND id_siswa = '$id_siswa'
        ");

        $skor += $nilai;
    }

    $nosoal++;
}

$nilai_akhir = ($nosoal > 0) ? (100 * $skor / $nosoal) : 0;

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
echo 'Nilai '.$nilai_akhir;

if((empty($tanggal)) or (empty($jam) ))
{
?>
<script>setTimeout(function () {
			   window.location.href= 'hasil_siswa.php?id=<?php echo $id_siswa;?>';
				},1000);
			</script>
			<?php


}
else
{
?>

<script>setTimeout(function () {
			   window.location.href= 'nilai.php?tanggal=<?php echo $tanggal;?>&jam=<?php echo $jam;?>';
				},10);
			</script>
			<?php
}

