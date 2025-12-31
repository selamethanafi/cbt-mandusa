<?php
include 'config.php';
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

while($u = $q->fetch_assoc()){
?>
<a href="token.php?id=<?= $u['id'] ?>" class="btn btn-primary mb-2">
<?= $u['nama_ujian'] ?>
</a>
<?php } ?>
