<?php
session_start();

/* ===============================
   LOGOUT AMAN CBT
================================ */

// hapus semua session
$_SESSION = [];
session_unset();
session_destroy();

// hapus cookie session (opsional tapi disarankan)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// arahkan ke halaman login
header("Location: index.php");
exit;
