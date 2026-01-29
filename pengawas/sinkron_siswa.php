<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
if(isset($_GET['id']))
{
    $id = $_GET['id'];
    $ta = $db->query("SELECT * FROM siswa where `id_siswa` = '$id'");
    if(mysqli_num_rows($ta) > 0)
    {
	$url = $sianis.'/cbtzya/updatepeserta/'.$key.'/'.$id;
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
				        // Enkripsi password
					$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
					$encrypted = openssl_encrypt($password, $method, $rahasia, 0, $iv);
					$final = base64_encode($iv . $encrypted);
					$db->query("UPDATE `siswa` SET `nama_siswa`= '$nama_siswa',`password`='$final',`username`= '$username', `kelas`= '$kelas',`rombel`='$ruang' WHERE `id_siswa` = '$id'");
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


