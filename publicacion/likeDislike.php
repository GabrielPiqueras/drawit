<?php

require("../conexion.php");

// Iniciamos la sesión y recogemos el nombre del usuario y el ID del dibujo
session_start();

// Si el usuario ha iniciado sesion
if($_SESSION["nombre"]){

    // Obtenemos el nombre, el id de la imagen y la acción
    $userId=$_SESSION["id"];
    $postId=$_POST["id"];
    $accion=$_POST["accion"];
    
    if($accion==1){
        // Sumar 1 al total de likes de ese dibujo y añado el registro en la tabla de likes
        $consulta1="UPDATE $dibujos SET LIKES=LIKES+1 WHERE IDP='$postId'";
        $consulta2="INSERT $likes (IDP,IDU,FECHA) VALUES('$postId','$userId',NOW())";
    }else{
        // Restar 1 al total de likes de ese dibujo y borro el registro de la tabla de likes
        $consulta1="UPDATE $dibujos SET LIKES=LIKES-1 WHERE IDP='$postId'";
        $consulta2="DELETE FROM $likes WHERE IDP='$postId' AND IDU='$userId'";
    }

    // Procesamos ambas consultas
    mysqli_query($conexion,$consulta1);
    mysqli_query($conexion,$consulta2);
}else{
    // Si no ha iniciado sesion mostramos un error
    echo "1";
}

// Cerramos la conexión
mysqli_close($conexion);

?>