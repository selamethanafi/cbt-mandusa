<?php
function tanggal($str)
	{
		$postedyear=substr($str,0,4);
		$postedmonth=substr($str,5,2);
  		$postedday=substr($str,8,2);
		$tanggalbiasa = $postedday.'-'.$postedmonth.'-'.$postedyear;	
		return $tanggalbiasa;	
	}	
function tanggal_jam($str)
	{
		$postedyear=substr($str,0,4);
		$postedmonth=substr($str,5,2);
  		$postedday=substr($str,8,2);
		$tanggalbiasa = $postedday.'-'.$postedmonth.'-'.$postedyear.' '.substr($str,-10);	
		return $tanggalbiasa;	
	}
function verify_siswa_password($password_input, $stored_password) {
    global $method, $rahasia;

    if (empty($password_input) || empty($stored_password)) {
        return false;
    }

    $decoded = base64_decode($stored_password);
    $iv_length = openssl_cipher_iv_length($method);
    $iv = substr($decoded, 0, $iv_length);
    $ciphertext = substr($decoded, $iv_length);
    $decrypted_password = openssl_decrypt($ciphertext, $method, $rahasia, 0, $iv);

    if ($decrypted_password === false) {
        error_log("Failed to decrypt password.");
        return false;
    }

    return ($decrypted_password === $password_input);
}
function clean($v){
    return trim($v ?? '');
}
function cari_semester()
{
	$tahuny = date("Y");
	$bulany = date("m");
	$tanggaly = date("d");
	if (($bulany=='07') or ($bulany=='08') or ($bulany=='09') or ($bulany=='10') or ($bulany=='11') or ($bulany=='12'))
	{
		$semester= '1';
	}
	else
	{
		$semester= '2';
	}
	//$semester='2';
	return $semester;
}
function cari_thnajaran()
	{

		$tahuny = date("Y");
		$bulany = date("m");
		$tanggaly = date("d");
		if (($bulany=='07') or ($bulany=='08') or ($bulany=='09') or ($bulany=='10') or ($bulany=='11') or ($bulany=='12'))
		{
			$tahuny2 = $tahuny+1;
			$thnajaran = ''.$tahuny;
		}
		else
		{
			$tahuny1 = $tahuny-1;
			$thnajaran = ''.$tahuny1;
		}
		//$thnajaran = '2018/2019';
		return $thnajaran;
	}
function tanggal_ke_hari($str) 
	{
	$dinane='?';
	if(strlen($str)==10)
	{
	$x = substr($str,0,4);
	$y = substr($str,5,2);
	$z = substr($str,8,2);
	$dina = date("l", mktime(0, 0, 0, $y, $z, $x));

	if ($dina == 'Sunday')
		{
		$dinane = 'Minggu';
		}
	if ($dina == 'Monday')
		{
		$dinane = 'Senin';
		}
	if ($dina == 'Tuesday')
		{
		$dinane = 'Selasa';
		}
	if ($dina == 'Wednesday')
		{
		$dinane = 'Rabu';
		}
	if ($dina == 'Thursday')
		{
		$dinane = 'Kamis';
		}
	if ($dina == 'Friday')
		{
		$dinane = 'Jumat';
		}
	if ($dina == 'Saturday')
		{
		$dinane = 'Sabtu';
		}
	}
	return $dinane;
  	}
function angka_jadi_bulan($postedmonth)
	{
		$bulan='';
		if ($postedmonth=="01")
			{
			$bulan = "Januari";
			}
		if ($postedmonth=="02")
			{
			$bulan = "Februari";
			}
		if ($postedmonth=="03")
			{
			$bulan = "Maret";
			}
		if ($postedmonth=="04")
			{
			$bulan = "April";
			}
		if ($postedmonth=="05")
			{
			$bulan = "Mei";
			}
		if ($postedmonth=="06")
			{
			$bulan = "Juni";
			}
		if ($postedmonth=="07")
			{
			$bulan = "Juli";
			}
		if ($postedmonth=="08")
			{
			$bulan = "Agustus";
			}
		if ($postedmonth=="09")
			{
			$bulan = "September";
			}
		if ($postedmonth=="10")
			{
			$bulan = "Oktober";
			}
		if ($postedmonth=="11")
			{
			$bulan = "November";
			}
		if ($postedmonth=="12")
			{
			$bulan = "Desember";
			}
		return $bulan;	
	} 
function via_curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $out = curl_exec($ch);
    curl_close($ch);
    return json_decode($out, true);
}
function isValidDateTime($datetime) {
    $format = 'Y-m-d H:i:s';
    $d = DateTime::createFromFormat($format, $datetime);
    return $d && $d->format($format) === $datetime;
}
function postcurl($urlsms,$params) 
	{
		$ch = curl_init($urlsms);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
