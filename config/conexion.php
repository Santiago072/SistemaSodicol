<?php
function conexion(){
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "sistema_sodicol";

    $conexion = mysqli_connect($host, $user, $pass, $db);
    return $conexion;
}
?>