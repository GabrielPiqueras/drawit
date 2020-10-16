<?php

// Iniciamos la sesion
session_start();

require("../conexion.php");

$id= $_POST["idUsuario"];

if($_SESSION["admin"]){
    $consulta="DELETE FROM $usuarios WHERE IDU='$id'";
    mysqli_query($conexion,$consulta);

    // Borrar notificaciones
    $borrarNotificaciones="DELETE FROM $notificaciones WHERE EMISOR='$id' OR RECEPTOR='$id'";
    mysqli_query($conexion,$borrarNotificaciones);
}


mysqli_close($conexion);

?>