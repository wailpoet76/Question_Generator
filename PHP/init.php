<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2025-03-18
// Description: Profile Page JS


//Routes
$MainDir=dirname($_SERVER['PHP_SELF']);

global $langset;$sender;$reciever;
    $tpl='includes/templates/';//tempalte directory
    $func='includes/functions/';//Functions Directory
    $langsDir='includes/langauges/';//langauges files directory
    $css='layout/css/';//Css directory
    $js='layout/js/';//Js directory
    $imgDir='layout/images/';//Images  directory
    //includes for important files
    include $func."functions.php";
    include  $tpl . 'header.php';
    if ($langset=="Eng"){
        include $langsDir . "english.php";
    }else{
        include $langsDir . "arabic.php";
    }
    include "DBconnect.php";
    include "includes/config.php";

    if(!isset($nonavbar)){include $tpl . "navbar.php";}

?>