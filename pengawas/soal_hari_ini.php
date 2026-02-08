<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

if(isset($_GET['jam']))
{
	$jam = $_GET['jam'];
	}
else
{
	$jam = '';
}
if(isset($_GET['tgl']))
{
	$tgl= $_GET['tgl'];
	}
else
{
	$tgl = '';
}
$tanggal = $tgl;
if(empty($tgl))
{
	$tanggal = date("Y-m-d");
}
$waktu = $tanggal.' '.$jam;
if((empty($waktu)) or ($waktu == ' '))
{
	$waktu = date("Y-m-d H:i:s");
}

	$query = "
    SELECT 
        s.id_soal, s.kode_soal, s.nama_soal, s.mapel, s.kelas, s.tampilan_soal, s.status, s.tanggal, s.waktu_ujian, s.token,
        COUNT(b.id_soal) AS jumlah_butir
    FROM soal s
    LEFT JOIN butir_soal b ON s.kode_soal = b.kode_soal where s.tanggal = '$waktu'
    GROUP BY s.id_soal, s.kode_soal, s.nama_soal, s.mapel, s.kelas, s.status,  s.tanggal, s.waktu_ujian, s.token
";
$query= "SELECT ua.id_ujian,
    ua.kode_soal,
    ua.nama_soal,
    ua.mapel, ua.kelas,ua.tampilan_soal, ua.status, ua.token,
    ua.waktu_ujian,
    ua.tanggal,
    COUNT(s.id) AS cacah_soal
FROM ujian_aktif ua
LEFT JOIN soal s 
    ON s.id_ujian = ua.id_ujian where ua.tanggal = '$waktu'
GROUP BY 
    ua.id_ujian,
    ua.kode_soal,
    ua.nama_soal,
    ua.mapel,
    ua.waktu_ujian,
    ua.tanggal
ORDER BY ua.tanggal DESC;
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
<title>Daftar Tes Hari ini</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
                                <?php
                                echo $tanggal;
                                if(empty($jam))
                                {
                                	echo '<p>Pilih waktu tes</p>';
                                	$ta = mysqli_query($db, "SELECT DISTINCT `tanggal` FROM `ujian_aktif` WHERE `tanggal` like '$tanggal%'");
                                	while ($da = mysqli_fetch_assoc($ta)) 
                                	{
                                		echo '<p><a href="soal_hari_ini.php?tgl='.$tanggal.'&jam='.substr($da['tanggal'],-8).'">'.substr($da['tanggal'],-8).'</a></p>';
                                	}
                                }
                                else
                                {?>
                                 <a class="btn btn-primary" href="aktifkan_semua_tes.php?jam=<?php echo $jam;?>" onclick="return confirm('Yakin mengaktifkan tes?')">Aktifkan Semua Tes</a> <a class="btn btn-success" href="monitor.php?tanggal=<?= $tgl;?>&jam=<?php echo $jam;?>">MONITORING</a><br /><br />

                                    <table id="soalTable" class="table table-striped">
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
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo $row['kode_soal']; ?></td>
                                                <td><?php echo $row['mapel']; ?></td>
                                                <td><?php echo $row['kelas']; ?></td>
                                                <td><?php echo $row['cacah_soal']; ?></td>
                                                <td>
                                                    <?php echo $row['waktu_ujian']; ?></td>
                                                <td>
                                                    <?php echo $row['tanggal']; ?></td>
                                                <td><?php echo $row['tampilan_soal']; ?></td>
                                                <td><?php echo $row['status'];?>
                                                </td>
                                                <td><?php echo $row['token']; ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php
                                    }?>
</div>
</body>

</html>
