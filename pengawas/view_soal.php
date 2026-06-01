<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$id_ujian = $_GET['id_ujian'] ?? '';

$stmt = $db->prepare("
    SELECT id, nomer_soal, kode_soal, tipe, soal, a, b, c, d, e, pasangan, kunci
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
<a href="unduh_soal.php?id=<?= $id_ujian;?>">Unduh soal</a>
<?php
$no = 1;
$kunci_jawaban = '';
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
$tipe = $row['tipe'];
$id_soal = $row['id'];
$query = "update `jawaban` set `tipe` ='$tipe' where `id_soal` = '$id_soal'";
mysqli_query($db, $query);
if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $row['kunci'];
		}
		else
		{
			$kunci_jawaban .= '#'.$row['kunci'];
		}      
$no++;

endwhile;

echo 'Kunci '.$kunci_jawaban;?>
<br /><br />
<a href="perbarui_kunci.php?id_ujian=<?= $id_ujian;?>">Perbarui Kunci</a> <a href="koreksi_per_ujian.php?id_ujian=<?= $id_ujian;?>">Koreksi Per Ujian</a>
</body>
</html>

