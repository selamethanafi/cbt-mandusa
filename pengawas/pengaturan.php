<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

/* PROSES UPDATE */
if(isset($_POST['simpan']))
{
    $stmt = $db->prepare("UPDATE cbt_konfigurasi SET konfigurasi_isi=? WHERE konfigurasi_id=?");

    foreach($_POST['isi'] as $id => $isi)
    {
        $stmt->bind_param("si",$isi,$id);
        $stmt->execute();
    }

    echo "<div style='color:green'>Konfigurasi berhasil diperbarui</div>";
}
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='tampil_nilai'");
if(mysqli_num_rows($ta) == 0)
{
	mysqli_query($db,"insert into `cbt_konfigurasi` (`konfigurasi_kode`, `konfigurasi_isi`, `konfigurasi_keterangan`) values ('tampil_nilai', '0', 'Siswa boleh melihat nilai, 1 = boleh, 0 = tidak boleh')");
}
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='kode_qr'");
if(mysqli_num_rows($ta) == 0)
{
	mysqli_query($db,"insert into `cbt_konfigurasi` (`konfigurasi_kode`, `konfigurasi_isi`, `konfigurasi_keterangan`) values ('kode_qr', '0', 'Wajib memakai Kode QR, 1 = ya, 0 = tidak')");
}
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='kamera'");
if(mysqli_num_rows($ta) == 0)
{
	mysqli_query($db,"insert into `cbt_konfigurasi` (`konfigurasi_kode`, `konfigurasi_isi`, `konfigurasi_keterangan`) values ('kamera', 'OFF', 'Monitoring lewat kamera?, ON = ya, OFF = tidak')");
}
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='token_kamera'");
if(mysqli_num_rows($ta) == 0)
{
	mysqli_query($db,"insert into `cbt_konfigurasi` (`konfigurasi_kode`, `konfigurasi_isi`, `konfigurasi_keterangan`) values ('token_kamera', '0', 'Token kamera')");
}
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='ubk_pusat'");
if(mysqli_num_rows($ta) == 0)
{
	mysqli_query($db,"insert into `cbt_konfigurasi` (`konfigurasi_kode`, `konfigurasi_isi`, `konfigurasi_keterangan`) values ('ubk_pusat', '0', 'url ubk pusat')");
}
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='ubk_stream'");
if(mysqli_num_rows($ta) == 0)
{
	mysqli_query($db,"insert into `cbt_konfigurasi` (`konfigurasi_kode`, `konfigurasi_isi`, `konfigurasi_keterangan`) values ('ubk_stream', '', 'url kirim stream')");
}
/* AMBIL DATA */
$result = $db->query("SELECT konfigurasi_id, konfigurasi_kode, konfigurasi_isi, konfigurasi_keterangan 
                        FROM cbt_konfigurasi 
                        ORDER BY konfigurasi_id");
?>

<!DOCTYPE html>
<html>
<head>
<title>Konfigurasi CBT</title>
<style>
table{
    border-collapse: collapse;
}
th,td{
    border:1px solid #ccc;
    padding:8px;
}
th{
    background:#eee;
}
input[type=text]{
    width:100%;
}
</style>
</head>

<body>

<h2>Konfigurasi Sistem CBT</h2>

<form method="post">

<table width="100%">
<tr>
<th>Keterangan</th>
<th>Kode</th>
<th>Nilai</th>

</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>
<td><?= htmlspecialchars($row['konfigurasi_keterangan']) ?></td>
<td><?= htmlspecialchars($row['konfigurasi_kode']) ?></td>
<td>
<input type="text"
       name="isi[<?= $row['konfigurasi_id'] ?>]"
       value="<?= htmlspecialchars($row['konfigurasi_isi']) ?>">
</td>



</tr>

<?php } ?>

</table>

<br>
<button type="submit" name="simpan">Simpan</button> <a href="menu.php">Menu Utama</a>

</form>

</body>
</html>
