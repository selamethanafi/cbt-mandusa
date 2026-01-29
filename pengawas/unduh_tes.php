<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

// dibuat oleh Selamet Hanafi
// selamet.hanafi@gmail.com
// www.sianis.web.id
?>
<?php
function via_curl($url_ard_unduh)
{
	$file = $url_ard_unduh;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $file);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$xmldata = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($xmldata, true);
	return $json;	
}

if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
else
{
	$id = '';
}
if(isset($_GET['jenis']))
{
	$jenis = $_GET['jenis'];
}
else
{
	$jenis = '';
}

$ta = $db->query( "SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'app_key_server_cbt_lokal'");
$da = mysqli_fetch_assoc($ta);
$key = $da['konfigurasi_isi'];
$ta = $db->query( "SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'url_bank_soal'");
$da = mysqli_fetch_assoc($ta);
$url_bank_soal = $da['konfigurasi_isi'];
//echo $key.' '.$url_bank_soal;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Unduh Tes</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
if(empty($id))
{
	$id = 0;
}
if(empty($jenis))
{
	echo 'Silakan memilih <h1><a href="unduh_tes.php?jenis=sim&id=0">Simulasi</a> <a href="unduh_tes.php?jenis=sum1&id=0">SUM 1</a> <a href="unduh_tes.php?jenis=pht&id=0">PHT</a>  <a href="unduh_tes.php?jenis=pas&id=0">PAS</a>  <a href="unduh_tes.php?jenis=uma&id=0">Asesmen Madrasah</a></h1>';
	die();
}
//echo $key.' '.$url_bank_soal;
if((!empty($key)) and (!empty($url_bank_soal)))
{
	$url = $url_bank_soal.'/tukardata/cacah_ujian.php?app_key='.$key.'&jenis='.$jenis;
	echo '<a href="'.$url.'">cek</a>';
	$json = via_curl($url);
	$cacah = 0;
	if($json)
	{
	       	foreach($json as $dm)
		{
			$cacah = clean($dm['cacah']);
		}
	}
	else
	{
		die('tidak tersambung ke bank soal');
	}
//	echo 'Cacah Tes '.$cacah;
	if($cacah > 0)
	{
		if($id < $cacah )
		{
			$url = $url_bank_soal.'/tukardata/ujian_json.php?app_key='.$key.'&jenis='.$jenis.'&id='.$id;
//			echo $url.'<br />';
//			die();
			$json = via_curl($url);
			
			if($json)
			{
				if($id == 0)
				{
					$db->query("truncate `ujian_aktif`");
					$db->query("truncate `jawaban`");
					$db->query("truncate `nilai`");
					$db->query("truncate `ujian`");					

				}
				//echo 'oke';
				foreach($json as $dm)
				{
					$pesan = clean($dm['pesan']);
					if($pesan == 'ada')
					{
						$id_soal= clean($dm['id_soal']);
						$kode_soal= clean($dm['kode_soal']);
						$nama_soal= clean($dm['nama_soal']);
						$mapel= clean($dm['mapel']);
						$kelas= clean($dm['kelas']);
						$waktu_ujian= clean($dm['waktu_ujian']);
						$tanggal= clean($dm['tanggal']);
						$date = new DateTime($tanggal);
						$date->modify('+1 month');
						$tanggal_selesai = $date->format('Y-m-d H:i:s');
						$status= clean($dm['status']);
						$tampilan_soal= clean($dm['tampilan_soal']);
						$kunci= clean($dm['kunci']);
						$token = clean($dm['token']);
						$user_id= clean($dm['user_id']);
						$exambrowser= clean($dm['exambrowser']);
						$tahun= clean($dm['tahun']);
						$semester= clean($dm['semester']);
						$sql = "INSERT INTO `ujian_aktif` (`id_ujian`, `kode_soal`, `nama_soal`, `mapel`, `kelas`, `waktu_ujian`, `tanggal`, `status`, `tampilan_soal`, `kunci`, `token`, `user_id`, `exambrowser`, `tahun`, `semester`, `tanggal_selesai`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
						$stmt = $db->prepare($sql);
if (!$stmt) {
    die("Prepare error: " . $db->error);
}
			$stmt->bind_param("issssisssssiisss",$id_soal, $kode_soal,$nama_soal, $mapel, $kelas, $waktu_ujian, $tanggal, $status, $tampilan_soal, $kunci, $token, $user_id, $exambrowser, $tahun, $semester, $tanggal_selesai);
			$stmt->execute();
						
					}
				}
				    // Hitung progress
    $progress = ($cacah > 0) ? round(($id / $cacah) * 100) : 0;
?>
<div class="container-fluid">
<div class="progress" style="width: 300px;">
  <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="<?= $total;?>">
<?= $progress ?>%
  </div>
</div>
</div>
<?php
					$id++;
        					echo 'Terproses '.$id.' dari '.$cacah.' tes';
    					    $lanjut = 'unduh_tes.php?jenis='.$jenis.'&id='.$id;
        //					die($lanjut);
                            ?>
	        				<script>setTimeout(function () {
			    			   window.location.href= '<?php echo $lanjut;?>';
				            	},50);
        					</script>
        					<?php


			}
			else
			{
				echo $url;
				die('gagal tersambung ke bank soal');
			}

		}
		else
		{
			//echo 'Rampung';
			header('Location: ../pengawas/unduh_soal_per_kode_soal.php?jenis='.$jenis);
		exit;
		}
	}
}
?>
</body>
</html>
