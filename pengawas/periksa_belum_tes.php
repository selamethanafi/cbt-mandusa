<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$waktu = 10;
// ======================================================
// POSISI SISWA
// ======================================================
if (isset($_GET['ke'])) {
    $ke = (int) $_GET['ke'];
} else {
    $ke = 0;
    $sql = "update `siswa` set `nis` = '1' where 1";
	$insert = $db->query($sql); 
}
// ======================================================
// AMBIL 1 SISWA
// ======================================================
$qs = $db->query(
    "SELECT *
     FROM `siswa`
     WHERE `rombel` = '$ruang'
     LIMIT $ke,1"
);

// ======================================================
// JIKA SEMUA SISWA SUDAH DIPERIKSA
// ======================================================
if (mysqli_num_rows($qs) == 0) {

    echo 'Rampung';
    echo '<br><a href="menu.php">Kembali ke Menu</a>';
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Memeriksa Peserta Belum Tes</title>

    <link rel="stylesheet"
          href="../css/style.css">

</head>

<body>

<?php

// ======================================================
// PROSES SISWA
// ======================================================
while ($ds = mysqli_fetch_assoc($qs)) {

    $id_siswa = (int) $ds['id_siswa'];
    $nis      = $id_siswa ;
    $nama     = $ds['nama_siswa'];
    $kelas    = $ds['kelas'];

    echo '<strong>' . htmlspecialchars($nama) . '</strong>';
    echo ' (' . htmlspecialchars($nis) . ')';
    echo '<br>';


// ======================================================
// URL API
// ======================================================
    $url = $sianis . '/cbt/caritesbelum';


// ======================================================
// PARAMETER API
// ======================================================
    $params = [
        'app_key' => $key,
        'nis'     => $nis
    ];


// ======================================================
// PANGGIL API
// ======================================================
    $json = postcurl($url, $params);


    if (!$json) {

        echo 'Gagal menghubungi server API.<br>';

    } else {


// ======================================================
// DECODE JSON
// ======================================================
        $data = json_decode($json, true);

// ======================================================
// CEK JSON
// ======================================================
        if (json_last_error() !== JSON_ERROR_NONE) {

            echo 'Response JSON tidak valid.<br>';

        } elseif (!is_array($data)) {

            echo 'Response bukan array.<br>';

        } else {

// ======================================================
// PROSES DATA HASIL API
// ======================================================
            foreach ($data as $row) {


// ==================================================
// DATA TES DITEMUKAN
// ==================================================
                if (
                    isset($row['nis']) &&
                    isset($row['tes_id'])
                ) {

                    $nis_api = $row['nis'];
                    $tes_id  = $row['tes_id'];

                    echo 'Tes ditemukan: '
                        . htmlspecialchars($tes_id)
                        . '<br>';
// ==================================================
// CARI ID UJIAN
// ==================================================
                    $stmt = $db->prepare(
                        "SELECT `id_ujian`
                         FROM `ujian_aktif`
                         WHERE `kode_soal` = ?
                         LIMIT 1"
                    );


                    if (!$stmt) {

                        echo 'Prepare query ujian gagal: '
                            . htmlspecialchars($db->error)
                            . '<br>';

                        continue;
                    }


                    $stmt->bind_param(
                        's',
                        $tes_id
                    );

                    $stmt->execute();

                    $result = $stmt->get_result();

                    $ujian = $result->fetch_assoc();

                    $stmt->close();


// ==================================================
// JIKA ID UJIAN DITEMUKAN
// ==================================================
                    if ($ujian) {

                        $id_ujian = (int) $ujian['id_ujian'];
// ==================================================
// INSERT SISWA SUSULAN
// id_siswa = nis
// ==================================================
                        $stmt = $db->prepare(
                            "INSERT IGNORE INTO `siswa_susulan`
                             (`id_siswa`, `id_ujian`)
                             VALUES (?, ?)"
                        );


                        if (!$stmt) {

                            echo 'Prepare insert gagal: '
                                . htmlspecialchars($db->error)
                                . '<br>';

                            continue;
                        }


                        $stmt->bind_param(
                            'ii',
                            $nis_api,
                            $id_ujian
                        );


                        if ($stmt->execute()) {

                            if ($stmt->affected_rows > 0) {

                                echo '→ Ditambahkan ke peserta susulan'
                                    . '<br>';

                            } else {

                                echo '→ Sudah terdaftar sebagai susulan'
                                    . '<br>';
                            }
				$token = substr(str_shuffle('123456789'), 0, 6);
				$sql = "update `siswa` set `password` = '$token', `nis` = '0' where `id_siswa` = '$id_siswa'";
				$insert = $db->query($sql); 	
				$url = $sianis.'/cbt/updatepassword';
				$params=[
					'app_key'=>$key,
					'password' => $token,
					'nis' => $id_siswa,
					];
				if($hasil = postcurl($url,$params))
				{
					echo ' Jawaban dari Simamad '.$hasil.'<br />';
				}

                        } else {

                            echo '→ Gagal insert: '
                                . htmlspecialchars($stmt->error)
                                . '<br>';
                        }


                        $stmt->close();

                    } else {

                        echo '→ ID ujian tidak ditemukan untuk '
                            . htmlspecialchars($tes_id)
                            . '<br>';
                    }
                }


// ==================================================
// RESPONSE PESAN DARI API
// ==================================================
                elseif (
                    isset($row['pesan'])
                ) {

                    $pesan = $row['pesan'];

                    echo '→ '
                        . htmlspecialchars($pesan)
                        . '<br>';
                }
            }
        }
    }


// ======================================================
// PEMISAH
// ======================================================
    echo '<hr>';


// ======================================================
// LANJUT KE SISWA BERIKUTNYA
// ======================================================
    $ke++;

?>

<script>

setTimeout(function () {

    window.location.href =
        'periksa_belum_tes.php?ke=<?php echo $ke; ?>';

}, <?php echo $waktu; ?>);

</script>

<?php

}

?>

</body>
</html>
