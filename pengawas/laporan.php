<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$kelas   = $_GET['kelas'] ?? '';
$mapel   = $_GET['mapel'] ?? '';

// validasi tanggal
$d = DateTime::createFromFormat('Y-m-d', $tanggal);
if (!$d || $d->format('Y-m-d') !== $tanggal) {
    $tanggal = date('Y-m-d');
}

$awal  = $tanggal . ' 00:00:00';
$akhir = $tanggal . ' 23:59:59';

/*
|--------------------------------------------------------------------------
| Ambil daftar kelas
|--------------------------------------------------------------------------
*/
$qKelas = $db->query("SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC");
$daftarKelas = [];

while ($r = $qKelas->fetch_assoc()) {
    $daftarKelas[] = $r['kelas'];
}

/*
|--------------------------------------------------------------------------
| Ambil daftar mapel
|--------------------------------------------------------------------------
*/
$qMapel = $db->query("SELECT DISTINCT nama_soal FROM ujian_aktif ORDER BY nama_soal ASC");
$daftarMapel = [];

while ($r = $qMapel->fetch_assoc()) {
    $daftarMapel[] = $r['nama_soal'];
}

/*
|--------------------------------------------------------------------------
| Query utama
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT 
        u.id_siswa,
        s.nama_siswa,
        s.kelas,
        ua.nama_soal,
        u.mulai,
        u.selesai,
        u.status
    FROM ujian u
    INNER JOIN siswa s ON s.id_siswa = u.id_siswa
    INNER JOIN ujian_aktif ua ON ua.id_ujian = u.id_ujian
    WHERE u.mulai BETWEEN ? AND ?
";
//$sql .= " ORDER BY s.nama_siswa ASC";
$params = [$awal, $akhir];
$types  = "ss";

if (!empty($kelas)) {
    $sql .= " AND s.kelas = ?";
    $params[] = $kelas;
    $types .= "s";
}

if (!empty($mapel)) {
    $sql .= " AND ua.nama_soal = ?";
    $params[] = $mapel;
    $types .= "s";
}

$sql .= " ORDER BY s.nama_siswa ASC";

$stmt = $db->prepare($sql);

if (!$stmt) {
    die("Prepare gagal: " . $db->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();

$result = $stmt->get_result();

$dataPesan = [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ujian</title>
    <style>
        body{
            font-family:Arial;
            margin:20px;
        }

        form{
            margin-bottom:20px;
            padding:15px;
            background:#f5f5f5;
            border-radius:8px;
        }

        select,input,button{
            padding:8px;
            margin:5px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        th,td{
            border:1px solid #ccc;
            padding:8px;
            text-align:left;
        }

        th{
            background:#eaeaea;
        }

        textarea{
            width:100%;
            margin-top:10px;
            padding:10px;
        }

        .btn-copy{
            background:#28a745;
            color:white;
            border:none;
            cursor:pointer;
        }

        .btn-copy:hover{
            background:#218838;
        }
    </style>
</head>
<body>

<h2>Laporan Ujian</h2>
<a href="menu.php">Kembali ke Menu</a>
<form method="get">
    <label>Tanggal:</label>
    <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">

    <label>Kelas:</label>
    <select name="kelas">
        <option value="">Semua Kelas</option>
        <?php foreach ($daftarKelas as $k): ?>
            <option value="<?= htmlspecialchars($k) ?>" <?= ($kelas == $k ? 'selected' : '') ?>>
                <?= htmlspecialchars($k) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Mapel:</label>
    <select name="mapel">
        <option value="">Semua Mapel</option>
        <?php foreach ($daftarMapel as $m): ?>
            <option value="<?= htmlspecialchars($m) ?>" <?= ($mapel == $m ? 'selected' : '') ?>>
                <?= htmlspecialchars($m) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filter</button>
</form>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>ID Siswa</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Nama Tes</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php $no = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $dataPesan[] = "• {$row['nama_siswa']} ({$row['kelas']}) telah mengerjakan {$row['nama_soal']}";
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['id_siswa']) ?></td>
                    <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                    <td><?= htmlspecialchars($row['nama_soal']) ?></td>
                    <td><?= htmlspecialchars($row['mulai']) ?></td>
                    <td><?= htmlspecialchars($row['selesai']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tidak ada data</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$templatePesan = '';

if (!empty($dataPesan)) {
    $templatePesan =
        "Menyampaikan informasi daftar siswa yang telah mengerjakan ujian tanggal {$tanggal}:\n\n" .
        implode("\n", $dataPesan) .
        "\n\nMohon perhatian walikelas dan guru mapel terkait.\nTerima kasih.";
}
?>

<?php if (!empty($templatePesan)): ?>
    <h3>Template Pesan Grup Guru Mapel</h3>

    <button class="btn-copy" onclick="copyPesan()">Copy Pesan</button>

    <textarea id="pesan" rows="15"><?= htmlspecialchars($templatePesan) ?></textarea>
<?php endif; ?>

<script>
function copyPesan() {
    const textarea = document.getElementById('pesan');
    textarea.select();
    textarea.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(textarea.value);

    alert('Pesan berhasil disalin');
}
</script>

</body>
</html>

<?php
$stmt->close();
$db->close();
?>
