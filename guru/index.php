<?php
session_start();

// Cek login
if(!isset($_SESSION['username'])){
    // Belum login → redirect ke halaman login
    header("Location: login.php");
    exit;
}

// Sudah login → redirect ke daftar ujian
header("Location: menu.php");
exit;
