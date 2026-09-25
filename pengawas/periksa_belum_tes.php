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
$qs = $db->query("SELECT * FROM `siswa` WHERE `rombel` = '$ruang' limit $ke,1");
if(mysqli_num_rows($qs) == 0)
{
	echo 'Rampung';
	echo '
	<br /><a href="menu.php">Kembali ke Menu</a>';
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
	$url = $sianis.'/cbt/caritesbelum';
	$params=[
			'app_key'=>$key,
			'nis' => $id_siswa,
			];
	if($json = postcurl($url,$params))
	{
		print_r($json);
		$data = json_decode($json, true);
		$pesan = $data[0]['pesan']; // oke
		$ket = $data[0]['ket'];   // 1
		$sql = "update `siswa` set `nis` = '$ket' where `id_siswa` = '$id_siswa'";
		$insert = $db->query($sql); 
	}
	echo $ds['nama_siswa'].' <br />';
	$ke++;
	?>
		<script>setTimeout(function () {
		 window.location.href= 'periksa_belum_tes.php?t&ke=<?php echo $ke;?>';
			},<?php echo $waktu;?>);
			</script>
		<?php
}
?>



