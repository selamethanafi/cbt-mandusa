<?php
if(!isset($_SESSION['role']) || $_SESSION['role']!='pengawas'){
    exit('Akses ditolak');
}
?>
