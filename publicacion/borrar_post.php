<?php

require("../conexion.php");

session_start();

// Recogemos el id del post y el nombre del usuario con sesion iniciada
$idPost=$_POST["idp"];
$idUserLogueado=$_POST["idSesion"];

if($_SESSION["id"]==$idUserLogueado){

    // Obtenemos la url de la imagen para borrarla de la carpeta más adelante
    $obtenerUrl="SELECT IMAGEN FROM $dibujos WHERE IDP='$idPost'";
    $resultadoUrl=mysqli_query($conexion,$obtenerUrl);
    $resultadoUrl=mysqli_fetch_array($resultadoUrl);
    $urlImagen=$resultadoUrl['IMAGEN'];

    $borrarPost="DELETE FROM $dibujos WHERE IDP='$idPost'";
    mysqli_query($conexion,$borrarPost);

    $error=mysqli_errno($conexion);

    if($error==0){
        // Borramos todos los comentarios de dicha publicacion
        $borrarComentarios="DELETE FROM $comentarios WHERE IDP='$idPost'";
        mysqli_query($conexion,$borrarComentarios);

        // Borramos todas las notificaciones de dicha publicacion
        $borrarNotificaciones="DELETE FROM $notificaciones WHERE IDLUGAR='$idPost'";
        mysqli_query($conexion,$borrarNotificaciones);

        // Borramos la imagen de la carpeta
        $carpeta = "../dibujos/img/";
        unlink($carpeta.$urlImagen);
        
        echo "0";
    }
}

mysqli_close($conexion);

?>