<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Menu</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<?php
if(isset($_GET['id']))
{
    $id = $_GET['id'];
    $ta = $db->query("SELECT * FROM siswa where `id_siswa` = '$id'");
    if(mysqli_num_rows($ta) > 0)
    {
	$url = $sianis.'/cbt/updatepeserta/'.$key.'/'.$id;
	if((!empty($key)) and (!empty($sianis)))
	{
		$json = via_curl($url);    
		if($json)
		{
		       	foreach($json as $dm)
			{
				$pesan = $dm['pesan'];
				if($pesan == 'ada')
				{
					$username = $dm['nopes'];
					$password = $dm['password'];
					$nama_siswa = $dm['nama'];
					$kelas = $dm['nama_kelas'];
					$ruang = $dm['ruang'];
					$versi= clean($dm['versi']);
					$ta = $db->query("select * from `siswa` where `username` = '$username'");
					$da =  mysqli_fetch_assoc($ta);
					$nis_lama = $da['id_siswa'];
					$ada = mysqli_num_rows($ta) ;
					if($nis_lama == $id)
					{
						$db->query("UPDATE `siswa` SET `nama_siswa`= '$nama_siswa',`password`='$password',`username`= '$username', `kelas`= '$kelas', `nis` = '$versi' WHERE `id_siswa` = '$id'");
						echo 'sukses memperbarui data siswa';
											?>
					<script>
					// Auto redirect setelah 2 detik
					setTimeout(function(){
					    window.location.href = 'siswa.php';
					}, 2000);
					</script>
					<?php		

					}
					else
					{
						echo 'ada nopes kembar '.$username;
						echo ' gagal lakukan unduh peserta ';
						?>
						<a class="btn btn-primary" href="sinkron_peserta.php" onclick="return confirm('Yakin mengunduh peserta semua?')">Unduh Semua Peserta</a>
						<?php
					
					}
				}
			}
		}
		else
		{
			echo 'gagal tersambung dengan sistem informasi madrasah ';
			?>
			<script>
// Auto redirect setelah 2 detik
setTimeout(function(){
    window.location.href = 'siswa.php';
}, 2000);
</script>
<?php		
		}
			
	} 
	else
	{
	    echo 'periksa parameter sambungan ke sistem informasi madrasah';
?>
<script>
// Auto redirect setelah 2 detik
setTimeout(function(){
    window.location.href = 'siswa.php';
}, 2000);
</script>
<?php
	} 
	}
	else
	{
	 echo 'siswa tidak ada di ruang ini';
?>
<script>
// Auto redirect setelah 2 detik
setTimeout(function(){
    window.location.href = 'siswa.php';
}, 2000);
</script>
<?php
	}  
}
else
{
    echo 'id siswa kosong';
    ?>
<script>
// Auto redirect setelah 2 detik
setTimeout(function(){
    window.location.href = 'siswa.php';
}, 2000);
</script>
<?php
}

?>
</div></body></html>
