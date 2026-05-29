<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$semester = cari_semester();
$ajaran = cari_thnajaran();
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
	// Update data soal
	$nis = $_POST['nis'];
	$ta  = $db->query("select * from `siswa` where `id_siswa` = '$nis'");
	if(mysqli_num_rows($ta) == 0)
	{
		$url = $sianis.'/cbt/updatepeserta/'.$key.'/'.$nis;
		//echo $url;
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
						if ($nama_siswa && $username && $password && $kelas && $ruang)
						{
							$stmt = $db->prepare("INSERT INTO siswa (id_siswa, nama_siswa, username, password, kelas, rombel, nis) VALUES (?,?,?,?,?,?,?)");
							$stmt->bind_param("sssssss", $nis, $nama_siswa, $username, $password, $kelas, $ruang,$versi);
							$stmt->execute();
					    	}
					}
				}
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
				echo 'gagal tersambung dengan sistem informasi madrasah ';
				?>
				<script>
				// Auto redirect setelah 2 detik
				setTimeout(function(){
				    window.location.href = 'tambah_siswa.php';
				}, 2000);
				</script>
				<?php
			}


		}
		else
		{
			die('cek key dan url sim');
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Siswa</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
 <div class="card">
<div class="card-body">
<form method="POST">
<div class="mb-3">
<label for="nopes" class="form-label">NIS</label>
<input type="text" class="form-control" id="nis" name="nis" required autofocus>
</div>
<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Tambah Peserta</button>
									
</form>
</div></div>
 
</body>

</html>
