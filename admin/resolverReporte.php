<?php

// Iniciamos la sesion
session_start();

require("../conexion.php");

$idPost= $_POST["IDP"];
$idReporte= $_POST["IDR"];
$accion= $_POST["accion"];
$urlImagen= $_POST["urlImagen"];

// Si hay sesion de Admin iniciada
if($_SESSION["admin"]){

    if($accion==1){
        // Si la acción es borrar, borramos el post 
        $borrarPost="DELETE FROM $dibujos WHERE IDP='$idPost'";
        $ejecutar=mysqli_query($conexion,$borrarPost);

        // Borramos la imagen de la carpeta para no ocupar espacio
        unlink($urlImagen);

        // Y como ya no existirá, borro el resto de reportes del mismo
        $borrarReportes="DELETE FROM $reportes WHERE IDP='$idPost'";
        $ejecutar= mysqli_query($conexion,$borrarReportes);
    }else{
        // Solo borro el reporte
        $borrarReporte="DELETE FROM $reportes WHERE IDR='$idReporte' AND IDP='$idPost'";
        $ejecutar= mysqli_query($conexion,$borrarReporte);
    }
}

mysqli_close($conexion);

?>