<?php
global $con;
session_start();
session_unset();
session_abort();
session_destroy();
$con=null;
header('Location:../index.html');
exit();
?>