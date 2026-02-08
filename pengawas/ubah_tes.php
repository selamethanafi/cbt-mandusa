<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

// Pastikan ID soal ada di URL
if (!isset($_GET['id'])) {
header('Location: daftar_tes.php');
exit();
}
$id = $_GET['id'];

// Ambil data soal berdasarkan ID
$query = "SELECT * FROM ujian_aktif WHERE id_ujian = '$id'";
$result = $db->query($query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
echo "Soal tidak ditemukan!";
exit();
}

// ✅ Jika soal status = aktif, tampilkan SweetAlert + redirect
if ($row['status'] == 'Aktif') {
echo 'Soal ini sudah aktif dan tidak bisa diedit!';
					?>
					<script>
					// Auto redirect setelah 2 detik
					setTimeout(function(){
					window.location.href = 'daftar_tes.php';
					}, 2000);
					</script>
					<?php	
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
	// Update data soal
	$tanggal = $_POST['tanggal'];
	$date = new DateTime($tanggal);
	$date->modify('+1 month');
	$tanggal_selesai = $date->format('Y-m-d H:i:s');
	$stmt = $db->prepare("UPDATE ujian_aktif SET tahun = ?, semester = ?, nama_soal = ?, mapel = ?, kelas = ?, tampilan_soal = ?, waktu_ujian = ?, tanggal = ?, tanggal_selesai = ?, user_id = ?, exambrowser = ? WHERE id_ujian = ?");
	$stmt->bind_param("ssssssississ", $_POST['tahun'], $_POST['semester'], $_POST['nama_soal'], $_POST['mapel'], $_POST['kelas'], $_POST['tampilan_soal'], $_POST['waktu_ujian'], $_POST['tanggal'], $tanggal_selesai, $_POST['id_user'], $_POST['exambrowser'], $_POST['id_ujian']
);

	if ($stmt->execute()) 
	{
		echo 'Data soal berhasil diupdate!';
		echo '<br />sukses memperbarui data tes';
					?>
					<script>
					// Auto redirect setelah 2 detik
					setTimeout(function(){
					window.location.href = 'daftar_tes.php';
					}, 1000);
					</script>
					<?php	
					exit;
	} else 
	{
		echo "Error: " . mysqli_error($db);
	}
$stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Ujian</title>
<link rel="stylesheet" href="../css/style.css">
</head>

<body>
 <div class="container-fluid">
 <div class="card">
<div class="card-header">
<h5 class="card-title mb-0">Edit Soal</h5>
</div>
<div class="card-body">
<?php
// Ambil data kelas dari tabel siswa secara DISTINCT
$query_kelas = "SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC";
$result_kelas = $db->query($query_kelas);
?>
<form method="POST">
<div class="mb-3">
<h2>Kode Soal : <?php echo $row['kode_soal']; ?></h2>
<input type="hidden" class="form-control" id="id_ujian" name="id_ujian" value="<?php echo $row['id_ujian']; ?>" required>
</div>
<div class="mb-3">
<label for="tampilan_soal" class="form-label">Tahun</label>
<input type="number" class="form-control" id="tampilan_soal" name="tahun" value="<?php echo $row['tahun'];?>" required>
</div>
	  <div class="mb-3">
<label for="tampilan_soal" class="form-label">Semester</label>
<input type="number" class="form-control" id="tampilan_soal" name="semester" value="<?php echo $row['semester'];?>" required>

</div>

<div class="mb-3">
<label for="nama_soal" class="form-label">Nama Soal</label>
<input type="text" class="form-control" id="nama_soal" name="nama_soal" value="<?php echo $row['nama_soal']; ?>" required>
</div>
<div class="mb-3">
<label for="mapel" class="form-label">Mata Pelajaran</label>
<input type="text" class="form-control" id="mapel" name="mapel" value="<?php echo $row['mapel']; ?>" required>
</div>
<div class="mb-3">
<label for="kelas" class="form-label">Kelas</label>
<select class="form-control" id="kelas" name="kelas" required>
 <option value="<?php echo $row['kelas']; ?>"> <?php echo $row['kelas'];?></option>

<?php while ($kelas_row = mysqli_fetch_assoc($result_kelas)): ?>
<option value="<?php echo $kelas_row['kelas']; ?>" <?php echo ($kelas_row['kelas'] == $row['kelas']) ? 'selected' : ''; ?>>
<?php echo $kelas_row['kelas']; ?>
</option>
<?php endwhile; ?>
</select>
</div>
<div class="mb-3">
<label for="waktu_ujian" class="form-label">Waktu Ujian (Menit)</label>
<input type="number" class="form-control" id="waktu_ujian" name="waktu_ujian" value="<?php echo $row['waktu_ujian']; ?>" required>
</div>
<div class="mb-3">
<label for="tampilan_soal" class="form-label">Tampilan Soal</label>
<select class="form-control" id="tampilan_soal" name="tampilan_soal" required>
<option value="<?php echo $row['tampilan_soal']; ?>"><?php echo $row['tampilan_soal']; ?></option>
<option value="Acak">Acak</option>
<option value="Urut">Urut</option>
</select>
</div>
<div class="mb-3">
<label for="tanggal" class="form-label">Tanggal Ujian</label>
<input type="datetime-local" class="form-control" id="tanggal" name="tanggal" value="<?php echo $row['tanggal']; ?>" required onclick="this.showPicker()">
</div>

   <div class="mb-3">
<label for="tampilan_soal" class="form-label">Menggunakan Exambrowser</label>
<select class="form-control" id="tampilan_soal" name="exambrowser" required>
<?php
if($row['exambrowser'] == '1')
{?>
<option value="<?php echo $row['exambrowser']; ?>">Ya</option>
<option value="0">Tidak</option> 
<?php  
}
else

{?>
<option value="<?php echo $row['exambrowser']; ?>">Tidak</option>
<option value="1">Ya</option> 
<?php  
}
?> 
</select>
</div>
   <div class="mb-3">
<label for="tampilan_soal" class="form-label">Guru Pengampu</label>
<select class="form-control" id="tampilan_soal" name="id_user" required>
<?php
$user_idx = $row['user_id'];
?>
<option value="<?php echo $user_idx; ?>"><?php echo $user_idx; ?></option>

</select>
</div>
<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
										<a href="daftar_tes.php" class="btn btn-danger">Batal</a>
</form>
</div>
</body>
</html>

