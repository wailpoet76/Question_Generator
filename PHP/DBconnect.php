<?php
$host="mysql:host=localhost;dbname=qg";//or sever DB file name
$username='root';//or website username
$pass='';//local nothing - but in website put website password
$except=array(
    PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8',
    );
try{
    $con=new PDO($host,$username,$pass,$except);
    $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch (PDOException $err){
    echo "Failed to connect to server:<br>"."Error code: ".$err->getCode()
    ."<br> Error Msg: ".$err->getMessage()
    ."<br> Error location: ".$err->getFile()."<br>, At line: ".$err->getMessage();
}

?>