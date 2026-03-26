<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

// dibuat oleh Selamet Hanafi
// selamet.hanafi@gmail.com
// www.sianis.web.id
?>
<?php
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
else
{
	$id = '';
}
if(isset($_GET['hal']))
{
	$hal = $_GET['hal'];
}
else
{
	$hal = '';
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
    <title>Sinkron Jadwal</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
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
				mysqli_query($db,"delete from `ujian_aktif` where `id_ujian` = '$id_soal'");
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
		header('Location: daftar_tes.php?hal='.$hal);
	}
	else
	{
		echo $url;
		die('gagal tersambung ke bank soal');
	}
}
else
{
	header('Location: menu.php');
	exit;
}
?>
</body>
</html>
