<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
if (!isset($_GET['jam']))
 {
    header('Location: soal_hari_ini.php');
    exit;
}
$jam = $_GET['jam'];
$aksi = 'aktif';
$tanggal = date("Y-m-d").' '.$jam;
// Untuk aksi AKTIF
 $kode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6));
 $status = 'Aktif';
// Ambil kode_soal dari tabel soal
$sqlKode = mysqli_query($db, "SELECT * FROM ujian_aktif WHERE tanggal = '$tanggal'");
while($dataKode = mysqli_fetch_assoc($sqlKode))
{
    if ($sqlKode && mysqli_num_rows($sqlKode) > 0) 
    {
        $kode_soal = $dataKode['kode_soal'];
        $id_ujian = $dataKode['id_ujian'];
        // Cek apakah ada butir soal
        $cekButir = mysqli_query($db, "SELECT COUNT(*) AS total FROM soal WHERE kode_soal = '$kode_soal'");
        $dataButir = mysqli_fetch_assoc($cekButir);
        if ($dataButir['total'] == 0) {
		echo '<a href="soal_hari_ini.php">Kembali <a/> ';
            die('Ujian '.$kode_soal.' tidak dapat diaktifkan karena belum memiliki butir soal.');
        }
       $updateStatus = mysqli_query($db, "UPDATE ujian_aktif SET status = 'Aktif', token = '$kode' WHERE id_ujian = '$id_ujian'");
	}
}
header('Location: soal_hari_ini.php?tgl='.date("Y-m-d").'&jam='.$jam);
exit;
