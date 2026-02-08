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
    s.nama_siswa,
    s.kelas,
    s.rombel,
    u.mulai,
    u.selesai,
    u.status,
    COUNT(j.id_soal) AS dijawab
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

WHERE s.rombel = '$ruang'

GROUP BY s.id_siswa
ORDER BY s.kelas ASC, s.nama_siswa ASC
");


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="30">

<title>Pengawasan</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<h4 class="mb-2">Dashboard Pengawas</h4>
<table width="100%"><tr><td width="33%"  align="left"><a href="menu.php">Menu</a></td><td align="center">Ruang: <strong><?= htmlspecialchars($ruang) ?></strong></td><td width="33%" align="right">Jam Peladen:  <?= date("d-m-Y H:i");?></td></tr></table><hr>
<?php
$ta = mysqli_query($db, "SELECT * FROM `reset`");
                                if(mysqli_num_rows($ta) > 0)
				{
					?>
                                    <h5 class="card-title mb-0">Daftar Permintaan Reset</h5>
                                    <div class="card-body">
	                                    <div class="table-wrapper">
                             		           <table class="table table-bordered table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Nama Siswa</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <?php
                                            while($da=mysqli_fetch_assoc($ta))
                                            {
                                            	echo '<tr><td width="60%">'.$da['nama'].'</td><td>';
                                       		echo '<a href="reset_siswa.php?id_siswa='.$da['id_siswa'].'&tanggal='.$tanggal.'&jam='.$pukul.'" class="btn btn-danger">Reset</a>';
                                            	echo '</td></tr>';
                                            }
                                           ?>
                                        </table>
                                        <?php
                                        }
                                        ?>
<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:40px;">No</th>
    <th>Nama Siswa</th>
    <th style="width:90px;">Kelas</th>
    <th style="width:90px;">Status</th>
    <th style="width:80px;">Dijawab</th>
    <th style="width:110px;">Sisa Waktu</th>
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
    <td class="text-start"><?= htmlspecialchars($r['nama_siswa']) ?></td>
    <td><?= $r['kelas'] ?></td>
    <td>
        <span class="badge bg-<?= $badge ?>">
            <?= strtoupper($status) ?>
        </span>
    </td>
    <td><?= $r['dijawab'] ?></td>
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


</div>
</div>
    		<script>setTimeout(function () {
		 window.location.href= 'monitor.php';
			},<?php echo 60000;?>);
			</script>
</body>
</html>

