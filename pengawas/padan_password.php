<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$semester = cari_semester();
$ajaran = cari_thnajaran();
$getnopes = $_GET['nopes'] ?? '';
if(!empty($getnopes))
{
	$db->query("update `siswa` set `rombel` = '' where `username` = '$getnopes'");
	header("Location: siswa.php");
	exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
	// Update data soal
	$nopes = $_POST['nopes'];
	$db->query("update `siswa` set `rombel` = '$ruang' where `username` = '$nopes'");

}

$query= "SELECT * from siswa where `rombel` = '$ruang' ORDER BY nama_siswa";

$q = $db->query($query);
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
<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:40px;">No</th>

    <th>Nama Siswa</th>
    <th>Nomor Peserta</th>
    <th>Password</th>
    <th style="width:90px;">Kelas</th>
        <th>Padan</th>    
        <th>Sinkron</th>

</tr>
</thead>
<tbody>
<?php
$no = 1;
while($r = $q->fetch_assoc()){
$nama_siswa = $r['nama_siswa'];
?>
<tr>
    <td><?= $no++ ?></td>
    <td class="text-start"><?= htmlspecialchars($nama_siswa) ?></td>    
    <td class="text-start"><?= htmlspecialchars($r['username']) ?></td>    
    <td class="text-start"><?php
    if($r['nis'] == 1)
	{
		echo htmlspecialchars($r['password']);
		} ?>
</td>    
    <td><?= $r['kelas'] ?></td>
    <?php
    $padan = 'tidak';
    $id = $r['id_siswa'];
    $url = $sianis.'/cbt/updatepeserta/'.$key.'/'.$id;
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
					if($password == $r['password'])
					{
						$padan = 'padan';
					}
				}
			}
		}
		else
		{
			$padan = 'gagal tersambung dengan sistem informasi madrasah ';
		}
	} 
	?>
	    <td><?= $padan;?>
    <td><a class="btn btn-primary" href="sinkron_siswa.php?id=<?= $r['id_siswa'];?>">Sinkron</a></td>
</tr>
<?php } ?>
</tbody>
</table>

  
</body>

</html>
