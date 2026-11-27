<?php
/*
periksa aksara
*/
include '../inc/config.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Pemeriksaan Aksara</title>
<link rel="stylesheet" href="../css/ujian.css?v=<?= time() ?>">
<style>
@font-face {
    font-family: 'Noto Sans Javanese';
    src: url('../fonts/NotoSansJavanese-Regular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}

.soal,
.opsi-teks {
    font-family: 'Noto Sans Javanese', Arial, sans-serif;
    line-height: 1.9;
}
</style>
</head>
<body>

<div class="container-fluid">

<div class="text-center mb-3">
    <h4>CBT <?= $sek_nama;?> Ruang <?= $ruang;?></h4>
    <small><?= date('Y') ?></small>
</div>

<div class="card shadow-sm">
<p>Bahasa Jawa</p>
<p>ꦱꦸꦏꦺꦴ ꦭꦤ꧀ ꦧꦸꦢꦶ ꦩꦭꦶꦃ ꦩꦼꦚꦁ ꦩꦼꦤ꧀ꦝꦺꦠ꧀ꦠꦺꦴꦏ꧀ ꦥꦏꦸꦧꦸꦮꦤ꧀</p>
<p>Adhedhasar paugeran panganggone aksara Murda, kepriye panulisan jeneng Suko, Budi, lan Pakubuwana kang trep?</p>
<ol type="a"><li>ꦱꦸꦏꦺꦴ, ꦧꦸꦢꦶ, ꦥꦏꦸꦧꦸꦮꦤ꧀</li><li>ꦯꦸꦏꦺꦴ, ꦨꦸꦢꦶ, ꦦꦏꦸꦧꦸꦮꦤ꧀</li><li>ꦯꦸꦏꦺꦴ, ꦧꦸꦢꦶ, ꦥꦏꦸꦧꦸꦮꦤ꧀</li><li>ꦱꦸꦏꦺꦴ, ꦨꦸꦢꦶ, ꦦꦏꦸꦧꦸꦮꦤ꧀</li><li>ꦯꦸꦏꦺꦴ, ꦨꦸꦢꦶ, ꦥꦏꦸꦧꦸꦮꦤ꧀</li></ol>
<hr>

<h5>2. Tes Tampilan Emoji</h5>
<p>Pastikan semua emoji berikut terlihat dengan jelas dan tidak berubah menjadi kotak kosong.</p>

<div class="tes-emoji">
    😀 😁 😂 🤣 😍 🥰 😎 🤔
    👍 👎 👏 🙏 💪 ❤️ 💚 💙
    🎉 🎊 🎯 🚀 🔥 ⭐ 🌈
    🏫 📚 💻 🖥️ 📱 🖨️
    🤖 ⚙️ 🔌 🔋 💡
    🐱 🐶 🐼 🦁 🐸 🦋
</div>

<hr>

<h5>3. Tes Tampilan Kode Arduino</h5>
<p>Pastikan kode di bawah tampil rapi, simbol tidak berubah, dan tulisan dapat dibaca.</p>

<pre class="tes-kode"><span class="komentar">// Program LED Berkedip</span>
<span class="perintah">const int</span> ledPin = 13;

<span class="perintah">void</span> setup() {
    pinMode(ledPin, OUTPUT);
    Serial.begin(<span class="angka">9600</span>);
}

<span class="perintah">void</span> loop() {
    digitalWrite(ledPin, HIGH);
    delay(<span class="angka">1000</span>);

    digitalWrite(ledPin, LOW);
    delay(<span class="angka">1000</span>);

    <span class="komentar">// Menampilkan pesan ke Serial Monitor</span>
    Serial.println("LED Berkedip!");

    <span class="komentar">/* Tes simbol pemrograman:
       { } [ ] ( ) ; , = == != && ||
       + - * / % < > <= >= ! &
    */</span>
}</pre>

<hr>

<h5>4. Tes Karakter Khusus</h5>
<p>Pastikan karakter berikut tampil dengan benar:</p>

<div style="font-size:20px; line-height:2;">
    <code>&lt; &gt; &amp; &quot; ' / \ | { } [ ] ( )</code>
    <br>
    <code>== != &lt;= &gt;= ++ -- += -= &amp;&amp; ||</code>
    <br>
    <code>°C ± × ÷ π √ ∑ → ← ↑ ↓</code>
</div>
<p>
    Setelah memeriksa seluruh tampilan di atas, bila tidak tampil / tampilan rusak silakan laporkan
    kondisi HP Anda.
</p>
<a href="daftar_ujian.php" class="btn btn-primary mb-2">Kembali ke Daftar Asesmen</a>
</div>
</body>
</html>


