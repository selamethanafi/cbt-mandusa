<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
$tanggal = $_GET['tanggal'] ?? '';
$pukul = $_GET['jam'] ?? '';
$waktu = $tanggal.' '.$pukul;
if (isValidDateTime($waktu)) {

} else {
    echo "Format waktu salah";
    ?>
					<script>
					// Auto redirect setelah 2 detik
					setTimeout(function(){
					window.location.href = 'soal_hari_ini.php';
					}, 2000);
					</script>
					<?php	
					exit;
}


$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='kode_qr'");
$da = mysqli_fetch_assoc($ta);
$kode_qr = $da['konfigurasi_isi'] ?? '0';
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='kamera'");
$da = mysqli_fetch_assoc($ta);
$kamera = strtoupper($da['konfigurasi_isi']) ?? 'OFF';
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='token_kamera'");
$da = mysqli_fetch_assoc($ta);
$token_kamera = $da['konfigurasi_isi'] ?? 'sdfghfhgjhkhgkhgwdasda';
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='ubk_pusat'");
$da = mysqli_fetch_assoc($ta);
$ubk_pusat = $da['konfigurasi_isi'] ?? '';
$ta = mysqli_query($db,"SELECT * FROM `cbt_konfigurasi` WHERE `konfigurasi_kode`='ubk_stream'");
$da = mysqli_fetch_assoc($ta);
$ubk_stream = $da['konfigurasi_isi'] ?? '';

?>
<!DOCTYPE html>
<html>
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
            Ruang: <strong><?= htmlspecialchars($ruang) ?></strong>
        </td>
        <td width="33%" align="right">
            Jam Peladen: <span id="jam-server"><?= date("d-m-Y H:i:s"); ?></span>
        </td>
    </tr>
</table>
<hr>

<div id="reset-container">
    <!-- nanti diisi via AJAX -->
</div>
<div id="pekerjaan-container">
    <!-- nanti diisi via AJAX -->
</div>

</div>
<script>
function loadReset(){
const tanggal = "<?= htmlspecialchars($tanggal, ENT_QUOTES) ?>";
const pukul= "<?= htmlspecialchars($pukul, ENT_QUOTES) ?>";
    fetch('reset_list.php?tanggal=' + tanggal + '&jam=' + pukul)
    .then(res => res.text())
    .then(html => {
        document.getElementById('reset-container').innerHTML = html;
    });
}

// refresh tiap 30 detik
setInterval(loadReset, 30000);

// load pertama
loadReset();
</script>
<script>
function loadPeserta(){
const tanggal = "<?= htmlspecialchars($tanggal, ENT_QUOTES) ?>";
const pukul= "<?= htmlspecialchars($pukul, ENT_QUOTES) ?>";
    fetch('pekerjaan_peserta_list.php?tanggal=' + tanggal + '&jam=' + pukul)
    .then(res => res.text())
    .then(html => {
        document.getElementById('pekerjaan-container').innerHTML = html;
    });
}

// refresh tiap 30 detik
setInterval(loadPeserta, 30000);

// load pertama
loadPeserta();
</script>
<?php if(isset($kamera) && $kamera =='ON'){ ?>

<video id="video" autoplay playsinline style="display:none;"></video>
<canvas id="canvas" style="display:none;"></canvas>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');

const ruang = "<?= htmlspecialchars($ruang, ENT_QUOTES) ?>";
const ubk_stream = "<?= htmlspecialchars($ubk_stream, ENT_QUOTES) ?>";
const token = "<?= htmlspecialchars($token_kamera ?? '', ENT_QUOTES) ?>";

let capturing = false;

// aktifkan kamera
navigator.mediaDevices.getUserMedia({ 
    video: { 
        width: 640, 
        height: 480,
        frameRate: { ideal: 10, max: 15 }
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

// 🔥 fungsi capture paling stabil
function captureFrame(callback){
    const ctx = canvas.getContext('2d');

    // trick: double draw untuk hilangkan tearing
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    setTimeout(() => {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        callback();
    }, 10); // delay kecil
}

// kirim gambar
function send(){
    if (capturing) return;
    if (video.readyState !== 4) return;

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

                    fetch(ubk_stream + '/upload.php?ruang=' + ruang, {
                        method: 'POST',
                        mode: 'cors',
                        body: JSON.stringify({ 
                            image: reader.result,
                            token: token
                        }),
                        headers: { 'Content-Type': 'application/json' }
                    })
                    .catch(err => console.error("Upload error:", err))
                    .finally(() => {
                        capturing = false;
                    });

                };

                reader.readAsDataURL(blob);

            }, 'image/jpeg', 0.6);

        });

    });
}

// interval (jangan terlalu cepat)
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

