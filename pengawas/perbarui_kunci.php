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
$no = 1;
$kunci_jawaban = '';
while ($row = $result->fetch_assoc()):
$id = $row['id'];
/* ===============================
   TIPE PILIHAN GANDA
   =============================== */
if($row['tipe'] == "pg")
{
    $kuncine = $row['kunci'];
    if($row['kunci'] == 'Pilihan_1')
    {
        $kuncine = 'A';
    	if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $kuncine;
		}
		else
		{
			$kunci_jawaban .= '#'.$kuncine;
		}        
    }
    else if($row['kunci'] == 'Pilihan_2')
    {
    	$kuncine = 'B';
    	if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $kuncine;
		}
		else
		{
			$kunci_jawaban .= '#'.$kuncine;
		}        

    }
    else if($row['kunci'] == 'Pilihan_3')
    {
    	$kuncine = 'C';
    	if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $kuncine;
		}
		else
		{
			$kunci_jawaban .= '#'.$kuncine;
		}        
        
    }
    else if($row['kunci'] == 'Pilihan_4')
    {
    	$kuncine = 'D';
    	if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $kuncine;
		}
		else
		{
			$kunci_jawaban .= '#'.$kuncine;
		}        
        
    }
    else if($row['kunci'] == 'Pilihan_5')
    {
        $kuncine = 'E';
    	if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $kuncine;
		}
		else
		{
			$kunci_jawaban .= '#'.$kuncine;
		}        
      
    }
     $sql = "update `soal` set `kunci` = '$kuncine' where `id`='$id'";
	    $db->query($sql);

}
else
{
    if(empty($kunci_jawaban))
		{
			$kunci_jawaban .= $row['kunci'];
		}
		else
		{
			$kunci_jawaban .= '#'.$row['kunci'];
		} 
}

echo "</div>";

$no++;

endwhile;
echo $kunci_jawaban;
?>
<script>setTimeout(function () {
			   window.location.href= 'view_soal.php?id_ujian=<?php echo $id_ujian;?>';
				},10);
			</script>
			<?php

