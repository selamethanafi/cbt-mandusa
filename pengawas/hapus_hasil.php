<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

// validasi parameter
$id_siswa = isset($_GET['id_siswa']) ? (int)$_GET['id_siswa'] : 0;
$id_ujian = isset($_GET['id_ujian']) ? (int)$_GET['id_ujian'] : 0;

if ($id_siswa <= 0 || $id_ujian <= 0) {
    die('Parameter tidak valid');
}

// buat token csrf
if (empty($_SESSION['hapus_token'])) {
    $_SESSION['hapus_token'] = bin2hex(random_bytes(32));
}

$pesan = '';
$sukses = false;

// proses hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['hapus_token'] ?? '';

    if (!hash_equals($_SESSION['hapus_token'], $token)) {
        die('Token keamanan tidak valid');
    }

    $id_siswa = (int)$_POST['id_siswa'];
    $id_ujian = (int)$_POST['id_ujian'];

    mysqli_begin_transaction($db);

    try {

        // hapus tabel nilai
        $stmt = mysqli_prepare($db, "DELETE FROM nilai WHERE id_siswa = ? AND id_ujian = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id_siswa, $id_ujian);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // hapus tabel jawaban
        $stmt = mysqli_prepare($db, "DELETE FROM jawaban WHERE id_siswa = ? AND id_ujian = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id_siswa, $id_ujian);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // hapus tabel ujian
        $stmt = mysqli_prepare($db, "DELETE FROM ujian WHERE id_siswa = ? AND id_ujian = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id_siswa, $id_ujian);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($db);

        unset($_SESSION['hapus_token']);

        $sukses = true;
        $pesan = "Hasil tes berhasil dihapus.";

    } catch (Exception $e) {
        mysqli_rollback($db);
        $pesan = "Gagal menghapus data.";
    }
}

// ambil info siswa untuk tampilan
$stmt = mysqli_prepare($db, "
    SELECT 
        s.nama_siswa,
        ua.nama_soal AS nama_ujian
    FROM ujian u
    INNER JOIN siswa s ON s.id_siswa = u.id_siswa
    INNER JOIN ujian_aktif ua ON ua.id_ujian = u.id_ujian
    WHERE u.id_siswa = ? AND u.id_ujian = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "ii", $id_siswa, $id_ujian);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Hapus Hasil Tes</title>
<link rel="stylesheet" href="../css/style.css">
<style>
.box-konfirmasi{
    max-width:600px;
    margin:40px auto;
    padding:20px;
    border:1px solid #ddd;
    border-radius:8px;
    background:#fff;
}
.btn{
    padding:10px 16px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    text-decoration:none;
    display:inline-block;
}
.btn-danger{
    background:#d9534f;
    color:#fff;
}
.btn-secondary{
    background:#6c757d;
    color:#fff;
}
.alert{
    padding:12px;
    margin-bottom:15px;
    border-radius:6px;
}
.alert-success{
    background:#d4edda;
    color:#155724;
}
.alert-error{
    background:#f8d7da;
    color:#721c24;
}
</style>
</head>
<body>
<div class="container-fluid">

<div class="box-konfirmasi">

    <h2>Konfirmasi Hapus Hasil Tes</h2>

    <?php if ($pesan): ?>
        <div class="alert <?= $sukses ? 'alert-success' : 'alert-error'; ?>">
            <?= htmlspecialchars($pesan); ?>
        </div>
    <?php endif; ?>

    <?php if (!$sukses): ?>

        <p><strong>Nama Siswa:</strong> <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?></p>
        <p><strong>Ujian:</strong> <?= htmlspecialchars($data['nama_ujian'] ?? '-'); ?></p>

        <p style="color:red;">
            Data berikut akan dihapus permanen:
        </p>
        <ul>
            <li>Nilai</li>
            <li>Jawaban siswa</li>
            <li>Data ujian</li>
        </ul>

        <form method="post" onsubmit="return confirm('Yakin ingin menghapus hasil tes ini?');">
            <input type="hidden" name="id_siswa" value="<?= $id_siswa; ?>">
            <input type="hidden" name="id_ujian" value="<?= $id_ujian; ?>">
            <input type="hidden" name="hapus_token" value="<?= $_SESSION['hapus_token']; ?>">

            <button type="submit" class="btn btn-danger">Hapus Permanen</button>
            <a href="daftar_tes.php" class="btn btn-secondary">Batal</a>
        </form>

    <?php else: ?>

        <a href="daftar_tes.php" class="btn btn-secondary">Kembali</a>

    <?php endif; ?>

</div>

</div>
</body>
</html>