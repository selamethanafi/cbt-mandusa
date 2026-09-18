<?php

require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

// =====================================================
// AMBIL PARAMETER
// =====================================================

$nopes = $_GET['nopes'] ?? '';
$kode  = $_GET['kode'] ?? '';

$nopes = trim($nopes);
$kode  = trim($kode);

if ($nopes === '' || $kode === '') {
    die('Nomor peserta atau kode tes tidak ditemukan.');
}
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='url_cbt'");
$da = mysqli_fetch_assoc($ta);
$url = $da['konfigurasi_isi'] ?? '';

// =====================================================
// CARI DATA SISWA
// =====================================================

$nopes_esc = $db->real_escape_string($nopes);

$sql = "
    SELECT *
    FROM siswa
    WHERE username = '$nopes_esc'
    LIMIT 1
";

$result = $db->query($sql);

if (!$result || $result->num_rows == 0) {
    die('Data peserta tidak ditemukan.');
}

$row = $result->fetch_assoc();


// =====================================================
// DATA PESERTA
// =====================================================

$nama = $row['nama_siswa'] ?? $nopes;


// =====================================================
// LINK YANG AKAN DIMASUKKAN KE QR CODE
// =====================================================

$link_tes = $url.'/peserta/login.php?nopes='
          . urlencode($nopes)
          . '&kode='
          . urlencode($kode);


// =====================================================
// FILE QR CODE
// =====================================================

require_once __DIR__ . '/phpqrcode/qrlib.php';


// Folder penyimpanan QR sementara
$folder_qr = __DIR__ . '/qrcodes/';

if (!is_dir($folder_qr)) {
    mkdir($folder_qr, 0755, true);
}


// Nama file QR
$nama_file_qr = 'qr_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $nopes) . '.png';

$file_qr = $folder_qr . $nama_file_qr;


// =====================================================
// GENERATE QR CODE
// =====================================================

QRcode::png(
    $link_tes,
    $file_qr,
    QR_ECLEVEL_H,
    8,
    2
);


// URL QR untuk ditampilkan di browser
$url_qr = 'qrcodes/' . $nama_file_qr;

?>
<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>QR Code Tes - <?= htmlspecialchars($nama) ?></title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 20px;

    background: #f2f2f2;

    font-family:
        Arial,
        Helvetica,
        sans-serif;
}

.container {
    width: 100%;
    max-width: 500px;

    margin: 30px auto;

    background: #ffffff;

    padding: 30px;

    border-radius: 12px;

    box-shadow:
        0 4px 15px rgba(0,0,0,0.12);

    text-align: center;
}

.judul {
    font-size: 24px;
    font-weight: bold;

    margin-bottom: 10px;
}

.subjudul {
    color: #666;

    margin-bottom: 20px;
}

.qr {
    margin: 20px auto;

    padding: 10px;

    display: inline-block;

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 8px;
}

.qr img {
    display: block;

    width: 300px;
    height: 300px;

    max-width: 100%;
}

.nama {
    font-size: 22px;

    font-weight: bold;

    margin-top: 15px;
}

.nopes {
    font-size: 18px;

    margin-top: 5px;

    color: #444;
}

.kode {
    font-size: 14px;

    margin-top: 5px;

    color: #777;
}

.petunjuk {
    margin-top: 20px;

    padding: 15px;

    background: #f7f7f7;

    border-radius: 8px;

    font-size: 15px;

    line-height: 1.5;
}

.tombol {
    margin-top: 25px;
}

.btn {
    display: inline-block;

    padding: 12px 22px;

    margin: 5px;

    border: none;

    border-radius: 6px;

    font-size: 15px;

    cursor: pointer;

    text-decoration: none;
}

.btn-print {
    background: #198754;

    color: white;
}

.btn-close {
    background: #6c757d;

    color: white;
}

.link-tes {
    margin-top: 15px;

    font-size: 12px;

    color: #888;

    word-break: break-all;
}


/* ========================================
   MODE CETAK
   ======================================== */

@media print {

    @page {
        size: A4 portrait;

        margin: 10mm;
    }

    body {
        background: white;

        padding: 0;
    }

    .container {
        max-width: none;

        width: 100%;

        margin: 0;

        padding: 10px;

        box-shadow: none;

        border-radius: 0;
    }

    .tombol {
        display: none;
    }

    .link-tes {
        display: none;
    }

    .qr img {
        width: 350px;
        height: 350px;
    }

    .petunjuk {
        background: white;

        border: 1px solid #ddd;
    }

}

</style>

</head>

<body>

<div class="container">

    <div class="judul">
        QR CODE TES
    </div>

    <div class="subjudul">
        Silakan scan QR Code untuk masuk ke tes
    </div>


    <!-- QR CODE -->

    <div class="qr">

        <img
            src="<?= htmlspecialchars($url_qr) ?>"
            alt="QR Code Tes">

    </div>


    <!-- DATA PESERTA -->

    <div class="nama">
        <?= htmlspecialchars($nama) ?>
    </div>

    <div class="nopes">
        Nomor Peserta:
        <strong>
            <?= htmlspecialchars($nopes) ?>
        </strong>
    </div>

    <div class="kode">
        Password:
        <?= htmlspecialchars($kode) ?>
    </div>


    <!-- PETUNJUK -->

    <div class="petunjuk">

        <strong>Petunjuk:</strong><br>

        Silakan scan QR Code menggunakan kamera
        atau aplikasi QR Scanner pada HP.

        Setelah QR Code berhasil dipindai,
        halaman login tes akan terbuka otomatis.

    </div>


    <!-- LINK TES -->

    <div class="link-tes">

        <?= htmlspecialchars($link_tes) ?>

    </div>


    <!-- TOMBOL -->

    <div class="tombol">

        <button
            type="button"
            class="btn btn-print"
            onclick="window.print()">

            🖨 Cetak QR Code

        </button>


        <button
            type="button"
            class="btn btn-close"
            onclick="window.close()">

            Tutup

        </button>

    </div>

</div>

</body>

</html>
