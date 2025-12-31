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
