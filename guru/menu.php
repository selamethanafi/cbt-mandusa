<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/guru.php';
$username = $_SESSION['nip'];
$tanda =$_SESSION['tanda'];
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
<h4 class="mb-2">Menu</h4>
<?php
    $tc = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='url_bank_soal'");
    $dc = mysqli_fetch_assoc($tc);
    $url_bank_soal = $dc['konfigurasi_isi'] ?? '';
    $url = $url_bank_soal.'/tukardata/user_id.php';
    // pakai cURL
    $data = [
    'username' => $username,
	];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response2 = curl_exec($ch);
    curl_close($ch);
    $result2 = json_decode($response2, true);
    curl_close($ch);
    foreach($result2 as $dt2)
	{
	    if($dt2['pesan'] == 'oke')
	    {	
		    $user_id = $dt2['user_id'];
		}
		else
		{
			$user_id = 0;
		}
		}

$ta = $db->query("delete from `guru` where `username` = '$username'");
$db->query("insert into `guru` (`user_id`,`username`) values ('$user_id','$username')");
if(empty($user_id))
{
	echo '<h1>Belum sinkron, hubungi admin</h1>';
}

?>
<a href="logout.php">Log out</a> <a href="soal_hari_ini.php">Daftar Tes</a> 
<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:10%;">Nomor</th>
    <th style="width:20%;">Kode Tes</th>
    <th style="width:30%;">Nama Tes</th>
    <th style="width:20%;">Peserta Susulan</th>
    <th style="width:10%;">Aktifkan Tes</th>    
</tr>
</thead>
<tbody>
<?php
$td = $db->query("SELECT * from `ujian_aktif` where `user_id` = '$user_id'");
$no = 1;
while($dd=mysqli_fetch_assoc($td))
{
	echo '<tr><td>'.$no.'</td><td>'.$dd['kode_soal'].'</td><td>'.$dd['nama_soal'].'</td><td><a href="susulan.php?kode_soal='.$dd['kode_soal'].'">Proses</a></td><td><a href="jadwalkan.php?id_ujian='.$dd['id_ujian'].'">Jadwalkan Hari ini</a></td></tr>';
	$no++;
}
?>
</tbody>
</table>
</div>
</div>

</body>
</html>

