<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$id_siswa = isset($_GET['id_siswa']) ? $db->real_escape_string($_GET['id_siswa']) : '';
$id_ujian = isset($_GET['id_ujian']) ? $db->real_escape_string($_GET['id_ujian']) : '';

$query = "
SELECT id, id_siswa, id_ujian, id_soal, jawaban, nomer_soal, nilai , tipe, waktu_menjawab
FROM jawaban 
WHERE id_siswa = '$id_siswa' 
AND id_ujian = '$id_ujian'
ORDER BY nomer_soal ASC
";

$q = $db->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Jawaban Siswa</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h3>Detail Jawaban Siswa</h3>

    <table>
        <thead>
            <tr>
                <th width="50">No</th>
                <th width="100">ID Soal</th>
                <th width="100">Jawaban</th>
                <th width="100">Kunci</th>
                <th>Nilai</th>
                <th>Tipe Soal</th>                
                <th>Waktu Menjawab</th>              
            </tr>
        </thead>
        <tbody>
        <?php
        $kunci_jawaban = '';
        $benar = 0;
        if ($q->num_rows > 0) {
            $no = 1;
            while ($row = $q->fetch_assoc()) {
            $id = $row['id_soal'];
            $ts = $db->query("SELECT * FROM `soal` WHERE `id` = '$id'");
            $ds = mysqli_fetch_assoc($ts);
            $kunci = $ds['kunci'] ?? '?';
            if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $row['jawaban'];
		}
		else
		{
			$kunci_jawaban .= '#'.$row['jawaban'];
		}      
	if($kunci == $row['jawaban'])
	{
	$benar++;
	}
                echo "<tr>
                        <td>{$row['nomer_soal']}</td>
                        <td>{$id}</td>
                        <td>{$row['jawaban']}</td>
                        <td>{$kunci}</td>                        
                        <td>{$row['nilai']}</td>
                        <td>{$row['tipe']}</td>
                        <td>{$row['waktu_menjawab']}</td>                        
                      </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='4'>Tidak ada data</td></tr>";
        }
        ?>
        </tbody>
    </table>
    Nilai 
    <?= $benar;?></br>
    <?= $kunci_jawaban;?>
</div>

</body>
</html>
