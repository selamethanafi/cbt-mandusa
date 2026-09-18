<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/guru.php';
$username = $_SESSION['nip'];
$tanggal = date("Y-m-d");
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
}

$query= "SELECT ua.id_ujian,
    ua.kode_soal,
    ua.nama_soal,
    ua.mapel, ua.kelas,ua.tampilan_soal, ua.status, ua.token,
    ua.waktu_ujian,
    ua.tanggal,
    COUNT(s.id) AS cacah_soal
FROM ujian_aktif ua 
LEFT JOIN soal s 
    ON s.id_ujian = ua.id_ujian where ua.user_id = '$user_id' and ua.tanggal like '$tanggal%'
GROUP BY 
    ua.id_ujian,
    ua.kode_soal,
    ua.nama_soal,
    ua.mapel,
    ua.waktu_ujian,
    ua.tanggal
ORDER BY ua.tanggal DESC;
";
$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    // If there's an error with the query, display the error message
    die('Error with the query: ' . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Daftar Tes Hari ini</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
<h3>Klik kode soal untuk monitoring, klik mapel untuk mengembalikan data ujian semula</h3>

                                    <table id="soalTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode Soal</th>
                                                <th>Nama Tes</th>
                                                <th>Kelas</th>
                                                <th>Tampilan</th>
                                                <th>Status</th>
                                                <th>Token</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo '<a href="monitor.php?id_ujian='.$row['id_ujian'].'">'.$row['kode_soal'].'</a>'; ?></td>
                                                <td><?php echo '<a href="sinkron_jadwal.php?id_ujian='.$row['id_ujian'].'">'.$row['nama_soal']; ?></a></td>
                                                <td><?php echo $row['kelas']; ?></td>
                                                <td><?php echo $row['tampilan_soal']; ?></td>
                                                <td><?php echo $row['status'];?>
                                                </td>
                                                <td><?php echo $row['token']; ?><a href="aktifkan_tes.php?id_ujian=<?= $row['id_ujian'];?>"> Aktfikan</a></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
</div>
</body>

</html>
