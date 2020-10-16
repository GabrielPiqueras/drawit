<?php

require("../conexion.php");

session_start();

// Id de la sesion
$sesionID=$_SESSION["id"];

// Recogemos el id del post, el id del autor y el comentario
$postID=$_POST["postID"];
$postUserID=$_POST["postUserID"];
$texto=$_POST["texto"];

if($_SESSION["nombre"]){

    // Insertamos el comentario en la BDD
    $insertar="INSERT $comentarios (IDP,IDU,TEXTO) VALUES('$postID','$sesionID','$texto')";
    mysqli_query($conexion,$insertar);

    $error=mysqli_errno($conexion);

    if($error==0){
        // Sumamos +1 los comentarios de la publicacion
        $sumarComentario="UPDATE $dibujos SET COMENTARIOS=COMENTARIOS+1 WHERE IDP='$postID'";
        mysqli_query($conexion,$sumarComentario);

        // Mandamos notificacion al usuario
        $notificacion="INSERT $notificaciones (TIPO,EMISOR,RECEPTOR,IDLUGAR,LEIDA,FECHA) VALUES('P','$sesionID','$postUserID','$postID','N',NOW())";
        mysqli_query($conexion,$notificacion);

        echo "0";
    }
}

mysqli_close($conexion);

?>