<?php
if(!isset($_SESSION['tanda']) || $_SESSION['tanda']!='PA'){
    exit('Akses ditolak, <a href="login.php">Login Lagi</a>');
}

?>
