<?php
require_once __DIR__ . '../../inc/config.php';
require_once __DIR__ . '../../inc/fungsi.php';
if(!isset($_SESSION['id_siswa'], $_SESSION['kelas'])){
    header("Location: login.php");
    exit;
}
$id_siswa = $_SESSION['id_siswa'];
$kelas_siswa = $_SESSION['kelas'];

$q = $db->query("
SELECT * FROM ujian_aktif
WHERE kelas='$kelas_siswa'
AND status='aktif'
AND NOW() BETWEEN mulai AND selesai
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="30">

<title>CBT Ujian</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<div class="card shadow-sm">
<div class="card-body">
<h4 class="mb-2">Daftar Tes</h4>
<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:10%;">No</th>
    <th>Nama CBT</th>
    <th style="width:10%;">Kelas</th>
    <th style="width:30%;">Pelaksanaan</th>    
    <th style="width:10%;">Durasi</th>
    <th style="width:10%;">Aksi</th>
	</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
while($r = $q->fetch_assoc()){

    
?>
<tr class="text-center">
    <td><?= $no++ ?></td>
    <td class="text-start"><?= htmlspecialchars($r['nama_ujian']) ?></td>
    <td><?= $r['kelas'] ?></td> <td><?= tanggal_jam($r['mulai']) ?> s.d. <?= tanggal_jam($r['selesai']) ?></td>
    <td><?= $r['durasi'] ?> menit</td>
    <td><a href="token.php?id=<?= $r['id'] ?>" class="btn btn-primary mb-2">Kerjakan</a></td>
    </td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>
</div>
</div>
</body>
</html>
