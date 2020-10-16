<?php

require("../conexion.php");

session_start();

$sesionId=$_SESSION["id"];

if($_SESSION["id"]){
    $marcarComoLeidas="UPDATE $notificaciones SET LEIDA='S' WHERE RECEPTOR='$sesionId'";
    mysqli_query($conexion,$marcarComoLeidas);

    $error= mysqli_errno($conexion);

    if($error==0){
        echo 0;
    }
}

?>