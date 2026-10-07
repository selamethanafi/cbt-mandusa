<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$tanggal = $_GET['tanggal'] ?? '';
$pukul = $_GET['jam'] ?? '';
$waktu = $tanggal . ' ' . $pukul;

if (isValidDateTime($waktu)) {

} else {
    echo "Format waktu salah";
    ?>
    <script>
        setTimeout(function(){
            window.location.href = 'soal_hari_ini.php';
        }, 2000);
    </script>
    <?php
    exit;
}

$ta = mysqli_query($db, "SELECT * FROM cbt_konfigurasi WHERE konfigurasi_kode='kode_qr'");
$da = mysqli_fetch_assoc($ta);
$kode_qr = $da['konfigurasi_isi'] ?? '0';

$ta = mysqli_query($db, "SELECT * FROM cbt_konfigurasi WHERE konfigurasi_kode='kamera'");
$da = mysqli_fetch_assoc($ta);
$kamera = strtoupper($da['konfigurasi_isi'] ?? 'OFF');

$ta = mysqli_query($db, "SELECT * FROM cbt_konfigurasi WHERE konfigurasi_kode='token_kamera'");
$da = mysqli_fetch_assoc($ta);
$token_kamera = $da['konfigurasi_isi'] ?? 'sdfghfhgjhkhgkhgwdasda';

$ta = mysqli_query($db, "SELECT * FROM cbt_konfigurasi WHERE konfigurasi_kode='ubk_pusat'");
$da = mysqli_fetch_assoc($ta);
$ubk_pusat = $da['konfigurasi_isi'] ?? '';

$ta = mysqli_query($db, "SELECT * FROM cbt_konfigurasi WHERE konfigurasi_kode='ubk_stream'");
$da = mysqli_fetch_assoc($ta);
$ubk_stream = $da['konfigurasi_isi'] ?? '';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pengawasan</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="container-fluid">

    <h4 class="mb-2">Dashboard Pengawas</h4>

    <table width="100%">
        <tr>
            <td width="33%" align="left">
                <a href="menu.php">Menu</a>
            </td>

            <td align="center">
                Ruang:
                <strong><?= htmlspecialchars($ruang) ?></strong>
            </td>

            <td width="33%" align="right">
                Jam Peladen:
                <span id="jam-server"><?= date("d-m-Y H:i:s"); ?></span>
            </td>
        </tr>
    </table>

    <hr>

    <?php
    $ts = mysqli_query(
        $db,
        "SELECT * FROM siswa
         WHERE rombel = '$ruang'
         AND nis = '0'"
    );

    if (mysqli_num_rows($ts) > 0) {
        echo '<h2>Daftar Peserta Susulan</h2><h3>';

        while ($ds = mysqli_fetch_assoc($ts)) {
            echo htmlspecialchars($ds['nama_siswa']) . ', ';
        }

        echo '</h3>';
    }
    ?>

    <div id="reset-container">
        <!-- nanti diisi via AJAX -->
    </div>

    <div id="pekerjaan-container">
        <!-- nanti diisi via AJAX -->
    </div>

    <!-- Alarm -->
    <div class="mt-2 mb-2">
        <button type="button" id="btnAlarm" class="btn btn-danger btn-sm">
            🔔 Aktifkan Alarm
        </button>

        <span id="statusAlarm" class="small text-muted ms-2">
            Alarm belum diaktifkan
        </span>
    </div>

    <audio id="alarmAudio" preload="auto">
        <source src="../peserta/alarm.mp3" type="audio/mpeg">
    </audio>

    <hr>

    <a href="daftar_hadir_tes_bersama.php" class="btn btn-primary">
        Isi berita acara
    </a>

</div>


<script>
function loadReset() {
    const tanggal = "<?= htmlspecialchars($tanggal, ENT_QUOTES) ?>";
    const pukul = "<?= htmlspecialchars($pukul, ENT_QUOTES) ?>";

    fetch('reset_list.php?tanggal=' + tanggal + '&jam=' + pukul)
        .then(res => res.text())
        .then(html => {
            document.getElementById('reset-container').innerHTML = html;
        })
        .catch(err => {
            console.error('Gagal memuat reset:', err);
        });
}

setInterval(loadReset, 30000);
loadReset();
</script>


<script>
let alarmAktif = false;
let pesertaSudahAlarm = new Set();

const alarmAudio = document.getElementById('alarmAudio');
const btnAlarm = document.getElementById('btnAlarm');
const statusAlarm = document.getElementById('statusAlarm');


// Aktifkan alarm setelah pengawas menekan tombol
btnAlarm.addEventListener('click', function() {

    alarmAudio.play()
        .then(function() {

            alarmAudio.pause();
            alarmAudio.currentTime = 0;

            alarmAktif = true;

            btnAlarm.innerHTML = '🔔 Alarm Aktif';
            btnAlarm.classList.remove('btn-danger');
            btnAlarm.classList.add('btn-success');

            statusAlarm.innerHTML =
                'Alarm aktif - memantau peserta > 10 menit';

            statusAlarm.classList.remove('text-muted');
            statusAlarm.classList.add('text-success');

            // langsung cek kondisi peserta
            cekAlarmPeserta();

        })
        .catch(function(error) {
            console.error('Audio tidak dapat diaktifkan:', error);
        });

});


