<?php
if (
    !isset($_SESSION['role']) ||
    !in_array($_SESSION['role'], ['panitia'])
) {
	    header("Location: pengawas.php");
    exit;	    
}
?>
