<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';
// Fungsi untuk menyalin rekursif
function copyRecursive($source, $dest) {
    if (is_dir($source)) {
        @mkdir($dest, 0755, true);
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                copyRecursive(
                    $source . DIRECTORY_SEPARATOR . $file,
                    $dest . DIRECTORY_SEPARATOR . $file
                );
            }
        }
    } elseif (file_exists($source)) {
        copy($source, $dest);
    }
}

// Fungsi untuk menghapus folder rekursif
function hapusFolder($folderPath) {
    if (!is_dir($folderPath)) return;
    $items = scandir($folderPath);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $folderPath . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            hapusFolder($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($folderPath);
}
$versi_saat_ini = $versi_aplikasi;

// Ambil versi terbaru dari GitHub Release
$url = 'https://api.github.com/repos/selamethanafi/cbt-mandusa/releases/latest';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'CBT-Update-Agent');
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
echo 'Tidak dapat terhubung ke GitHub';
die();
}

$data = json_decode($response, true);

$versi_tag = $data['tag_name'] ?? '';
$versi_baru = ltrim($versi_tag, 'v');
$changelog = $data['body'] ?? '';
$url = $data['url'] ?? '';
$download_url = "https://github.com/selamethanafi/cbt-mandusa/archive/refs/tags/{$versi_tag}.zip";
echo 'versi saat ini '.$versi_saat_ini.'<br />';
echo 'versi github '.$versi_baru.'<br />';
if (version_compare($versi_baru, $versi_saat_ini, '>')) 
{
    echo 'Proses update';
	$download_url = "https://github.com/selamethanafi/cbt-mandusa/archive/refs/tags/{$versi_tag}.zip";
	$tmp_zip = __DIR__ . '/update.zip';
	$folder_extract = __DIR__ . '/update_temp/';
	$root_path = realpath(__DIR__ . '/../'); // Path root aplikasi
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $download_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'CBT-Update-Agent');
	$zipData = curl_exec($ch);
	if (curl_errno($ch)) 
	{
		die('Download gagal: ' . curl_error($ch));
	}
	curl_close($ch);
	file_put_contents($tmp_zip, $zipData);
	echo "<br />Download berhasil: " . $tmp_zip;

	// Ekstrak file ZIP
	$zip = new ZipArchive();
	if ($zip->open($tmp_zip) === TRUE) 
	{
		if (!is_dir($folder_extract)) {
		mkdir($folder_extract, 0755, true);
		}
		$zip->extractTo($folder_extract);
		$zip->close();
		unlink($tmp_zip);
		$folders = array_diff(scandir($folder_extract), ['.', '..']);
		$source_folder = null;
		foreach ($folders as $folder) 
		{
			if (is_dir($folder_extract . $folder)) {
				$source_folder = $folder_extract . $folder;
				break;
			}
		}
		if ($source_folder) {
			copyRecursive($source_folder, $root_path);
			hapusFolder($folder_extract);
			$stmt = $db->prepare("UPDATE `cbt_konfigurasi` set `konfigurasi_isi` = ? where `konfigurasi_kode` = 'versi'");
			$stmt->bind_param("s", $versi_baru);
			$stmt->execute();
			$stmt->close();
			// 📝 Log setiap update (disimpan di file update_log.txt)
			file_put_contents(
			__DIR__ . '/update_log.txt',
			"[" . date('Y-m-d H:i:s') . "] Update berhasil → versi baru: $versi_baru_safe\n",
			FILE_APPEND
			);
			echo '<br />success';
		} else {
			echo '<br />Struktur folder update tidak valid';
		}
	} else {
		@unlink($tmp_zip);
		echo '<br />Gagal ekstrak file ZIP';
	}
} else 
{
echo 'Versi sudah termutahir';
}

					?>
					<script>
					// Auto redirect setelah 2 detik
					setTimeout(function(){
					window.location.href = 'menu.php';
					}, 1000);
					</script>
					<?php	
					exit;
