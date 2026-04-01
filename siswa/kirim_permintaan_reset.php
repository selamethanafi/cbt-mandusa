<?php
require_once '../inc/config.php';
if(isset($_GET['nopes']))
{
	$nopes= $_GET['nopes'];
}
else
{
	$nopes = '';
}
if(isset($_GET['kode']))
{
	$kode= $_GET['kode'];
}
else
{
	$kode = '';
}
if(isset($_GET['reset']))
{
	$reset= $_GET['reset'];
}
else
{
	$reset = '';
}
if((!empty($nopes)) and (!empty($kode)) and (!empty($reset)))
{
	$query = mysqli_query($db, "SELECT * from siswa WHERE username = '$nopes'");
	if(mysqli_num_rows($query) == 0)
	{
		echo 'Gagal mengirim permintaan reset, data murid tidak ditemukan, <a href="login,php?">ke Halaman Login/a>';
	    header('Location: login.php');
	    exit();
	}
	$dq = mysqli_fetch_assoc($query);
	$nama = $dq['nama_siswa'];
	$id_siswa = $dq['id_siswa'];
	$ta = mysqli_query($db, "SELECT * FROM `reset` WHERE `id_siswa` = '$id_siswa' and `macam` = '$reset'");
	if(mysqli_num_rows($ta) == 0)
	{
		mysqli_query($db, "insert into reset (`id_siswa`, `nama`) values ('$id_siswa', '$nama')");
	}
	$_SESSION['warning_message'] = 'Permintaan reset sudah dikirimkan';
    header('Location: login.php?nopes='.$nopes.'&kode='.$kode);
    exit();
}
	$_SESSION['warning_message'] = 'Gagal mengirim permintaan reset';
    header('Location: gagal.php?nopes='.$nopes.'&kode='.$kode);
    exit();
