<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "gestion_modules";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

function redirect($url) {
    header("Location: $url");
    exit();
}
?>