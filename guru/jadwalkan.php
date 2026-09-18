<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/guru.php';
$username = $_SESSION['nip'];
$waktu = date("Y-m-d H:i:s");
if(isset($_GET['id_ujian']))
{
	$id_ujian = $_GET['id_ujian'];
	}
else
{
	die('kode soal tidak boleh kosong');
}
$user_id = 0;
$ta = $db->query("SELECT * from `guru` where `username` = '$username'");
$ada = mysqli_num_rows($ta);
if($ada == 0)
{
	$db->query("insert into `guru` (`username`) values ('$username')");
}
else
{
	$da = mysqli_fetch_assoc($ta);
	$user_id = $da['user_id'] ?? 0;
}
if(empty($user_id))
{
	echo '<h1>Belum sinkron, hubungi admin</h1>';
	die();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Susulan Hari ini</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
                                <?php
                                echo $waktu;
				        $date = new DateTime($waktu);
					$date->modify('+1 month');
					$tanggal_selesai = $date->format('Y-m-d H:i:s');
				        $sql = "UPDATE ujian_aktif SET  tanggal = ?, tanggal_selesai = ? WHERE id_ujian = ?";
					$stmt = $db->prepare($sql);
					if (!$stmt) {
					    die("Prepare error: " . $db->error);
					}
					$stmt->bind_param("sss", $waktu,$tanggal_selesai,$id_ujian);
					$stmt->execute();
					if (!$stmt->execute()) 
					{
						die("Execute error: " . $stmt->error);
					}
					if ($stmt->affected_rows > 0) 
					{
					    echo "Data berhasil diupdate";
					} else {
					    //echo "Tidak ada perubahan";
					}
		echo '<a href="soal_hari_ini.php">Aktifkan Tes</a>';
?>
</div>
</body>

</html>
