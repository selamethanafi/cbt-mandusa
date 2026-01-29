<?php
/* ===============================
   UJIAN.PHP – FINAL PRODUKSI
================================ */

include '../inc/config.php';

if (!isset($_SESSION['id_siswa'])) {
    header("Location: login.php");
    exit;
}

$id_siswa = $_SESSION['id_siswa'];
$ujian_id = $_SESSION['ujian_id'];


/* ===============================
   DISABLE BACK BROWSER
================================ */
echo "<script>
history.pushState(null,null,location.href);
window.onpopstate=function(){history.go(1);}
</script>";
$u = $db->query("SELECT * FROM ujian_aktif WHERE id_ujian=$ujian_id")->fetch_assoc();

if(!$u || time() > strtotime($u['tanggal_selesai'])){
    exit('Ujian sudah berakhir');
}
$durasi = $u['waktu_ujian']; // menit
/* ===============================
   CATAT WAKTU UJIAN (1x)
================================ */
$db->query("INSERT IGNORE INTO ujian (id_siswa,id_ujian,mulai,selesai) VALUES ($id_siswa, $ujian_id, NOW(), DATE_ADD(NOW(), INTERVAL $durasi MINUTE))");

$uj = $db->query("SELECT * FROM ujian WHERE id_siswa=$id_siswa and id_ujian = $ujian_id")->fetch_assoc();
$sisa = strtotime($uj['selesai']) - time();
if ($sisa <= 0) {
    header("Location: selesai.php");
    exit;
}

/* ===============================
   URUTAN SOAL ACAK (SEKALI SAJA)
================================ */
if (!isset($_SESSION['urutan_soal'])) {
    $ids = [];
    
    $q = $db->query("SELECT id FROM soal where `id_ujian` = '$ujian_id'");
    while ($r = $q->fetch_assoc()) {
        $ids[] = $r['id'];
    }
    shuffle($ids);
    $_SESSION['urutan_soal'] = $ids;
}

$urutan = $_SESSION['urutan_soal'];
$total  = count($urutan);

/* ===============================
   NAVIGASI SOAL
================================ */
$no = isset($_GET['no']) ? (int)$_GET['no'] : 1;
if ($no < 1) $no = 1;
if ($no > $total) $no = $total;

$id_soal = $urutan[$no - 1];

/* ===============================
   AMBIL SOAL
================================ */
$s = $db->query("SELECT * FROM soal WHERE id='$id_soal'")->fetch_assoc();

