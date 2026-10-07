<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$db->set_charset('utf8mb4');
$waktu = 10;
if(isset($_GET['ke']))
{
$ke = $_GET['ke'];
}
else
{
	$ke = 0;
}
if(isset($_GET['ruang']))
{
$ruang = $_GET['ruang'];
}
else
{
	$ruang = '';
}
if(empty($ruang))
{
	$qs = $db->query("SELECT * FROM `siswa` limit $ke,1");
}
else
{
	$qs = $db->query("SELECT * FROM `siswa` where `rombel` = '$ruang' limit $ke,1");
}
if(mysqli_num_rows($qs) == 0)
{
	 ?>
		<script>setTimeout(function () {
		 window.location.href= 'hutang_tes.php?ruang=<?php echo $ruang;?>';
			},<?php echo $waktu;?>);
			</script>
		<?php
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Memeriksa peseta belum tes atau mengirimkan tugas</title>
<link rel="stylesheet" href="../css/style.css">
<?php

while($ds = mysqli_fetch_assoc($qs))
{
	$nis = $ds['id_siswa'];
	// =====================================================
	// NIS yang akan dicari
	// =====================================================
	$nis = trim($nis);
	if ($nis === '') {
	    die('NIS belum diisi');
	}
	// =====================================================
	// PANGGIL ENDPOINT JSON
	// =====================================================
	$url = $sianis.'/cbt/siswatugasbelum/'.urlencode($nis);
	//echo $url;
	$ch = curl_init($url);
	curl_setopt_array($ch, [
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_TIMEOUT        => 15,
	    CURLOPT_CONNECTTIMEOUT => 5,
	    CURLOPT_HTTPHEADER     => [
	        'Accept: application/json'
	    ]
	]);
	$response = curl_exec($ch);
	if ($response === false) {
	    die('Gagal memanggil API '.$url.': ' . curl_error($ch));
	}
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpCode !== 200) {
	    die('API mengembalikan HTTP ' . $httpCode);
	}
	// =====================================================
	// DECODE JSON
	// =====================================================
	$json = json_decode($response, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
	    die('JSON tidak valid: ' . json_last_error_msg());
	}
	if (empty($json['status'])) {
	    die($json['message'] ?? 'Data tidak ditemukan');
	}
//	echo $url;
	// =====================================================
	// MASUKKAN KE TABEL siswa_absen
	// =====================================================
	if (!empty($json['data'])) {
		$stmt = $db->prepare("
		    INSERT IGNORE INTO `siswa_absen`
		        (`nis`, `tes_id`)
		    VALUES (?, ?)
		");
	    foreach ($json['data'] as $row) {
	        $tes_id    = $row['id_mapel'] ?? '';
	        if (strpos($tes_id, '_') === false) {
		    $hasil = $tes_id;
		}
		else
		{
		        $bagian = explode('_', $tes_id);
			$hasil = $bagian[1];
		}
	        if ($tes_id === '') {
	            continue;
	        }
	        $stmt->bind_param(
	            'ss',
	            $nis,
	            $hasil
		);
	        $stmt->execute();
	    }
	    $stmt->close();
	    	echo "Data berhasil dimasukkan.";
	}	
	else
	{
		echo "Kosong.";
		$db->query("delete from `siswa_absen` where `nis` = '$nis'");
	}
	$ke++;
	?>
		<script>setTimeout(function () {
		 window.location.href= 'hutang_tugas.php?ruang=<?php echo $ruang;?>&ke=<?php echo $ke;?>';
			},<?php echo $waktu;?>);
			</script>
		<?php

}
