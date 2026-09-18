<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/guru.php';
$id_ujian = $_GET['id_ujian'] ?? '';
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
	<h4 class="mb-2">Dashboard Guru</h4>
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
const id_ujian = "<?= htmlspecialchars($id_ujian, ENT_QUOTES) ?>";
    fetch('reset_list.php?id_ujian=' + id_ujian)
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
const id_ujian = "<?= htmlspecialchars($id_ujian, ENT_QUOTES) ?>";
    fetch('pekerjaan_peserta_list.php?id_ujian=' + id_ujian)
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

