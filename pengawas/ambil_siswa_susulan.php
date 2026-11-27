<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mengirim Jawaban Siswa</title>
<link rel="stylesheet" href="../css/style.css">
<?php
header('Content-Type: text/html; charset=utf-8');

// =====================================
// AMBIL DATA DARI SIMAMAD
// =====================================

$url = $sianis . '/tukardata/siswasusulan';

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'app_key' => $key
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
    ],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// =====================================
// TAMPILAN CONTAINER
// =====================================

echo '<div class="container">';

// =====================================
// CEK KONEKSI
// =====================================

if ($error) {
    echo '
    <div class="card shadow-sm">
        <h3>Koneksi SIMAMAD Gagal</h3>
        <div class="alert-danger">
            ' . htmlspecialchars($error) . '
        </div>
    </div>';
    exit;
}

if ($httpCode != 200) {
    echo '
    <div class="card shadow-sm">
        <h3>Pengambilan Data Gagal</h3>
        <div class="alert-danger">
            HTTP: ' . (int)$httpCode . '<br>
            ' . htmlspecialchars($response) . '
        </div>
    </div>';
    exit;
}

// =====================================
// DECODE JSON
// =====================================

$hasil = json_decode($response, true);

if (!$hasil || ($hasil['status'] ?? '') !== 'berhasil') {
    echo '
    <div class="card shadow-sm">
        <h3>Data Tidak Valid</h3>
        <div class="alert-danger">
            Respons SIMAMAD tidak valid.
        </div>
    </div>';
    exit;
}
$query= "update siswa set `nis` = '1' where 1";
$q = $db->query($query);
// =====================================
// INSERT KE TABEL CBT
// =====================================

$stmt = $db->prepare("
    INSERT IGNORE INTO siswa_susulan
    (id_siswa, id_ujian)
    VALUES (?, ?)
");

if (!$stmt) {
    echo '
    <div class="card shadow-sm">
        <h3>Database Error</h3>
        <div class="alert-danger">
            Gagal menyiapkan query database.
        </div>
    </div>';
    exit;
}
// Update tabel siswa
$update = $db->prepare("
    UPDATE siswa
    SET nis = ?
    WHERE id_siswa = ?
");

// Insert siswa susulan
$stmt = $db->prepare("
    INSERT IGNORE INTO siswa_susulan
    (id_siswa, id_ujian)
    VALUES (?, ?)
");

$jumlah = 0;
$sudah_ada = 0;
$gagal = 0;

foreach ($hasil['data'] as $row) {

    $id_siswa = (int) $row['nis'];
    $id_ujian = (int) $row['tes_id'];

    // Update tabel siswa
    $nis = 0;

    $update->bind_param("ii", $nis, $id_siswa);
    $update->execute();

    // Insert siswa susulan
    $stmt->bind_param("ii", $id_siswa, $id_ujian);

    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {
            $jumlah++;
        } else {
            $sudah_ada++;
        }

    } else {
        $gagal++;
    }
}

$update->close();
$stmt->close();

// =====================================
// HITUNG HASIL
// =====================================

$total = count($hasil['data']);
$thnajaran = htmlspecialchars($hasil['thnajaran'] ?? '-');

// =====================================
// TAMPILAN HASIL SINKRONISASI
// =====================================

echo '
<div class="card shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 style="margin:0;">Sinkronisasi Siswa Susulan</h3>
        <span class="badge bg-success">Selesai</span>
    </div>

    <hr>

    <p>
        Tahun Ajaran :
        <strong>' . $thnajaran . '</strong>
    </p>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead>
                <tr>
                    <th>Data SIMAMAD</th>
                    <th>Data Baru</th>
                    <th>Sudah Ada</th>
                    <th>Gagal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>' . $total . '</strong>
                    </td>
                    <td>
                        <strong style="color:#198754;">
                            ' . $jumlah . '
                        </strong>
                    </td>
                    <td>
                        <strong style="color:#d97706;">
                            ' . $sudah_ada . '
                        </strong>
                    </td>
                    <td>
                        <strong style="color:#dc3545;">
                            ' . $gagal . '
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card shadow-sm" style="background:#f8f9fa;">

        <div class="d-flex align-items-center" style="gap:10px;">
            <span class="badge bg-success" style="font-size:14px;">
                ' . ($gagal == 0 ? 'BERHASIL' : 'PERHATIAN') . '
            </span>

            <strong>
                ' . ($gagal == 0
                    ? 'Sinkronisasi siswa susulan berhasil.'
                    : 'Sinkronisasi selesai, tetapi ada data gagal dimasukkan.'
                ) . '
            </strong>
        </div>

    </div>

    <div class="mt-3">
        <a href="javascript:history.back()" class="btn btn-secondary">
            Kembali
        </a>
    </div>

</div>';

echo '</div>';

