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
<title>Susulan Hari ini</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
                                <?php
                                echo $tanggal.' '.$jam;
                                if(empty($jam))
                                {
                                	echo '<p>Pilih waktu tes</p>';
                                	$ta = mysqli_query($db, "SELECT DISTINCT `tanggal` FROM `ujian_aktif` WHERE `tanggal` like '$tanggal%'");
                                	while ($da = mysqli_fetch_assoc($ta)) 
                                	{
                                		echo '<p><a href="susulan.php?tgl='.$tanggal.'&jam='.substr($da['tanggal'],-8).'">'.substr($da['tanggal'],-8).'</a></p>';
                                	}
                                	echo '<p><a href="susulan.php?tgl='.$tanggal.'&jam=07:00:00">07:00:00</a></p>';
                                	echo '<p><a href="susulan.php?tgl='.$tanggal.'&jam=08:00:00">08:00:00</a></p>';
                                	echo '<p><a href="susulan.php?tgl='.$tanggal.'&jam=09:00:00">09:00:00</a></p>';
                                	echo '<p><a href="susulan.php?tgl='.$tanggal.'&jam=10:00:00">10:00:00</a></p>';
                                	echo '<p><a href="susulan.php?tgl='.$tanggal.'&jam=11:00:00">11:00:00</a></p>';
                                	echo '<p><a href="susulan.php?tgl='.$tanggal.'&jam=12:00:00">12:00:00</a></p>';
                                	echo '<p><a href="susulan.php?tgl='.$tanggal.'&jam=13:00:00">13:00:00</a></p>';
                                }
                                else
                                {
                                $url = $sianis.'/cbt/jadwalsusulan/'.$key;
//echo $url;
echo '<br />';
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='url_cbt'");
$da = mysqli_fetch_assoc($ta);
$url_cbt = $da['konfigurasi_isi'] ?? '';

echo 'Ruang '.$ruang.'<br />';
if (str_contains($url_cbt, 'susulan')) 
{
	$json = via_curl($url);    
	if($json)
	{
		$db->query("UPDATE ujian_aktif SET status = 'Nonaktif', token = '' WHERE 1");	
		$db->query("update `siswa` set `kelas` = '', `rombel` = '' where 1");		
	       	foreach($json as $dm)
		{
			$pesan = $dm['pesan'];
			if($pesan == 'ada')
			{
				$namasiswa = $dm['namasiswa'];
			        $kode_soal = clean($dm['tmujian_id']);
			        $id_siswa = $dm['tmsiswa_id'];
			        $password = $dm['password'];
				echo $namasiswa.' '.$kode_soal.'<br />';				        
				        $date = new DateTime($waktu);
					$date->modify('+1 month');
					$tanggal_selesai = $date->format('Y-m-d H:i:s');
				        $sql = "UPDATE ujian_aktif SET  kelas=?, tanggal = ?, tanggal_selesai = ? WHERE kode_soal = ?";
					$stmt = $db->prepare($sql);
					if (!$stmt) {
					    die("Prepare error: " . $db->error);
					}
					$stmt->bind_param("ssss", $kode_soal,$waktu,$tanggal_selesai,$kode_soal);
					$stmt->execute();
					if (!$stmt->execute()) 
					{
						die("Execute error: " . $stmt->error);
					}
					if ($stmt->affected_rows > 0) 
					{
					    echo "Data berhasil diupdate";
					} else {
					    //echo "Tidak ada perubahan";
					}
					$db->query("update `siswa` set `kelas` = '$kode_soal',`password`='$password', `rombel` = '$ruang' where `id_siswa` = '$id_siswa'");
			}
		}
		echo '<a href="soal_hari_ini.php">Aktifkan Tes</a>';
	}
	else
	{
		echo 'Gagal tersambung ke simamad';
	}
}
else
{
	echo 'server ini tidak bisa digunakan untuk susulan';
}
}
?>
</div>
</body>

</html>
