<?php
session_start();

$db = new mysqli("localhost","super","95EfR6wfFf^!","cbt_ringan");
if ($db->connect_error) die("DB Error");

date_default_timezone_set("Asia/Jakarta");
