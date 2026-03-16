<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$ke = $_GET['ke'] ?? 0;
if (!$db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
$aksi = '';
$nama_pengawas = '';
$catatan = '';
$ta =  mysqli_query($db, "SELECT DISTINCT `tanggal` FROM `ujian_aktif` order by `tanggal` limit $ke,1 ");
$da = mysqli_fetch_assoc($ta);
$waktu = $da['tanggal'] ?? date("Y-m-d H:i:d");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tes</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
<?php
echo $waktu;
?>
                    <div class="row">
                    <?php
	$query= "SELECT ua.id_ujian,
    ua.kode_soal,
    ua.nama_soal,
    ua.mapel, ua.kelas,ua.tampilan_soal, ua.status, ua.token,
    ua.waktu_ujian,
    ua.tanggal, ua.tanggal_selesai,
    COUNT(s.id) AS cacah_soal
FROM ujian_aktif ua
LEFT JOIN soal s 
    ON s.id_ujian = ua.id_ujian
    WHERE ua.tanggal = '$waktu'
GROUP BY 
    ua.id_ujian,
    ua.kode_soal,
    ua.nama_soal,
    ua.mapel,
    ua.waktu_ujian,
    ua.tanggal";
$result = mysqli_query($db, $query);
if(mysqli_num_rows($result) > 0)
{
	                    $no = 1;
	                    ?>
<table class="table table-bordered table-striped table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Soal</th>
                                                <th>Mapel</th>
                                                <th>Kls</th>
                                                <th>Jml Soal</th>
                                                <th>Durasi (menit)</th>
                                                <th>Tgl Ujian</th>
                                                <th>Tampilan</th>
                                                <th>Status</th>
                                                <th>Token</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td><?php echo '<a href="ubah_tes.php?id='.$row['id_ujian'].'">'.$no++.'</a>'; ?></td>
                                                <td><?php echo $row['kode_soal']; ?></td>
                                                <td><?php echo $row['mapel']; ?></td>
                                                <td><?php echo $row['kelas']; ?></td>
                                                <td><?php echo '<a href="view_soal.php?id_ujian='.$row['id_ujian'].'" target="_blank">'.$row['cacah_soal']; ?></a></td>
                                                <td><i class="fa fa-clock" aria-hidden="true"></i>
                                                    <?php echo $row['waktu_ujian']; ?></td>
                                                <td><i class="fa fa-calendar-alt" aria-hidden="true"></i>
                                                    <?php echo date('d M Y H:i', strtotime($row['tanggal'])); ?> s.d. <?php echo date('d M Y H:i', strtotime($row['tanggal_selesai'])); ?></td>
                                                <td><?php echo $row['tampilan_soal']; ?></td>
                                                <td>
                                                    <?php if ($row['status'] == 'Aktif') { ?>
                                                    <span class="badge bg-success">Aktif</span>  <a href="nonaktif.php?id_ujian=<?= $row['id_ujian'];?>" onclick="return confirm('Yakin menonaktifkan tes ini?')">Nonaktifkan</a>
                                                    <?php } else { ?>
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $row['token']; ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                 <?php        
                 }           
if($ke == 0)
{
	echo '<a href="daftar_tes_per_tanggal.php?ke=1">Halaman Selanjutnya</a>'; 
}
elseif($ke == 1)
{
	echo '<a href="daftar_tes_per_tanggal.php">Halaman Sebelumnya</a> '; 
	echo '<a href="daftar_tes_per_tanggal.php?ke=2">Halaman Selanjutnya</a> '; 
}
else
{
	$prev = $ke - 1;
	$nex = $ke + 1;
	echo '<a href="daftar_tes_per_tanggal.php?ke='.$prev.'">Halaman Sebelumnya</a> '; 
	echo '<a href="daftar_tes_per_tanggal.php?ke='.$nex.'">Halaman Selanjutnya</a> '; 	
}
?>
</body>

</html>
