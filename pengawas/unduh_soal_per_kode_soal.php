<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

// dibuat oleh Selamet Hanafi
// selamet.hanafi@gmail.com
// www.sianis.web.id
?>
<?php
$tahun = cari_thnajaran();
$semester = cari_semester();

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
$ta = $db->query("SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'app_key_server_cbt_lokal'");
$da = mysqli_fetch_assoc($ta);
$key = $da['konfigurasi_isi'];
$ta = $db->query("SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'url_bank_soal'");
$da = mysqli_fetch_assoc($ta);
$url_bank_soal = $da['konfigurasi_isi'];
//echo $key.' '.$sianis;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Unduh Soal</title>
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
	echo 'Silakan memilih <h1><a href="unduh_soal_per_kode_soal.php?jenis=sim&id=0">Simulasi</a> <a href="unduh_soal_per_kode_soal.php?jenis=pht&id=0">PHT</a>  <a href="unduh_soal_per_kode_soal.php?jenis=pas&id=0">PAS</a>  <a href="unduh_soal_per_kode_soal.php?jenis=uma&id=0">Asesmen Madrasah</a></h1>';
	die();
}
$tunjukkan_hasil = '0';
if((!empty($key)) and (!empty($url_bank_soal)))
{
	$ta = $db->query("SELECT * FROM `ujian_aktif` where `tahun` = '$tahun' and `semester` = '$semester' and `kode_soal` like '$jenis%'");
	$cacah = mysqli_num_rows($ta);
	if($id < $cacah )
	{
		$ta = $db->query("SELECT * FROM `ujian_aktif` where `tahun` = '$tahun' and `semester` = '$semester' and `kode_soal` like '$jenis%' limit $id,1");
		while($da = mysqli_fetch_assoc($ta))
		{
			$kode_soal = $da['kode_soal'];
			$id_ujian = $da['id_ujian'];
			//ambil cacah_soal
			$url = $url_bank_soal.'/tukardata/cacah_soal_json.php?app_key='.$key.'&kode_soal='.$kode_soal;
			//echo $url;
			$json = via_curl($url);
			$cacah_soal = 0;
			if($json)
			{
			       	foreach($json as $dm)
				{
					$cacah_soal = $dm['cacah'];
				}
			}
			else
			{
				echo 'gagal terhubung dengan bank soal '.$url;
				die();
			}
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
			echo 'Terproses '.$id.' tes dari '.$cacah.' tes<br />';
			echo 'Nama Tes '.$da['nama_soal'].'<br />';
			echo 'cacah_soal '.$cacah_soal.'<br />';
			$url2 = $url_bank_soal.'/tukardata/soal_per_kode_soal_json.php?app_key='.$key.'&kode_soal='.$kode_soal;
//			die($url2);
			$json2 = via_curl($url2);
			if(!$json2)
			{
				die('tidak dapat mengunduh soal '.$url2);
			}
			$db->query("delete FROM `soal` where `kode_soal` = '$kode_soal'");
	        	foreach($json2 as $dms)
			{
				$pesan = clean($dms['pesan']);
				if($pesan == 'ada')
				{
					// Ambil dari array dms
					$id_soal        = clean($dms['id_soal']);
					$nomer_soal     = clean($dms['nomer_soal']);
					$kode_soal      = clean($dms['kode_soal']);
					$pertanyaan     = clean($dms['pertanyaan']);
					$tipe_soal      = clean($dms['tipe_soal']);
					$pilihan_1      = clean($dms['pilihan_1']);
					$pilihan_2      = clean($dms['pilihan_2']);
					$pilihan_3      = clean($dms['pilihan_3']);
					$pilihan_4      = clean($dms['pilihan_4']);
					$pilihan_5      = clean($dms['pilihan_5']);
					$jawaban_benar  = clean($dms['jawaban_benar']);
					$status_soal    = clean($dms['status_soal']);
					$created_at     = clean($dms['created_at']); // bisa s&j default juga
					//'pg','','bs','jodohkan','uraian'
					//
					$pasangan = '';
					$simpan = 0;
					if($tipe_soal == 'Pilihan Ganda')
					{
						$tipe_soal = 'pg';
						$simpan++;
						if($jawaban_benar == 'pilihan_1')
						{
							$jawaban_benar = 'A';
						}
						if($jawaban_benar == 'pilihan_2')
						{
							$jawaban_benar = 'B';
						}
						if($jawaban_benar == 'pilihan_3')
						{
							$jawaban_benar = 'C';
						}
						if($jawaban_benar == 'pilihan_4')
						{
							$jawaban_benar = 'D';
						}
						if($jawaban_benar == 'pilihan_5')
						{
							$jawaban_benar = 'E';
						}
						
												
					}
					elseif($tipe_soal == 'Uraian')
					{
						$tipe_soal = 'uraian';
						$simpan++;						
					}
					elseif($tipe_soal == 'Pilihan Ganda Kompleks')
					{
						$tipe_soal = 'pg_kompleks';
						$map = [
						    'pilihan_1' => 'A',
						    'pilihan_2' => 'B',
						    'pilihan_3' => 'C',
						    'pilihan_4' => 'D',
						    'pilihan_5' => 'E'
						];
						$input = $jawaban_benar;
						$arr = explode(',', $input);
						$hasil = [];
						foreach ($arr as $p) {
						    $p = trim($p);
						    if (isset($map[$p])) {
						        $hasil[] = $map[$p];						
						    }
						}
						$jawaban_benar = implode(',', $hasil);
//						die($tipe_soal.' '.$url2);
						$simpan++;					
					}
					elseif($tipe_soal == 'Benar/Salah')
					{
						$tipe_soal = 'bs';
						$kunci_lama = $jawaban_benar;
						$items = explode('|', $kunci_lama);
						$huruf = ['A','B','C','D','E'];
						$hasil = [];
						foreach ($items as $i => $v) {
						    $kode = $huruf[$i];
						    $nilai = (trim($v) === 'Benar') ? 'B' : 'S';
						    $hasil[] = $kode . ':' . $nilai;
						}
						$jawaban_benar = implode(',', $hasil);

						$simpan++;
					}
					elseif($tipe_soal == 'Menjodohkan')
					{
						$tipe_soal = 'menjodohkan';
						$simpan++;						
					}
					else
					{
						die($tipe_soal.' '.$url2);
					}
					$sql = "INSERT INTO soal 
(id, id_ujian,nomer_soal, kode_soal, soal, tipe, a, b, c, d, e, pasangan, kunci)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
if($simpan > 0)
					{
						$stmt = $db->prepare($sql);
						$stmt->bind_param("iiissssssssss", $id_soal, $id_ujian, $nomer_soal, $kode_soal, $pertanyaan, $tipe_soal, $pilihan_1, $pilihan_2, $pilihan_3, $pilihan_4, $pilihan_5, $pasangan ,$jawaban_benar);
						if ($stmt->execute()) {
						    //echo "Insert sukses";
						} else {
						    echo "Error: " . $stmt->error;
						    die();
						}
						$stmt->close();
						}
				}
		
			}
			$id++;
			$lanjut = 'unduh_soal_per_kode_soal.php?jenis='.$jenis.'&id='.$id;
			?>
					<script>setTimeout(function () {
						   window.location.href= '<?php echo $lanjut;?>';
					},10);
					</script>
					<?php
		}
	}
	else
	{
	echo 'Rampung';
	?>
					<script>setTimeout(function () {
						   window.location.href= '../pengawas/daftar_tes.php';
					},2000);
					</script>
					<?php
	}
}
?>
</body>
</html>
