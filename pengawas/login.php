<?php
include '../config.php';

if($_POST){
  $nis = $_POST['nis'];
  $pass = $_POST['password'];

  $q = $db->prepare("SELECT * FROM pengawas WHERE username=?");
  $q->bind_param("s",$nis);
  $q->execute();
  $r = $q->get_result()->fetch_assoc();

  if($r && password_verify($pass,$r['password'])){
    $_SESSION['username'] = $r['id'];
    $_SESSION['role'] = 'pengawas';
    header("Location: monitor.php");
    exit;
  }
  $error="Login gagal";
}
?>
<form method="post">
<input name="nis" placeholder="Username"><br>
<input name="password" type="password"><br>
<button>LOGIN</button>
<?= $error ?? '' ?>
</form>
