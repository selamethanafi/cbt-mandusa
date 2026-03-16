<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$id_ujian = $_GET['id_ujian'] ?? '';

$stmt = $db->prepare("
    SELECT nomer_soal, kode_soal, tipe, soal, a, b, c, d, e, pasangan, kunci
    FROM soal
    WHERE id_ujian = ?
    ORDER BY nomer_soal
");

$stmt->bind_param("s", $id_ujian);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Daftar Soal</title>

<style>

body{
    font-family: Arial;
    margin:20px;
}

.soalbox{
    border:1px solid #ccc;
    padding:15px;
    margin-bottom:20px;
    border-radius:6px;
}

.nomor{
    font-weight:bold;
    margin-bottom:10px;
}

.pilihan{
    margin-left:20px;
    line-height:1.8;
}

.kunci{
    margin-top:10px;
    color:green;
    font-weight:bold;
}

.pasangan{
    margin-left:20px;
}

</style>

</head>

<body>

<h2>Daftar Soal Ujian</h2>

<?php
$no = 1;

while ($row = $result->fetch_assoc()):

echo "<div class='soalbox'>";

echo "<div class='nomor'>Soal $no ({$row['tipe']})</div>";

echo "<div class='pertanyaan'>{$row['soal']}</div>";

/* ===============================
   TIPE PILIHAN GANDA
   =============================== */
if($row['tipe'] == "pg"){

    echo "<div class='pilihan'>";

    echo "A. {$row['a']}<br>";
    echo "B. {$row['b']}<br>";
    echo "C. {$row['c']}<br>";
    echo "D. {$row['d']}<br>";
    echo "E. {$row['e']}<br>";

    echo "</div>";

}

if($row['tipe'] == "pg_kompleks"){

    echo "<div class='pilihan'>";

    echo "A. {$row['a']}<br>";
    echo "B. {$row['b']}<br>";
    echo "C. {$row['c']}<br>";
    echo "D. {$row['d']}<br>";
    echo "E. {$row['e']}<br>";

    echo "</div>";

}

/* ===============================
   TIPE MENJODOHKAN
   =============================== */
elseif($row['tipe'] == "menjodohkan"){

    echo "<div class='pasangan'>";

    if(!empty($row['pasangan'])){

        $pasangan = explode(";", $row['pasangan']);

        echo "<ol>";

        foreach($pasangan as $p){
            echo "<li>$p</li>";
        }

        echo "</ol>";

    }

    echo "</div>";

}

/* ===============================
   TIPE ESAI
   =============================== */
elseif($row['tipe'] == "ESAI"){

    echo "<div style='margin-top:15px; color:#999;'>Jawaban uraian...</div>";

}

echo "<div class='kunci'>Kunci: {$row['kunci']}</div>";

echo "</div>";

$no++;

endwhile;
?>

</body>
</html>
