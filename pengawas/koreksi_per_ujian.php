<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
include '../peserta/fungsi_nilai.php';
$id_ujian = (int) $_GET['id_ujian'];
$ke=$_GET['ke'] ?? '0';
$ta = $db->query("select * from `ujian` where `id_ujian` = '$id_ujian'");
$total = mysqli_num_rows($ta);
$ta = $db->query("select * from `ujian` where `id_ujian` = '$id_ujian' limit $ke,1");
$ada = mysqli_num_rows($ta);
echo 'Ada '.$ada;
while ($da = $ta->fetch_assoc()) 
{
	$id_siswa = $da['id_siswa'];
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
$ke++;
			$lanjut = 'koreksi_per_ujian.php?ke='.$ke.'&id_ujian='.$id_ujian;
			?>
					<script>setTimeout(function () {
						   window.location.href= '<?php echo $lanjut;?>';
					},10);
					</script>
					<?php
}
if($ada == 0)
{
	echo ' Rampung <a href="kirim_per_ujian.php?id_ujian='.$id_ujian.'">Kirim Hasil<a/>';
}
