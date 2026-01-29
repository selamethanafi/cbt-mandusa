<?php 
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
else
{
	$id = 0;
}
// ----------------------
// Ambil Total Peserta
// ----------------------
$url = $sianis.'/cbtzya/jml_peserta/'.$key.'/'.$ruang;
$json = via_curl($url);
$total = 0;

if($json){
	if($id == 0)
	{
		$db->query("SET FOREIGN_KEY_CHECKS = 0");
		$db->query("truncate `siswa`");
		$db->query("SET FOREIGN_KEY_CHECKS = 1");
	}
foreach($json as $dm){
$total = $dm['cacah'];
}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Unduh Peserta Tes</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
if($id <= $total)
{

$url = $sianis.'/cbtzya/peserta/'.$key.'/'.$id.'/'.$ruang;
$json = via_curl($url);
$pesan = "[Data tidak ditemukan]";

if($json){
foreach($json as $dm){
$pesan = $dm['pesan']; // <-- bisa diganti $dm['nama'] jika tersedia
   $nis  = clean($dm['nisn']);
		$nama = clean($dm['nama']);
		$username = clean($dm['username']);
		$kelas= clean($dm['nama_kelas']);
		$rombel   = clean($dm['ruang']);
		$agen = clean($dm['agen']);
		$versi= clean($dm['versi']);
		$password = clean($dm['password']);
	// Enkripsi password
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
	$encrypted = openssl_encrypt($password, $method, $rahasia, 0, $iv);
	$final = base64_encode($iv . $encrypted);
	if(empty($password))
	{
		die($nama.' password masih kosong, buat dulu');
	}
	if ($nama && $username && $password && $kelas && $rombel)
	{
		// Cek duplikat
		$cek = $db->prepare("SELECT id_siswa FROM siswa WHERE id_siswa = ?");
		$cek->bind_param("s", $nis);
		$cek->execute();
		$cek->store_result();
		if ($cek->num_rows > 0) 
		{
		   $stmt = $db->prepare("UPDATE siswa SET nama_siswa = ?, username = ?, password = ?, kelas = ?, rombel = ? WHERE id_siswa = ? ");
			$stmt->bind_param("ssssss", $nama, $username, $password, $kelas, $rombel, $nis );
			$stmt->execute();
		} else 
		{
			$stmt = $db->prepare("INSERT INTO siswa (id_siswa, nama_siswa, username, password, kelas, rombel) VALUES (?,?,?,?,?,?)");
			$stmt->bind_param("ssssss", $nis, $nama, $username, $final, $kelas, $rombel);
			$stmt->execute();
		}
	}
}
}

// Hitung progress
$progress = ($total > 0) ? round(($id / $total) * 100) : 0;

?>
<div class="container-fluid">
<div class="progress" style="width: 300px;">
  <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="<?= $total;?>">
<?= $progress ?>%
  </div>
</div>
</div>
<?php
$id++;
						$lanjut = 'sinkron_peserta_per_ruang.php?id='.$id;
							?>
							<script>setTimeout(function () {
						   window.location.href= '<?php echo $lanjut;?>';
							},10);
							</script>
							<?php
}
else
{
$lanjut = 'siswa.php';
							?>
							<script>setTimeout(function () {
						   window.location.href= '<?php echo $lanjut;?>';
							},10);
							</script>
							<?php
							}
?>
</body>
</html>

