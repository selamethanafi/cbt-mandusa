<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$waktu = 10;
if(isset($_GET['ke']))
{
$ke = $_GET['ke'];
}
else
{
$ke = 0;
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
		 window.location.href= 'kirim_berita_acara.php?tanggal=<?php echo $tanggal;?>';
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

	$qua = $db->query("SELECT * FROM `ujian_aktif` WHERE `kelas` = '$kelas' and `tanggal` like '$tanggal%'");
	$cacah_tes = mysqli_num_rows($qua);
	$dikerjakan = 0;
	while($dua = mysqli_fetch_assoc($qua))
	{
		$id_ujian = $dua['id_ujian'];
		$qu = $db->query("SELECT * FROM `ujian` WHERE `id_ujian` = '$id_ujian' and `id_siswa` = '$id_siswa'");
		$dikerjakan = $dikerjakan + mysqli_num_rows($qu);
	}
	if($dikerjakan < $cacah_tes)
	{
		$token = substr(str_shuffle('123456789'), 0, 6);
		$sql = "update `siswa` set `password` = '$token', `nis` = '0' where `id_siswa` = '$id_siswa'";
		$insert = $db->query($sql); 
		$url = $sianis.'/cbt/updatepassword';
		$params=[
			'app_key'=>$key,
			'password' => $token,
			'nis' => $id_siswa,
			];
		if($hasil = postcurl($url,$params))
		{
			echo ' Jawaban dari Simamad '.$hasil.'<br />';
		}
	}
	echo $ds['nama_siswa'].' cacah tes '.$cacah_tes.' '.$dikerjakan.'<br />';
	$ke++;
	?>
		<script>setTimeout(function () {
		 window.location.href= 'belum_tes.php?tanggal=<?php echo $tanggal;?>&ke=<?php echo $ke;?>';
			},<?php echo $waktu;?>);
			</script>
		<?php
}
?>



