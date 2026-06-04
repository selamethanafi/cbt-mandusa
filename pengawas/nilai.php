<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$tanggal = $_GET['tanggal'] ?? '';
$pukul = $_GET['jam'] ?? '';
$waktu = $tanggal.' '.$pukul;
if (isValidDateTime($waktu)) {

} else {
    echo "Format waktu salah";
    ?>
					<script>
					// Auto redirect setelah 2 detik
					setTimeout(function(){
					window.location.href = 'soal_hari_ini.php';
					}, 2000);
					</script>
					<?php	
					exit;
}
$q = $db->query("
SELECT 
    s.id_siswa,
    s.username,
    s.nama_siswa,
    s.kelas,
    s.rombel,
    u.mulai,
    u.selesai,
    u.status,
    u.id_ujian,
    COUNT(j.id_soal) AS dijawab,
    n.nilai

FROM siswa s

LEFT JOIN ujian u 
    ON u.id_siswa = s.id_siswa
    AND u.id_ujian IN (
        SELECT id_ujian 
        FROM ujian_aktif 
        WHERE tanggal = '$waktu'
    )

LEFT JOIN jawaban j 
    ON j.id_siswa = s.id_siswa
    AND j.id_ujian = u.id_ujian
    AND j.jawaban IS NOT NULL
    AND TRIM(j.jawaban) != ''

LEFT JOIN nilai n
    ON n.id_siswa = s.id_siswa
    AND n.id_ujian = u.id_ujian

WHERE s.rombel = '$ruang'

GROUP BY s.id_siswa, u.id_ujian

ORDER BY s.kelas ASC, s.nama_siswa ASC
");

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="30">

<title>Hasil Ujian</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<h4 class="mb-2">Hasil Ujian</h4>
<table width="100%"><tr><td width="33%"  align="left"><a href="menu.php">Menu</a></td><td align="center">Ruang: <strong><?= htmlspecialchars($ruang) ?></strong></td><td width="33%" align="right">Jam Peladen:  <?= date("d-m-Y H:i");?></td></tr></table><hr>

<?php
echo '<a href="nilai.php?tanggal='.$tanggal.'&jam='.$pukul.'" class="btn btn-success">Muat Ulang</a>';
$belum = 0;
$tidak = 0;
                                        ?>
<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:40px;">No</th>
    <th style="width:150px;">No Peserta</th>    
    <th>Nama Siswa</th>
    <th style="width:90px;">Id Ujian</th>    
    <th style="width:90px;">Kelas</th>
    <th style="width:90px;">Status</th>
    <th style="width:80px;">Dijawab</th>
    <th style="width:110px;">Nilai</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
while($r = $q->fetch_assoc()){

    // status default
    $status = $r['status'] ?? 'belum';

    // badge warna
    if($status == 'aktif'){
        $badge = 'success';
        $belum++;
    } elseif($status == 'selesai'){
        $badge = 'secondary';
    } else {
        $badge = 'warning';
    }

    // hitung sisa waktu
    $sisa = '-';
    if($status == 'aktif' && !empty($r['selesai'])){
        $detik = strtotime($r['selesai']) - time();
        $sisa = $detik > 0 ? gmdate("H:i:s",$detik) : '00:00:00';
    }
?>
<tr>
    <td><?= $no++ ?></td>
    <td class="text-start"><?= htmlspecialchars($r['username']) ?></td>        
    <td class="text-start"><?= htmlspecialchars($r['nama_siswa']) ?></td>
    <td class="text-start"><?= htmlspecialchars($r['id_ujian']) ?></td>
    <td><?= $r['kelas'] ?></td>
    <td>
        <span class="badge bg-<?= $badge ?>">
            <?= strtoupper($status) ?>
        </span>
    </td>
    <td> <a href="jawaban.php?id_siswa=<?= $r['id_siswa'];?>&id_ujian=<?= $r['id_ujian'];?>" target="_blank"><?= $r['dijawab'] ?></a></td>
    <td><?= $r['nilai'] ?> <a href="koreksi.php?id_siswa=<?= $r['id_siswa'];?>&id_ujian=<?= $r['id_ujian'];?>&tanggal=<?= $tanggal;?>&jam=<?= $pukul;?>">Koreksi</a></td>
</tr>
<?php } ?>
</tbody>
</table>
<div class="mt-2 small">
<span class="badge bg-success">AKTIF</span> Sedang ujian &nbsp;
<span class="badge bg-warning text-dark">BELUM</span> Belum mulai &nbsp;
<span class="badge bg-secondary">SELESAI</span> Sudah selesai
</div>


</div>
</div>
</body>
</html>

