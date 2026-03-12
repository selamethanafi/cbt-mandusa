<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$sql = "SELECT soal, a, b, c, d, e
        FROM soal
        WHERE soal LIKE ?
        OR a LIKE ?
        OR b LIKE ?
        OR c LIKE ?
        OR d LIKE ?
        OR e LIKE ?";

$stmt = $db->prepare($sql);

$cari = '%<img src="http%';

$stmt->bind_param("ssssss", $cari, $cari, $cari, $cari, $cari, $cari);

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    echo "<div style='border:1px solid #ccc;padding:10px;margin:10px'>";
    echo "<b>Soal:</b><br>".$row['soal']."<br><br>";
    echo "<b>A:</b> ".$row['a']."<br>";
    echo "<b>B:</b> ".$row['b']."<br>";
    echo "<b>C:</b> ".$row['c']."<br>";
    echo "<b>D:</b> ".$row['d']."<br>";
    echo "<b>E:</b> ".$row['e']."<br>";
    echo "</div>";
}

$stmt->close();
$db->close();
?>