// Cek peserta yang sudah lebih dari 10 menit
function cekAlarmPeserta() {

    if (!alarmAktif) {
        return;
    }

    const peserta = document.querySelectorAll('.alarm-peserta');

    let alarmBaru = false;

    peserta.forEach(function(element) {

        const idSiswa = element.dataset.id;

        if (idSiswa && !pesertaSudahAlarm.has(idSiswa)) {

            pesertaSudahAlarm.add(idSiswa);

            alarmBaru = true;
        }
    });


    // Jika ada peserta baru yang > 10 menit
    if (alarmBaru) {

        alarmAudio.currentTime = 0;

        alarmAudio.play()
            .catch(function(error) {
                console.error('Alarm gagal dimainkan:', error);
            });
    }


    // Hapus dari daftar jika peserta sudah
    // menyimpan jawaban lagi
    pesertaSudahAlarm.forEach(function(idSiswa) {

        const masihAlarm = document.querySelector(
            '.alarm-peserta[data-id="' + idSiswa + '"]'
        );

        if (!masihAlarm) {
            pesertaSudahAlarm.delete(idSiswa);
        }
    });
}
</script>


<script>
function loadPeserta() {
    const tanggal = "<?= htmlspecialchars($tanggal, ENT_QUOTES) ?>";
    const pukul = "<?= htmlspecialchars($pukul, ENT_QUOTES) ?>";

    fetch('pekerjaan_peserta_list.php?tanggal=' + tanggal + '&jam=' + pukul)
        .then(res => res.text())
        .then(html => {

            document.getElementById('pekerjaan-container').innerHTML = html;

            // Setelah hasil AJAX masuk, cek alarm
            cekAlarmPeserta();

        })
        .catch(err => {
            console.error('Gagal memuat peserta:', err);
        });
}

setInterval(loadPeserta, 30000);
loadPeserta();
</script>


<?php if (isset($kamera) && $kamera == 'ON') { ?>

<video id="video" autoplay playsinline style="display:none;"></video>
<canvas id="canvas" style="display:none;"></canvas>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');

const ruang = "<?= htmlspecialchars($ruang, ENT_QUOTES) ?>";
const ubk_stream = "<?= htmlspecialchars($ubk_stream, ENT_QUOTES) ?>";
const token = "<?= htmlspecialchars($token_kamera ?? '', ENT_QUOTES) ?>";

let capturing = false;


// Aktifkan kamera
navigator.mediaDevices.getUserMedia({
    video: {
        width: 640,
        height: 480,
        frameRate: {
            ideal: 10,
            max: 15
        }
    }
})
.then(stream => {

    video.srcObject = stream;

    video.onloadedmetadata = () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
    };

})
.catch(err => {
    alert("Kamera error: " + err.message);
});


// Capture frame
function captureFrame(callback) {

    const ctx = canvas.getContext('2d');

    ctx.drawImage(
        video,
        0,
        0,
        canvas.width,
        canvas.height
    );

    setTimeout(() => {

        ctx.drawImage(
            video,
            0,
            0,
            canvas.width,
            canvas.height
        );

        callback();

    }, 10);
}


// Kirim gambar
function send() {

    if (capturing) {
        return;
    }

    if (video.readyState !== 4) {
        return;
    }

    capturing = true;

    requestAnimationFrame(() => {

        captureFrame(() => {

            canvas.toBlob(blob => {

                if (!blob) {
                    capturing = false;
                    return;
                }

                const reader = new FileReader();

                reader.onloadend = () => {

                    fetch(
                        ubk_stream + '/upload.php?ruang=' + ruang,
                        {
                            method: 'POST',
                            mode: 'cors',
                            body: JSON.stringify({
                                image: reader.result,
                                token: token
                            }),
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        }
                    )
                    .catch(err => {
                        console.error("Upload error:", err);
                    })
                    .finally(() => {
                        capturing = false;
                    });

                };

                reader.readAsDataURL(blob);

            }, 'image/jpeg', 0.6);

        });

    });
}


setInterval(send, 5000);
</script>

<?php } ?>


<script>
function updateJam() {

    const now = new Date();

    const dd = String(now.getDate()).padStart(2, '0');
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    const yyyy = now.getFullYear();

    const hh = String(now.getHours()).padStart(2, '0');
    const mi = String(now.getMinutes()).padStart(2, '0');
    const ss = String(now.getSeconds()).padStart(2, '0');

    document.getElementById('jam-server').textContent =
        `${dd}-${mm}-${yyyy} ${hh}:${mi}:${ss}`;
}

setInterval(updateJam, 1000);
</script>

</body>
</html>