/* ===============================
   JAWABAN SISWA
================================ */
$jwb = $db->query("
SELECT jawaban FROM jawaban
WHERE id_siswa=$id_siswa AND id_soal='$id_soal'
")->fetch_assoc();

$jawaban      = $jwb['jawaban'] ?? '';
$jawaban_arr  = json_decode($jawaban, true);

/* ===============================
   STATUS TERJAWAB (INDIKATOR)
================================ */
$jawab = [];
$qj = $db->query("
SELECT id_soal FROM jawaban
WHERE id_siswa=$id_siswa
AND jawaban IS NOT NULL AND TRIM(jawaban)!=''
");
while ($r = $qj->fetch_assoc()) {
    $jawab[$r['id_soal']] = true;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Ujian</title>
<link rel="stylesheet" href="../css/ujian.css?v=<?= time() ?>">
</head>
<body>

<div class="container-fluid">

<div class="text-center mb-3">
    <h4>UJIAN SEKOLAH</h4>
    <small><?= date('Y') ?></small>
</div>

<div class="card shadow-sm">

<div class="d-flex justify-content-between mb-3">
    <div>Soal <?= $no ?> / <?= $total ?></div>
    <div class="timer">⏱ <?= gmdate("H:i:s", $sisa) ?></div>
</div>

<!-- INDIKATOR -->
<div class="indikator mb-3">
<?php foreach ($urutan as $i => $sid): 
    $kelas = 'ind-belum';
    if (isset($jawab[$sid])) $kelas = 'ind-jawab';
    if ($sid == $id_soal)     $kelas = 'ind-aktif';
?>
<a href="?no=<?= $i+1 ?>" class="<?= $kelas ?>"><?= $i+1 ?></a>
<?php endforeach; ?>
</div>

<!-- SOAL -->
<div class="soal"><?= $s['soal'] ?>
</div>

<form method="post" action="simpan.php">
<input type="hidden" name="id_soal" value="<?= $id_soal ?>">
<input type="hidden" name="no" value="<?= $no ?>">

<?php
/* ===============================
   TAMPIL SOAL SESUAI TIPE
================================ */
switch (strtolower($s['tipe'])) {

// ================= PG & BENAR-SALAH =================
case 'pg':

    foreach (['a','b','c','d','e'] as $p) {

        if (empty($s[$p])) continue;

        $opt = strtoupper($p); // A B C D E

        $cek = ($jawaban === $opt) ? 'checked' : '';

        echo "
        <label class='opsi-box'>
            <input type='radio'
                   name='jawaban'
                   value='$opt'
                   $cek>
            <span class='opsi-huruf'>$opt.</span>
            <span class='opsi-teks'>{$s[$p]}</span>
        </label>
        ";
    }

break;


case 'bs':

    echo "<div class='soal-bs'>";

    foreach (['a','b','c','d','e'] as $p) {

        if (empty($s[$p])) continue;

        $kode = strtoupper($p); // A, B, C...

        // jawaban siswa (jika ada)
        $pilih = $jawaban_arr[$kode] ?? '';

        echo "
        <div class='bs-item'>
            <div class='bs-teks'>
                {$s[$p]}
            </div>

            <label class='bs-opsi'>
                <input type='radio'
                       name='jawaban[$kode]'
                       value='B'
                       ".($pilih=='B'?'checked':'').">
                Benar
            </label>

            <label class='bs-opsi'>
                <input type='radio'
                       name='jawaban[$kode]'
                       value='S'
                       ".($pilih=='S'?'checked':'').">
                Salah
            </label>
        </div>
        ";
    }

    echo "</div>";

break;

case 'pg_kompleks':

    foreach (['a','b','c','d','e'] as $p) {

        if (empty($s[$p])) continue;

        $opt = strtoupper($p); // A B C D E

        $cek = (
            is_array($jawaban_arr) &&
            in_array($opt, $jawaban_arr)
        ) ? 'checked' : '';

        echo "
        <label class='opsi-box'>
            <input type='checkbox'
                   name='jawaban[]'
                   value='$opt'
                   $cek>
            <span class='opsi-huruf'>$opt.</span>
            <span class='opsi-teks'>{$s[$p]}</span>
        </label>
        ";
    }

break;

case 'menjodohkan':
    // ===============================
    // PARSE JAWABAN BENAR
    // ===============================
    $pairs = explode('|', $s['kunci']);

    $kiri = [];
    $opsi = [];

    foreach ($pairs as $p) {
        [$k, $v] = explode(':', $p, 2);
        $kiri[] = trim($k);
        $opsi[] = trim($v);
    }

    // opsi unik & acak
    $opsi = array_unique($opsi);
    shuffle($opsi);

    // jawaban siswa (jika ada)
    $jawab = is_array($jawaban_arr) ? $jawaban_arr : [];

    echo "<table class='table-jodoh'>";

    foreach ($kiri as $k) {

        echo "<tr>";
        echo "<td class='jodoh-kiri'><strong>$k</strong></td>";
        echo "<td class='jodoh-kanan'>";

        foreach ($opsi as $o) {

            $cek = (isset($jawab[$k]) && $jawab[$k] == $o) ? 'checked' : '';

            echo "
            <label class='jodoh-opsi'>
                <input type='radio'
                       name='jawaban[".htmlspecialchars($k)."]'
                       value='".htmlspecialchars($o)."'
                       $cek>
                $o
            </label>
            ";
        }

        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";

break;


case 'uraian':
    echo "<textarea name='jawaban' rows='6'>".htmlspecialchars($jawaban)."</textarea>";
break;
}
?>

<button class="btn btn-success mt-3">💾 Simpan Jawaban</button>
<div class="mt-4 d-flex justify-content-between">
<?php if ($no > 1): ?>
<a href="?no=<?= $no-1 ?>" class="btn btn-outline-secondary">⬅ Sebelumnya</a>
<?php endif; ?>

<?php if ($no < $total): ?>
<a href="?no=<?= $no+1 ?>" class="btn btn-primary">Berikutnya ➡</a>
<?php else: ?>
<a href="selesai.php" class="btn btn-danger"
onclick="return confirm('Akhiri ujian?')">Selesai</a>
<?php endif; ?>
</div>


</form>
</div>
</div>

</body>
</html>

