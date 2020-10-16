<?php

require("../conexion.php");

session_start();

// Recogemos el id del post, el id del usuario y el comentario
$userID=$_POST["userID"];
$perfilID=$_POST["perfilID"];
$texto=$_POST["texto"];

if($_SESSION["nombre"]){

    // Insertamos el comentario en la BDD
    $insertar="INSERT $comentarios_muro (IDU,PERFILID,TEXTO) VALUES('$userID','$perfilID','$texto')";
    $ejecutar=mysqli_query($conexion,$insertar);

    $error=mysqli_errno($conexion);

    if($error==0){
        // Mandamos notificacion al usuario
        $notificacion="INSERT $notificaciones (TIPO,EMISOR,RECEPTOR,IDLUGAR,LEIDA,FECHA) VALUES('M','$userID','$perfilID','0','N',NOW());";
        mysqli_query($conexion,$notificacion);
        
        echo "0";
    }
}

mysqli_close($conexion);

?>