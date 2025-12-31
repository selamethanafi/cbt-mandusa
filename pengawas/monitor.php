<?php
include '../config.php';
if(!isset($_SESSION['role']) || $_SESSION['role']!='pengawas'){
    exit('Akses ditolak');
}
$ruang = '1';
//$ruang = $_SESSION['ruang'];

$q = $db->query("
SELECT 
    s.id,
    s.nama,
    s.kelas,
    s.ruang,
    u.mulai,
    u.selesai,
    u.status,
    COUNT(j.id_soal) AS dijawab
FROM siswa s
LEFT JOIN ujian u 
    ON u.id_siswa = s.id
LEFT JOIN jawaban j 
    ON j.id_siswa = s.id
    AND j.jawaban IS NOT NULL
    AND TRIM(j.jawaban) != ''
WHERE s.ruang = '$ruang'
GROUP BY s.id
ORDER BY s.kelas ASC, s.nama ASC
");

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="30">

<title>CBT Ujian</title>

<!-- Bootstrap CSS (hanya CSS, tanpa JS) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
<h4 class="mb-2">Dashboard Pengawas</h4>
<div class="mb-3 text-muted">
Ruang: <strong><?= htmlspecialchars($ruang) ?></strong>
</div>

<table class="table table-bordered table-striped table-sm align-middle">
<thead class="table-light text-center">
<tr>
    <th style="width:40px;">No</th>
    <th>Nama Siswa</th>
    <th style="width:90px;">Kelas</th>
    <th style="width:90px;">Status</th>
    <th style="width:80px;">Dijawab</th>
    <th style="width:110px;">Sisa Waktu</th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
while($r = $q->fetch_assoc()){

    // status default
    $status = $r['status'] ?? 'belum';

    // badge warna
    if($status == 'aktif'){
        $badge = 'success';
    } elseif($status == 'selesai'){
        $badge = 'secondary';
    } else {
        $badge = 'warning';
    }

    // hitung sisa waktu
    $sisa = '-';
    if($status == 'aktif' && !empty($r['selesai'])){
        $detik = strtotime($r['selesai']) - time();
        $sisa = $detik > 0 ? gmdate("H:i:s",$detik) : '00:00:00';
    }
?>
<tr class="text-center">
    <td><?= $no++ ?></td>
    <td class="text-start"><?= htmlspecialchars($r['nama']) ?></td>
    <td><?= $r['kelas'] ?></td>
    <td>
        <span class="badge bg-<?= $badge ?>">
            <?= strtoupper($status) ?>
        </span>
    </td>
    <td><?= $r['dijawab'] ?></td>
    <td><?= $sisa ?></td>
</tr>
<?php } ?>
</tbody>
</table>
<div class="mt-2 small">
<span class="badge bg-success">AKTIF</span> Sedang ujian &nbsp;
<span class="badge bg-warning text-dark">BELUM</span> Belum mulai &nbsp;
<span class="badge bg-secondary">SELESAI</span> Sudah selesai
</div>


</div>
</div>
</body>
</html>

