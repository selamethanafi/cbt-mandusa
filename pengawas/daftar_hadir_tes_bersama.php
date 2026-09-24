<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

if (!$db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
$cek = mysqli_query($db, "SHOW COLUMNS FROM `daftar_pengawas` LIKE 'murid'");
if (mysqli_num_rows($cek) == 0) {
    $sql = "
        ALTER TABLE `daftar_pengawas`
        ADD `murid` TEXT NULL DEFAULT NULL
        AFTER `catatan`
    ";
    if (mysqli_query($db, $sql)) {
        echo "Kolom murid berhasil ditambahkan";
    } else {
        echo "Error: " . mysqli_error($db);
    }
} else {
    //echo "Kolom murid sudah ada";
}
$aksi = '';
$nama_pengawas = '';
$pengawas_dua = '';
$catatan = '';
if(isset($_GET['tanggal']))
{
	$tanggal_tes = $_GET['tanggal'];
	$ta =  mysqli_query($db, "SELECT * FROM `daftar_pengawas` WHERE `waktu` = '$tanggal_tes'");
	if(mysqli_num_rows($ta) > 0)
	{
		$aksi = 'ubah';
		$da = mysqli_fetch_assoc($ta);
		$nama_pengawas = $da['nama_pengawas'];
		$pengawas_dua = $da['pengawas_dua'];
		$catatan = $da['catatan'];
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>
                               
                                    <form method="POST">
                                        <div class="row mb-3">
                                        <?php
                                        if($aksi == 'ubah')
                                        {?>
                                            <div class="col-md-6 mt-3">
                                                <label for="kode_soal" class="form-label">Waktu Pelaksanaan</label>
                                                <select name="post_tanggal_tes" id="kode_soal" class="form-control" required>
                                                    <?php
                                                        echo "<option value='{$tanggal_tes}'>{$tanggal_tes}</option>";
                                                    ?>
                                                </select>
                                            </div>
                                            <?php
                                            }
                                            
                                            else
                                            {?>
                                             <div class="col-md-6 mt-3">
                                                <label for="kode_soal" class="form-label">Waktu Pelaksanaan</label>
                                                <select name="post_tanggal_tes" id="kode_soal" class="form-control" required>
                                                    <option value="">Pilih waktu pelaksanaan</option>
                                                    <?php

                                                    $ta =  mysqli_query($db, "SELECT DISTINCT `tanggal` FROM `ujian_aktif` WHERE 1 order by `tanggal`");
                                    while ($da = mysqli_fetch_assoc($ta)) 
                                    {
                                                        echo "<option value='{$da['tanggal']}'>{$da['tanggal']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <?php
                                            }
                                            ?>
                                            
                                            <div class="col-md-12 mt-3">
                                                <label for="pengawas" class="form-label">Catatan</label>
                                                <textarea name="catatan" id="catatan" class="form-control" rows="3"
                                                    placeholder="catatan selama pelaksanaan tes"
                                                    required><?= $catatan;?></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="pengawas" class="form-label">Nama Pengawas</label>
                                                <input name="pengawas" id="pengawas" class="form-control" 
                                                    placeholder="nama pengawas" value="<?= $nama_pengawas;?>"
                                                    required></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="pengawas_dua" class="form-label">Nama Pengawas Kedua</label>
                                                <input name="pengawas_dua" id="pengawas_dua" class="form-control" 
                                                    placeholder="pengawas dua" value="<?= $pengawas_dua;?>"
                                                    required></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <button type="submit" name="tampilkan"
                                                    class="btn btn-secondary w-100">Simpan data Berita Acara</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <?php if (isset($_POST['tampilkan'])): ?>
                            <?php
	                        $post_tanggal_tes = mysqli_real_escape_string($db, $_POST['post_tanggal_tes']);
	                        $pengawas = mysqli_real_escape_string($db, $_POST['pengawas']);
	                        $pengawas_dua = mysqli_real_escape_string($db, $_POST['pengawas_dua']);
	                        $catatan = mysqli_real_escape_string($db, $_POST['catatan']);
	                        
                            	$ta = mysqli_query($db,"select * from `daftar_pengawas` where `waktu` = '$post_tanggal_tes'");
                            	
                            	if(mysqli_num_rows($ta) == 0)
                            	{
	                            	mysqli_query($db,"insert into `daftar_pengawas` (`waktu`, `nama_pengawas`, `catatan`, `pengawas_dua`) values ('$post_tanggal_tes', '$pengawas', '$catatan', '$pengawas_dua')");
                            	}
                            	else
                            	{
	                            	mysqli_query($db,"update `daftar_pengawas` set `nama_pengawas`= '$pengawas', `catatan` ='$catatan',`pengawas_dua` = '$pengawas_dua' where `waktu` = '$post_tanggal_tes'");
                            	}
                            endif; ?>
                        </div>
                    </div>
                    <div class="row">
                    <?php
	                    $ta = mysqli_query($db,"select * from `daftar_pengawas` order by `waktu` DESC");
	                    $no = 1;
	                    ?>
                    	<table class="table table-bordered table-responsive" id="nilaiTableData">
		        	<thead>
		                    <tr>
		                        <th>Nomor</th>
	                	        <th>Waktu Pelaksanaan</th>
		                        <th>Pengawas I</th>
		                        <th>Pengawas II</th>
		                        <th>Catatan</th>
		                        <th>Ubah</th>
		                        <th colspan="2">Cetak</th>
	                    	</tr>
		                </thead>
		                <tbody>
		                <?php
		                 while ($da = mysqli_fetch_assoc($ta)) 
		                 {
			            echo "<tr><td>{$no}</td><td>{$da['waktu']}</td><td>{$da['nama_pengawas']}</td><td>{$da['pengawas_dua']}</td><td>{$da['catatan']}</td>";
			            
			            echo '<td><a href="daftar_hadir_tes_bersama.php?tanggal='.$da['waktu'].'" class="btn btn-primary">Ubah</a></td><td>';
			            echo '<a href="berita_acara_tes_bersama_cetak.php?tanggal='.$da['waktu'].'"
   class="btn btn-warning"
   target="_blank"
   onclick="return confirm(&quot;Cetak Berita Acara dan menonaktifkan tes untuk waktu '.$da['waktu'].'?&quot;);">
   BA
</a>';
echo '</td><td><a href="daftar_hadir_tes_bersama_cetak.php?tanggal='.$da['waktu'].'" class="btn btn-success" target="_blank">DH</a></td></tr>';
			            $no++;
			        }
			 echo '</tbody></table>';
			    ?>


</body>

</html>
