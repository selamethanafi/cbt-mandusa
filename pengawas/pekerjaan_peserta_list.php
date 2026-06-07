<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';

$tanggal = $_GET['tanggal'] ?? date("Y-m-d");
$pukul = $_GET['jam'] ?? date("H:i:s");
$waktu = $tanggal.' '.$pukul;
if (isValidDateTime($waktu)) {

} else {
    echo "Format waktu salah";
	die();
}

$q = $db->query("
SELECT 
    s.id_siswa,
    s.kelas,
    s.username,
    s.nama_siswa,
    s.kelas,
    s.rombel,
    u.mulai,
    u.selesai,
    u.status,
    COUNT(j.id_soal) AS dijawab,
    MAX(j.waktu_menjawab) AS terakhir_menjawab

FROM siswa s

LEFT JOIN ujian_aktif ua
    ON ua.tanggal = '$waktu'
    AND ua.kelas = s.kelas

LEFT JOIN ujian u
    ON u.id_siswa = s.id_siswa
    AND u.id_ujian = ua.id_ujian

LEFT JOIN jawaban j 
    ON j.id_siswa = s.id_siswa
    AND j.id_ujian = u.id_ujian
    AND j.jawaban IS NOT NULL
    AND TRIM(j.jawaban) != ''

WHERE s.rombel = '$ruang'

GROUP BY s.id_siswa

ORDER BY s.kelas ASC, s.nama_siswa ASC
");
/*
$q = $db->query("SELECT
    s.id_siswa,
    s.nama_siswa,
    s.kelas,
    u.id_ujian,
    u.status
FROM siswa s
LEFT JOIN ujian u
    ON u.id_siswa = s.id_siswa
    AND u.id_ujian IN (
        SELECT id_ujian
        FROM ujian_aktif
        WHERE tanggal = '$waktu'
        AND kelas = s.kelas
    )
WHERE s.rombel = '$ruang'
ORDER BY s.nama_siswa");
*/
?>
<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:40px;">No</th>
    <th style="width:150px;">No Peserta</th>    
    <th>Nama Siswa</th>
    <th style="width:90px;">Kelas</th>
    <th style="width:90px;">Status</th>
    <th style="width:80px;">Dijawab</th>
    <th style="width:60px;">Terakhir<br />Menjawab<br />(menit yang lalu)</th>
    <th style="width:50px;">Sisa Waktu</th>    
</tr>
</thead>
<tbody>
<?php

$belum = 0;
$tidak = 0;
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
    <td><?= $r['kelas'] ?></td>
    <td>
        <span class="badge bg-<?= $badge ?>">
            <?= strtoupper($status) ?>
        </span>
    </td>
    <td><?= $r['dijawab'] ?></td><td>
    <?php
    if (strtoupper($status) == 'SELESAI')
    {
    	echo '';
    	}
    else
    {
	if (!empty($r['terakhir_menjawab'])) 
	{
		$menit = floor((time() - strtotime($r['terakhir_menjawab'])) / 60);
		    echo $menit > 5
	        ? '<span style="color:red;">⚠️ '.$menit.' menit yang lalu</span>'
	        : $menit;
	} else {
	    echo '-';
	}
	}
     	?>
    	</td>
    <td><?= $sisa ?></td>
</tr>
<?php } ?>
</tbody>
</table>
<div class="mt-2 small">
<span class="badge bg-success">AKTIF</span> Sedang ujian &nbsp;
<span class="badge bg-warning text-dark">BELUM</span> Belum mulai &nbsp;
<span class="badge bg-secondary">SELESAI</span> Sudah selesai
</div>
