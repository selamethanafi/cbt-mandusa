<?php
echo __DIR__;
$ruang = $_GET['ruang'] ?? 'default';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['image'])) {

    $img = $data['image'];
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = base64_decode($img);

    // 🕒 timestamp
    $time = date("Ymd_His");

     // 📁 BASE folder foto (BUKAN log)
    $base = __DIR__ . "/foto";

    // buat folder foto kalau belum ada
    if (!is_dir($base)) {
        mkdir($base, 0777, true);
    }

    // folder per ruang
    $folder = $base . "/" . $ruang;

    if (!is_dir($folder)) {
        if (!mkdir($folder, 0777, true)) {
            die("Gagal membuat folder: " . $folder);
        }
    }

    // ✅ 1. simpan file terbaru (untuk viewer)
    file_put_contents(__DIR__ . "/latest_$ruang.jpg", $img);

    // ✅ 2. simpan file histori (timestamp)
    file_put_contents($folder . "cam_{$ruang}_{$time}.jpg", $img);
}
?>
