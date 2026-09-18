<?php
require_once '../inc/config.php';
$id_ujian = $_GET['id_ujian'] ?? '';
$id_siswa = $_GET['id_siswa'] ?? '';

$ta = mysqli_query($db, "SELECT * FROM `reset`");

if(mysqli_num_rows($ta) > 0){
?>
<h5>Daftar Permintaan Reset</h5>
<table class="table table-bordered table-striped">
<tr>
    <th>Nama Siswa</th>
    <th>Aksi</th>
</tr>
<?php
while($da=mysqli_fetch_assoc($ta)){
    echo '<tr>';
    echo '<td>'.$da['nama'].'</td>';
    echo '<td><a href="reset_siswa.php?id_siswa='.$da['id_siswa'].'&id_ujian='.$id_ujian.'" class="btn btn-danger">Reset</a></td>';
    echo '</tr>';
}
?>
</table>
<?php } ?>
