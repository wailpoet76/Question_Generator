<?php
$dsn = "mysql:host=sql202.infinityfree.com;dbname=if0_39178435_qg";
$dsn = "mysql:host=sql202.infinityfree.com;port=3306;dbname=if0_39178435_qg";
$username = "if0_39178435";
$pass = "I8P39LLZQOWoU";

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);

try {
    $con = new PDO($dsn, $username, $pass, $options);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!";
} catch (PDOException $err) {
    echo "Failed to connect to server:<br>" .
         "Error code: " . $err->getCode() . "<br>" .
         "Error Msg: " . $err->getMessage() . "<br>" .
         "Error location: " . $err->getFile() . "<br>" .
         "At line: " . $err->getLine();
}
?>