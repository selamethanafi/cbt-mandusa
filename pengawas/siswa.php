<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$semester = cari_semester();
$ajaran = cari_thnajaran();

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
        <th>Nomor Peserta</th>
    <th>Nama Siswa</th>
    <th style="width:90px;">Kelas</th>
        <th>Sinkron</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
while($r = $q->fetch_assoc()){
?>
<tr>
    <td><?= $no++ ?></td>
    <td class="text-start"><?= htmlspecialchars($r['username']) ?></td>    
    <td class="text-start"><?= htmlspecialchars($r['nama_siswa']) ?></td>
    <td><?= $r['kelas'] ?></td>
    <td><a href="sinkron_siswa.php?id=<?= $r['id_siswa'];?>">Sinkron</a></td>
</tr>
<?php } ?>
</tbody>
</table>

  
</body>

</html>
