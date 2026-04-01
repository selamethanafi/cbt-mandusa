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
SELECT id, id_siswa, id_ujian, id_soal, jawaban, nomer_soal, nilai 
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
                <th>No</th>
                <th>ID Soal</th>
                <th>Jawaban</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($q->num_rows > 0) {
            $no = 1;
            while ($row = $q->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['nomer_soal']}</td>
                        <td>{$row['id_soal']}</td>
                        <td>{$row['jawaban']}</td>
                        <td>{$row['nilai']}</td>
                      </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='4'>Tidak ada data</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
