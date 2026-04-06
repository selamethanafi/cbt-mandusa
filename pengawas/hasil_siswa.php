<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$semester = cari_semester();
$ajaran = cari_thnajaran();
$id_siswa = $_GET['id'] ?? '';

$query= "SELECT * from siswa where `id_siswa` = '$id_siswa'";

$q = $db->query($query);
$qn = $db->query("
    SELECT 
        u.id_ujian,
        ua.kode_soal,
        ua.nama_soal,
        ua.mapel,
        n.nilai
    FROM ujian u
    LEFT JOIN nilai n 
        ON u.id_siswa = n.id_siswa 
        AND u.id_ujian = n.id_ujian
    LEFT JOIN ujian_aktif ua
        ON u.id_ujian = ua.id_ujian
    WHERE u.id_siswa = '$id_siswa'
");

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Hasil Siswa</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
 <div class="card">
 <?php
$r = $q->fetch_assoc();
$nama_siswa = $r['nama_siswa'];
echo $nama_siswa;
?>
<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:40px;">No</th>
    <th>Ujian</th>
    <th>Nilai</th>
    <th>Koreksi</th>
    <th>Kirim</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
while($n = $qn->fetch_assoc()){
?>
<tr>
    <td><?= $no++ ?></td>
    <td class="text-start"><?= $n['nama_soal'];?></td>    
    <td class="text-start"><?= $n['nilai']; ?></td>    
    <td><a href="koreksi.php?id_siswa=<?= $id_siswa;?>&id_ujian=<?= $n['id_ujian'];?>">Koreksi</a></td>
    <td><a href="kirim_per_siswa.php?id_siswa=<?= $id_siswa;?>&id_ujian=<?= $n['id_ujian'];?>" target="_blank">Kirim</a></td>
</tr>
<?php } ?>
</tbody>
</table>

  
</body>

</html>

