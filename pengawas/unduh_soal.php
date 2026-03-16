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

if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
else
{
	$id = '';
}
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='url_bank_soal'");
$da = mysqli_fetch_assoc($ta);
$url_bank_soal = $da['konfigurasi_isi'] ?? '';
$url_cbt = '..';

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
$tunjukkan_hasil = '0';
if((!empty($key)) and (!empty($url_bank_soal)))
{
$ta = $db->query("SELECT * FROM `ujian_aktif` where `id_ujian` = '$id'");
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
		$url2 = $url_bank_soal.'/tukardata/soal_per_kode_soal_json.php?app_key='.$key.'&kode_soal='.$kode_soal;
//		die($url2);
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
					$pertanyaan = str_replace($url_bank_soal,$url_cbt,$pertanyaan);
					$pertanyaan = str_replace($url_bank_soal,$url_cbt,$pertanyaan);
					$tipe_soal      = clean($dms['tipe_soal']);
					$pilihan_1      = clean($dms['pilihan_1']);
					$pilihan_1 = str_replace($url_bank_soal,$url_cbt,$pilihan_1);
					$pilihan_2      = clean($dms['pilihan_2']);
					$pilihan_2 = str_replace($url_bank_soal,$url_cbt,$pilihan_2);
					$pilihan_3      = clean($dms['pilihan_3']);
					$pilihan_3 = str_replace($url_bank_soal,$url_cbt,$pilihan_3);
					$pilihan_4      = clean($dms['pilihan_4']);
					$pilihan_4 = str_replace($url_bank_soal,$url_cbt,$pilihan_4);
					$pilihan_5      = clean($dms['pilihan_5']);
					$pilihan_5 = str_replace($url_bank_soal,$url_cbt,$pilihan_5);
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
						echo '<h1>ada tipe soal yang tidak sesuai ketentuan</h1>';
						die($tipe_soal.' '.$url2);
				}
				if(empty($jawaban_benar))
				{
						if($tipe_soal == 'uraian')
						{
						}
						else
						{
							echo '<h1>kunci kosong</h1>';
							die('nomor '.$nomer_soal.' '.$url2);
						}
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
	}
}
			?>
					<script>setTimeout(function () {
						   window.location.href= 'view_soal.php?id_ujian=<?= $id;?>';
					},2000);
					</script>
					<?php

?>
</body>
</html>
