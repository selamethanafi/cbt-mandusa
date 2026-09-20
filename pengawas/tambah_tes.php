<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$semester = cari_semester();
$ajaran = cari_thnajaran();
$getkodetes = $_GET['kodetes'] ?? '';
if(!empty($getkodetes))
{
	exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
	// Update data soal
	$kodetes = $_POST['kodetes'];
	$ta = $db->query( "SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'app_key_server_cbt_lokal'");
	$da = mysqli_fetch_assoc($ta);
	$key = $da['konfigurasi_isi'];
	$ta = $db->query( "SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'url_bank_soal'");
	$da = mysqli_fetch_assoc($ta);
	$url_bank_soal = $da['konfigurasi_isi'];
	if((!empty($key)) and (!empty($url_bank_soal)))
	{
		$url = $url_bank_soal.'/tukardata/daftar_tes_json.php?app_key='.$key.'&kode='.$kodetes;
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
			
			header("Location: view_soal.php?id_ujian=".$id_soal);
	exit;
		} // json benar
		else
		{
			die('gagal mengunduh');
		}
	} // ada key dan url bank soal
 }

$query= "SELECT * from siswa where `rombel` = '$ruang' ORDER BY nama_siswa";

$q = $db->query($query);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Tambah Tes</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
 <div class="card">
<div class="card-body">
<form method="POST">
<div class="mb-3">
<label for="nopes" class="form-label">Kode Tes</label>
<input type="text" class="form-control" id="nopes" name="kodetes" required autofocus>
</div>
<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Unduh Tes</button>
									
</form>
</div></div>
 
</body>

</html>
