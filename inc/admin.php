<?php
if (
    !isset($_SESSION['role']) ||
    !in_array($_SESSION['role'], ['pengawas', 'panitia'])
) {
    exit('Akses ditolak, <a href="login.php">Login Lagi</a>');
}
?>
