<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$ta = $db->query("SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'cbt_ruang'");
                                        $da = mysqli_fetch_assoc($ta);
                                        $ruang = $da['konfigurasi_isi'];
$ta = $db->query("SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode` = 'versi'");
                                        $da = mysqli_fetch_assoc($ta);
                                        $versi = $da['konfigurasi_isi'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Menu</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<h4 class="mb-2">Menu</h4>
<div class="mb-3 text-muted">
Ruang: <strong><?= htmlspecialchars($ruang) ?></strong>
</div>
<a href="logout.php">Log out</a>
<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:25%;">Persiapan</th>
    <th style="width:25%;">Daftar Tes</th>
    <th style="width:25%;">Pengawasan</th>
    <th style="width:25%;">Laporan</th>
</tr>
</thead>
<tbody>
<tr>
    <td><p><a class="btn btn-primary" href="sinkron_peserta.php" onclick="return confirm('Yakin mengunduh peserta semua?')">
Unduh Peserta</a></p><p><a class="btn btn-primary" href="sinkron_peserta_per_ruang.php" onclick="return confirm('Yakin mengunduh peserta di ruang ini?')">
Unduh Peserta Ruang ini</a></p><p><a class="btn btn-primary" href="unduh_tes.php"  onclick="return confirm('Yakin mengunduh tes dari server pusat?')">Unduh Tes</a></p><p><a class="btn btn-primary" href="unduh_soal_per_kode_soal.php"  onclick="return confirm('Yakin mengunduh soal dari server pusat?')">Unduh Soal</a></p>
<p><a class="btn btn-primary" href="siswa.php">Daftar Peserta</a></p>
<p><a class="btn btn-primary" href="update_jadwal.php"  onclick="return confirm('Yakin memutahirkan data cbt dari server pusat?')">Update CBT</a></p>
<p><a class="btn btn-primary" href="pengaturan.php">Pengaturan</a></p>
</td>
    <td>
       	<p><a class="btn btn-primary" href="daftar_tes_hapus.php">Hapus Tes</a></p>    
       	<p><a class="btn btn-primary" href="soal_hari_ini.php">Daftar Tes Hari Ini</a></p>
    	<p><a class="btn btn-primary" href="daftar_tes.php">Daftar Tes</a></p>
    	    	<p><a class="btn btn-primary" href="daftar_tes_per_tanggal.php">Daftar Tes Per Sesi</a></p>    	    	
    </td>
    <td><p><a class="btn btn-primary" href="soal_hari_ini.php">Pengawasan</a></p>
    <p><a class="btn btn-primary" href="kirim_nilai.php">Kirim Hasil</a></p></td>
    <td><p><a class="btn btn-primary" href="daftar_hadir_tes_bersama.php">Daftar Hadir dan Berita Acara</a></p>
    </td>
</tr>
</tbody>
</table>
Versi <?= $versi;?> <a href="update.php"  class="btn btn-success">Update</a>
</div>
</div>

</body>
</html>

