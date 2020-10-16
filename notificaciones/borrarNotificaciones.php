<?php

require("../conexion.php");

session_start();

$sesionId=$_SESSION["id"];

if($_SESSION["id"]){
    $borrarNotificaciones="DELETE FROM $notificaciones WHERE RECEPTOR='$sesionId'";
    mysqli_query($conexion,$borrarNotificaciones);

    $error= mysqli_errno($conexion);

    if($error==0){
        echo 0;
    }
}

?>