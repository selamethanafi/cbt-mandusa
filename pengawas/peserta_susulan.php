<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$semester = cari_semester();
$ajaran = cari_thnajaran();
$query= "SELECT * from siswa where `nis` = '0' ORDER BY nama_siswa";
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
    <th style="width:90px;">Kelas</th>
    <th>Hasil</th>
    <th>Jadwalkan Susulan</th>    
    <th>Kode QR</th>
	<th>Sinkron</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
while($r = $q->fetch_assoc())
{
	$nama_siswa = $r['nama_siswa'];
	?>
	<tr>
   		 <td><?= $no++ ?></td>
		    <td class="text-start"><?= htmlspecialchars($nama_siswa) ?></td>    
		    <td class="text-start"><?= htmlspecialchars($r['username']) ?></td>    
		        <td><?= $r['kelas'] ?></td>
	<?php
    		echo '<td><a class="btn btn-primary" href="hasil_siswa.php?id='.$r['id_siswa'].'">Lihat Hasil</a></td>';
    		echo '<td class="text-start">';
    		?>
    		<a class="btn btn-primary" href="ikutkansusulan.php?id_siswa=<?= $r['id_siswa'];?>">SUSULAN</a>
    		<?php
    		echo '</td><td>';
    		?>
    		<a href="generate_qrcode.php?nopes=<?= urlencode($r['username']) ?>&kode=<?= urlencode($r['password']) ?>"
       class="btn btn-success btn-sm"
       target="_blank">
        QR Code
	    </a>
	    	<?php
	    	echo '</td>';
 ?>
 <td><a class="btn btn-primary" href="sinkron_siswa.php?id=<?= $r['id_siswa'];?>">Sinkron</a></td>
		<?php
    	
}
?>
</tbody>
</table>

  
</body>

</html>
