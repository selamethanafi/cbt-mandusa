<?php
session_start();

// Cek login
if(!isset($_SESSION['id_siswa'])){
    // Belum login → redirect ke halaman login
    header("Location: login.php");
    exit;
}

// Sudah login → redirect ke daftar ujian
header("Location: daftar_ujian.php");
exit;
