<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='tanggal_awal'");
$da = mysqli_fetch_assoc($ta);
$tanggal_awal = $da['konfigurasi_isi'] ?? '';

$waktu = 10;
$skr = date("Y-m-d H:i:s");
if(isset($_GET['ke']))
{
$ke = $_GET['ke'];
}
else
{
$ke = 0;
$sql = "update `siswa` set `nis` = '1' where `rombel` = '$ruang'";
$insert = $db->query($sql);
}
if(isset($_GET['tanggal']))
{
$tanggal = $_GET['tanggal'];
}
else
{
$tanggal = date("Y-m-d");
}

$year = substr($tanggal,0,4);
$month = substr($tanggal,5,2); // February
$day = substr($tanggal,8,2);
if (checkdate($month, $day, $year)) {

} else {
die('tanggal salah');
}
$qs = $db->query("SELECT * FROM `siswa` WHERE `rombel` = '$ruang' limit $ke,1");
if(mysqli_num_rows($qs) == 0)
{
	 ?>
		<script>setTimeout(function () {
		 window.location.href= 'siswa.php';
			},<?php echo $waktu;?>);
			</script>
		<?php
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Memeriksa peseta belum tes</title>
<link rel="stylesheet" href="../css/style.css">
<?php
while($ds = mysqli_fetch_assoc($qs))
{
	$kelas = $ds['kelas'];
	$id_siswa = $ds['id_siswa'];

	$qua = $db->query("SELECT * FROM `ujian_aktif` WHERE `kelas` = '$kelas' and `tanggal` > '$tanggal_awal' and `tanggal` < '$skr'");
	$cacah_tes = mysqli_num_rows($qua);
	$dikerjakan = 0;
	while($dua = mysqli_fetch_assoc($qua))
	{
		$id_ujian = $dua['id_ujian'];
		$qu = $db->query("SELECT * FROM `ujian` WHERE `id_ujian` = '$id_ujian' and `id_siswa` = '$id_siswa'");
		if(mysqli_num_rows($qu) == 0)
		{
			$stmt = $db->prepare(
                            "INSERT IGNORE INTO `siswa_susulan`
                             (`id_siswa`, `id_ujian`)
                             VALUES (?, ?)"
                        );
                        if (!$stmt) {

                            echo 'Prepare insert gagal: '
                                . htmlspecialchars($db->error)
                                . '<br>';

                            die();
                        }


                        $stmt->bind_param(
                            'ii',
                            $id_siswa,
                            $id_ujian
                        );
                         if ($stmt->execute()) {

                            if ($stmt->affected_rows > 0) {

                                echo '→ Ditambahkan ke peserta susulan'
                                    . '<br>';

                            } else {

                                echo '→ Sudah terdaftar sebagai susulan'
                                    . '<br>';
                            }
				$sql = "update `siswa` set `nis` = '0' where `id_siswa` = '$id_siswa'";
				$insert = $db->query($sql); 	
                        } else {

                            echo '→ Gagal insert: '
                                . htmlspecialchars($stmt->error)
                                . '<br>';
                        }

		}
	}
	echo $ds['nama_siswa'].' cacah tes '.$cacah_tes.' '.$dikerjakan.'<br />';
	$ke++;
	?>
		<script>setTimeout(function () {
		 window.location.href= 'belum_tes_ruang_ini.php?ke=<?php echo $ke;?>';
			},<?php echo $waktu;?>);
			</script>
		<?php
}
?>



