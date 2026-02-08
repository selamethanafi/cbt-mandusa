<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

// ----------------------
// Ambil Total Peserta
// ----------------------
$url = $sianis.'/cbtzya/jml_peserta/'.$key.'/semua';
$json = via_curl($url);
$total = 0;

if($json){
    foreach($json as $dm){
        $total = $dm['cacah'];
    }
}
else
{
	die('tidak terhubung ke sistem informasi madrasah, periksa internet');
}
$id=0;
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
?>
<?php
// Nilai progress (0 - 100)

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Progress Unduh Siswa dari Sistem Informasi Madrasah</title>
<link rel="stylesheet" href="../css/style.css?">
</head>
<body>
<?php
if($id == 0)
{
	$db->query("truncate table `siswa`");
}
echo 'Total = '.$total;
//die('id '.$id.' dari '.$total);
$progress = $id * 100 / $total;
$progress = round($progress);
if($id < $total)
{

    $url = $sianis.'/cbtzya/peserta/'.$key.'/'.$id;
    $json = via_curl($url);
    $pesan = "[Data tidak ditemukan]";
    if($json)
    {
        foreach($json as $dm)
        {
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
			$stmt->bind_param("ssssss", $nis, $nama, $username, $password, $kelas, $rombel);
			$stmt->execute();
		}
	    }
        }
    }
?>
<div class="container-fluid">

<div class="progress">
    <div class="progress-bar"
         role="progressbar"
         style="width: <?= $progress ?>%;"
         aria-valuenow="<?= $progress ?>"
         aria-valuemin="0"
         aria-valuemax="100">
        <?= $progress ?>%
    </div>
</div>

<?php
				$id++;
				$lanjut = 'sinkron_peserta.php?id='.$id;
			?>
					<script>setTimeout(function () {
						   window.location.href= '<?php echo $lanjut;?>';
					},1);
					</script>
					<?php

}
else
{
	echo 'Rampung';
?>
<script>
// Auto redirect setelah 2 detik
setTimeout(function(){
    window.location.href = 'siswa.php';
}, 2000);
</script>
<?php
}
?>
</div>
</body>
</html>

