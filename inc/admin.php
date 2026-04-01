<?php
if(!isset($_SESSION['role']) || $_SESSION['role']!='pengawas'){
    exit('Akses ditolak, <a href="login.php">Login Lagi</a>');
}
?>
