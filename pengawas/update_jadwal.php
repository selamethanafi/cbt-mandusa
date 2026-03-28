<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

// dibuat oleh Selamet Hanafi
// selamet.hanafi@gmail.com
// www.sianis.web.id
?>
<?php
if(isset($_GET['ke']))
{
	$ke = $_GET['ke'];
}
else
{
	$ke = 0;
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
<head><meta charset="UTF-8"><title>Pemutakhiran Jadwal</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
$ta = $db->query("SELECT * FROM `ujian_aktif`");
$total = mysqli_num_rows($ta);
$tb = $db->query("SELECT * FROM `ujian_aktif` order by `id_ujian` limit $ke,1");
$ada = mysqli_num_rows($tb);
if($ada > 0)
{
	$data = mysqli_fetch_assoc($tb);
	$id = $data['id_ujian'];
	if((!empty($key)) and (!empty($url_bank_soal)))
	{
		$url = $url_bank_soal.'/tukardata/jadwal_json.php?app_key='.$key.'&id='.$id;
		$json = via_curl($url);
		if($json)
		{
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
					$sql = "UPDATE ujian_aktif SET kode_soal = ?, nama_soal = ?, mapel = ?, kelas = ?, waktu_ujian = ?, tanggal = ?, status = ?, tampilan_soal = ?, kunci = ?, token = ?, user_id = ?, exambrowser = ?, tahun = ?, semester = ?, tanggal_selesai = ? WHERE id_ujian = ?";
					$stmt = $db->prepare($sql);
					if (!$stmt) {
					    die("Prepare error: " . $db->error);
					}
					$stmt->bind_param("ssssisssssiisssi", $kode_soal,$nama_soal,$mapel,$kelas,$waktu_ujian,$tanggal,$status,$tampilan_soal,$kunci,$token,$user_id,$exambrowser,$tahun,$semester,$tanggal_selesai,$id);
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

					$ke++;
				    $progress = ($total > 0) ? round(($ke / $total) * 100) : 0;
?>
<div class="container-fluid">
<div class="progress" style="width: 300px;">
  <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="<?= $total;?>">
<?= $progress ?>%
  </div>
</div>
</div>
<?php
			echo 'Terproses '.$ke.' tes dari '.$total.' tes<br />';

			$lanjut = 'update_jadwal.php?ke='.$ke;
			?>
					<script>setTimeout(function () {
						   window.location.href= '<?php echo $lanjut;?>';
					},10);
					</script>
					<?php
				}
			}
		}
		else
		{
			echo $url;
			die('gagal tersambung ke bank soal');
		}
	}
	else
	{
		echo 'tidak tersambung periksa key';
		die();
	}
}
else
{
	$lanjut = 'daftar_tes_per_tanggal.php';
			?>
					<script>setTimeout(function () {
						   window.location.href= '<?php echo $lanjut;?>';
					},10);
					</script>
					<?php
				}
				
?>
</body>
</html>
