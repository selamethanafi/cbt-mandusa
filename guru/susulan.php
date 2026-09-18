<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/guru.php';
$username = $_SESSION['nip'];
$waktu = date("Y-m-d H:i:s");
if(isset($_GET['kode_soal']))
{
	$kode_soal = $_GET['kode_soal'];
	}
else
{
	die('kode soal tidak boleh kosong');
}
$user_id = 0;
$ta = $db->query("SELECT * from `guru` where `username` = '$username'");
$ada = mysqli_num_rows($ta);
if($ada == 0)
{
	$db->query("insert into `guru` (`username`) values ('$username')");
}
else
{
	$da = mysqli_fetch_assoc($ta);
	$user_id = $da['user_id'] ?? 0;
}
if(empty($user_id))
{
	echo '<h1>Belum sinkron, hubungi admin</h1>';
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
                                echo $waktu;
                                $url = $sianis.'/cbt/jadwalsusulantes/'.$key.'/'.$kode_soal;
echo '<br />';
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='url_cbt'");
$da = mysqli_fetch_assoc($ta);
$url_cbt = $da['konfigurasi_isi'] ?? '';

echo 'Ruang '.$ruang.'<br />';
	$json = via_curl($url);    
	if($json)
	{
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
					$db->query("update `siswa` set `kelas` = '$kode_soal',`password`='$password' where `id_siswa` = '$id_siswa'");
			}
		}
		echo '<a href="soal_hari_ini.php">Aktifkan Tes</a>';
	}
	else
	{
		echo 'Gagal tersambung ke simamad';
	}

?>
</div>
</body>

</html>
