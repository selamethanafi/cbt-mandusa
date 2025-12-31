<?php

include '../inc/config.php';
if(!isset($_SESSION['id_siswa'])) exit('Akses ditolak');
if(!isset($_SESSION['ujian_id'])){
    exit('Akses ujian ditolak');
}
echo '<script>history.pushState(null,null,location.href); window.onpopstate=function(){history.go(1);}</script>';
$ujian_id = $_SESSION['ujian_id'];
$u = $db->query("SELECT * FROM ujian_aktif WHERE id=$ujian_id")->fetch_assoc();

if(!$u || time() > strtotime($u['selesai'])){
    exit('Ujian sudah berakhir');
}
$id_siswa = $_SESSION['id_siswa'];

/* ===============================
   SETTING UJIAN
================================ */
$durasi = 90; // menit

// catat waktu mulai & selesai (sekali saja)
$db->query("
INSERT IGNORE INTO ujian (id_siswa,mulai,selesai)
VALUES ($id_siswa, NOW(), DATE_ADD(NOW(), INTERVAL $durasi MINUTE))
");

// ambil waktu ujian
$uj = $db->query("SELECT * FROM ujian WHERE id_siswa=$id_siswa")->fetch_assoc();
$sisa = strtotime($uj['selesai']) - time();
if($sisa <= 0){
    header("Location: selesai.php");
    exit;
}

/* ===============================
   NAVIGASI SOAL
================================ */
$no = isset($_GET['no']) ? (int)$_GET['no'] : 1;
$total = $db->query("SELECT COUNT(*) jml FROM soal")->fetch_assoc()['jml'];
if($no < 1) $no = 1;
if($no > $total) $no = $total;

// ambil soal
$s = $db->query("
SELECT * FROM soal
LIMIT ".($no-1).",1
")->fetch_assoc();

// ambil jawaban sebelumnya
$jwb = $db->query("
SELECT jawaban FROM jawaban
WHERE id_siswa=$id_siswa AND id_soal=".$s['id']
)->fetch_assoc();

$jawaban = $jwb['jawaban'] ?? '';
$jawaban_arr = json_decode($jawaban,true);
$ids = [];
$qid = $db->query("SELECT id FROM soal ORDER BY id ASC");
while($r = $qid->fetch_assoc()){
    $ids[] = $r['id'];
}
/* ===============================
   STATUS JAWABAN (INDIKATOR)
================================ */
$jawab = [];

$qj = $db->query("
SELECT id_soal
FROM jawaban
WHERE id_siswa = $id_siswa
AND jawaban IS NOT NULL
AND TRIM(jawaban) != ''
");

while($r = $qj->fetch_assoc()){
    $jawab[$r['id_soal']] = true;
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>CBT Ujian</title>
<link rel="stylesheet" href="../css/style.css">
<script>
history.pushState(null,null,location.href);
window.onpopstate = () => history.go(1);
</script>
</head>


<body>
<div class="container-fluid mt-4">
<div class="text-center mb-3">
    <h4 class="fw-bold">UJIAN SEKOLAH</h4>
    <div class="text-muted small">
        SMA / SMK / MA • Tahun Pelajaran <?= date('Y') ?>
    </div>
    <hr>
</div>

<div class="card shadow-sm">
<div class="card-body">


<div class="d-flex justify-content-between mb-3">
    <h5>Soal <?= $no ?> dari <?= $total ?></h5>
    <div class="timer">
        ⏱ <?= gmdate("H:i:s",$sisa) ?>
    </div>
</div>
<div class="indikator">
<?php foreach($ids as $i => $id_soal): 

    $kelas = 'ind-belum';

    if(isset($jawab[$id_soal])){
        $kelas = 'ind-jawab';
    }

    if($id_soal == $s['id']){
        $kelas = 'ind-aktif';
    }
?>
<a href="?no=<?= $i+1 ?>" class="<?= $kelas ?>">
<?= $i+1 ?>
</a>
<?php endforeach ?>
</div>

<div class="alert alert-secondary soal">
<?= nl2br(htmlspecialchars($s['soal'])) ?>
</div>
<form method="post" action="simpan.php">

<input type="hidden" name="id_soal" value="<?= $s['id'] ?>">
<input type="hidden" name="no" value="<?= $no ?>">

<?php
/* ===============================
   TAMPILKAN SOAL SESUAI TIPE
================================ */
switch($s['tipe']){

// ================= PG & BENAR-SALAH =================
case 'pg':

case 'bs':
    foreach(['a','b','c','d','e'] as $p){
    
        if(empty($s[$p])) continue;
        $val = strtoupper($p);
        $cek = ($jawaban == $val) ? 'checked' : '';
        ?>
    <div class="form-check opsi">
  <input class="form-check-input" type="radio"
         name="jawaban" value="<?= $val ?>" <?= $cek ?>>
  <label class="form-check-label">
    <?= $s[$p] ?>
  </label>
</div>
<?php
    }
break;

// ================= PG KOMPLEKS =================
case 'pg_kompleks':
    foreach(['a','b','c','d','e'] as $p){
        if(empty($s[$p])) continue;
        $val = strtoupper($p);
        $cek = (is_array($jawaban_arr) && in_array($val,$jawaban_arr)) ? 'checked' : '';
        ?>
       <div class="form-check opsi">
  <input class="form-check-input" type="checkbox"
         name="jawaban[]" value="<?= $val ?>" <?= $cek ?>>
  <label class="form-check-label">
    <?= $s[$p] ?>
  </label>
</div>
<?php
    }
break;

// ================= MENJODOHKAN =================
case 'jodohkan':
    $pair = json_decode($s['pasangan'],true);
    foreach($pair as $k=>$v){
        $isi = $jawaban_arr[$k] ?? '';
        ?>
        <div class="row mb-2">
  <div class="col-md-5 fw-bold"><?= $k ?></div>
  <div class="col-md-7">
    <input class="form-control" type="text"
           name="jawaban[<?= htmlspecialchars($k) ?>]"
           value="<?= htmlspecialchars($isi) ?>">
  </div>
</div>
<?php
    }
break;

// ================= URAIAN =================
case 'uraian':
    ?>
    <textarea class="form-control" name="jawaban" rows="6">
<?= htmlspecialchars($jawaban) ?>
</textarea>
<?php
break;
}
?>

<br>
<div class="mt-3">
<button class="btn btn-success">💾 Simpan Jawaban</button>
</div>


</form>
<div class="mt-3 d-flex justify-content-between">
<?php if($no > 1): ?>
<a class="btn btn-outline-secondary" href="?no=<?= $no-1 ?>">⬅ Sebelumnya</a>
<?php endif ?>

<?php if($no < $total): ?>
<a class="btn btn-outline-primary" href="?no=<?= $no+1 ?>">Berikutnya ➡</a>
<?php else: ?>
<a class="btn btn-danger"
   href="selesai.php"
   onclick="return confirm('Yakin mengakhiri ujian?')">
   Selesai
</a>
<?php endif ?>
</div>
</div> <!-- card-body -->
</div> <!-- card -->
</div> <!-- container -->
</body>
</html>


