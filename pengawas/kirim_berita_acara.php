<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$waktu = 10;
if(isset($_GET['tanggal']))
{
$tanggal = $_GET['tanggal'];
}
else
{
$tanggal = date("Y-m-d");
}

$year = substr($tanggal,0,4);
$month = substr($tanggal,5,2); // February
$day = substr($tanggal,8,2);
if (checkdate($month, $day, $year)) {

} else {
die('tanggal salah');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mengirim Berita Acara</title>
<link rel="stylesheet" href="../css/style.css">
<?php
$q = mysqli_query(
    $db,
    "SELECT
        waktu,
        nama_pengawas,
        catatan,
        murid
     FROM daftar_pengawas
     WHERE waktu LIKE '$tanggal%'"
);

$data = [];

while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}
$payload = json_encode([
    'token' => $key,
    'ruang' => $ruang,
    'data'  => $data
]);

$ch = curl_init($sianis.'/cbt/ba');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => $payload
]);

$response = curl_exec($ch);
curl_close($ch);

//echo $response;
$result = json_decode($response, true);

echo sprintf(
    "Berita acara terkirim  %s (%d data)",
    $result['status'] ? 'BERHASIL' : 'GAGAL',
    $result['jumlah'] ?? 0
);
?>
<br /><a href="menu.php">Kembali ke Menu</a>
