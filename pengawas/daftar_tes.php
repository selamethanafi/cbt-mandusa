<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$hal = 0;
if(isset($_GET['hal']))
{
	$hal = $_GET['hal'];
	
}
$limit = $hal * 10;
$semester = cari_semester();
$ajaran = cari_thnajaran();

//$query = "SELECT * FROM `ujian_aktif` ORDER BY `ujian_aktif`.`tanggal` ASC";
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
GROUP BY 
    ua.id_ujian,
    ua.kode_soal,
    ua.nama_soal,
    ua.mapel,
    ua.waktu_ujian,
    ua.tanggal
ORDER BY ua.tanggal ASC limit $limit,10;
";
$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    // If there's an error with the query, display the error message
    die('Error with the query: ' . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Daftar Tes</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
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
                                                <td><?php echo $row['cacah_soal']; ?></td>
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
if($hal == 0)
{
	echo '<a href="daftar_tes.php?hal=1">Halaman Selanjutnya</a>'; 
}
elseif($hal == 1)
{
	echo '<a href="daftar_tes.php">Halaman Sebelumnya</a> '; 
	echo '<a href="daftar_tes.php?hal=2">Halaman Selanjutnya</a> '; 
}
else
{
	$prev = $hal - 1;
	$nex = $hal + 1;
	echo '<a href="daftar_tes.php?hal='.$prev.'">Halaman Sebelumnya</a> '; 
	echo '<a href="daftar_tes.php?hal='.$nex.'">Halaman Selanjutnya</a> '; 	
}
?>
    </div>
  
</body>

</html>
