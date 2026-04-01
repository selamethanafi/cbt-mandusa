<?php
if(isset($_GET['nopes']))
{
	$nopes = $_GET['nopes'];
}
else
{
$kode = '';
}
if(isset($_GET['kode']))
{
	$kode = $_GET['kode'];
}
else
{
$nopes = '';
}
$waktu = 10;
?>
		<script>setTimeout(function () {
		 window.location.href= '../peserta/login.php?nopes=<?php echo $nopes;?>&kode=<?php echo $kode;?>';
			},<?php echo $waktu;?>);
			</script>

