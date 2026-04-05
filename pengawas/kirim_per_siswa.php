<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$waktu = 10;
if(isset($_GET['id_siswa']))
{
$id_siswa = $_GET['id_siswa'];
}
else
{
$id_siswa = '0';
}
if(isset($_GET['id_ujian']))
{
$id_ujian = $_GET['id_ujian'];
}
else
{
$id_ujian = '0';
}
$query_nilai = $db->query("SELECT * FROM `ujian` WHERE `id_siswa` = '$id_siswa' and `id_ujian` = '$id_ujian'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mengirim Jawaban Siswa</title>
<link rel="stylesheet" href="../css/style.css">
<?php
while($hu = mysqli_fetch_assoc($query_nilai))
{
	$id_ujian = $hu['id_ujian'];
	$id_siswa = $hu['id_siswa'];
	$qsoal = $db->query("SELECT * FROM `soal` WHERE `id_ujian`= '$id_ujian' order by `nomer_soal` ASC");
	$kunci_jawaban = '';
	while($dq = mysqli_fetch_assoc($qsoal))
	{
		if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $dq['kunci'];
		}
		else
		{
			$kunci_jawaban .= '#'.$dq['kunci'];
		}
		
	}
	$qnilai = $db->query("SELECT * FROM `nilai` WHERE `id_ujian` = '$id_ujian' and `id_siswa` = '$id_siswa'");
	$dnilai = mysqli_fetch_assoc($qnilai);
	$qnu = $db->query("SELECT * FROM `ujian_aktif` WHERE `id_ujian` = '$id_ujian'");
	$dnu = mysqli_fetch_assoc($qnu);
	$kode_soal = $dnu['kode_soal'] ?? '';
	$boleh = $dnu['nilai'] ?? '1';
	$token = substr(str_shuffle('ABCDEFGHJKLMNPQRSTWXYZ123456789'), 0, 6);
	$qj = $db->query("SELECT * FROM `jawaban` where `id_siswa` = '$id_siswa' and `id_ujian` = '$id_ujian' ORDER BY `id_soal` ASC");
	$jwb_siswa = '';
	$analisis = '';
	$skor_per_soal = '';
	$awal = 0;
	while($dj = mysqli_fetch_assoc($qj))
	{
		$dijawab = $dj['jawaban'];
		$analisis .= $dj['nilai'];
		if($awal > 0)
		{
			$skor_per_soal .= '#'.$dj['nilai'];
			$jwb_siswa .= '#'.$dijawab;
		}
		else
		{
			$skor_per_soal .= $dj['nilai'];
			$jwb_siswa .= $dijawab;
		}
		$awal++;
	}
	$nilai_akhir = $dnilai['nilai'] ?? 0;
	$url = $sianis.'/tukardata/terimajawabanubk';
	$jwb_siswa = str_replace('"','',$jwb_siswa);
	$jwb_siswa = str_replace('[','',$jwb_siswa);
	$jwb_siswa = str_replace(']','',$jwb_siswa);
	$jwb_siswa = str_replace('{','',$jwb_siswa);
	$jwb_siswa = str_replace('}','',$jwb_siswa);
	$params=[
			'app_key'=>$key,
			'tmujian_id' => $kode_soal,
			'nis' => $id_siswa,
			'jawaban_pg' => $jwb_siswa,
			'nilai' => $nilai_akhir,
			'hasil_analisis' => $analisis,
			'kunci_jawaban' => $kunci_jawaban,
			'skor_per_soal' => $skor_per_soal,
			'boleh' => $boleh,
			];
	//print_r($params);
	if($hasil = postcurl($url,$params))
	{
		echo $hasil;
		$json = json_decode($hasil, true);
		if($json)
		{
			foreach($json as $dt)
			{
				echo 'Berhasil';
				$pesan = $dt['pesan'];
				if($pesan == 'oke')
				{
					echo ' terkirim';
				}
				else
				{
					echo 'Gagal mengirim, <a href="kirim_nilai.php?tanggal='.$tanggal.'&ke='.$ke.'">Ulang</a>';
					die();
				}
			}
		}
		else
		{
			echo 'Tidak terkirim';
			die();
		}
	}
	else
	{
		echo 'Hasil '.$hasil.' ';
		echo 'Gagal terhubung ke simamad, gagal mengirim nilai';
		die();
	}
}
?>



