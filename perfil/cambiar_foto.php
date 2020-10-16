<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

$usuario = $_POST["userfoto"];

// Si el archivo existe:

if(isset($_FILES["archivo"])){

    // Guardamos las propiedades en variables:

    $nombreIMG = $_FILES["archivo"]["name"];
    $tipo = $_FILES["archivo"]["type"];
    $tipo = strtolower(explode('/',$tipo)[1]);
    $ruta_provisional = $_FILES["archivo"]["tmp_name"];
    $size = $_FILES["archivo"]["size"];
    $size = $_FILES["archivo"]["size"]/1000000;

    // Para obtener las dimensiones:
    $dimensiones= getimagesize($ruta_provisional);
    $ancho= $dimensiones[0];
    $alto= $dimensiones[1];

    // Definimos la carpeta
    $carpeta="../img/perfiles/"; 

    if($tipo!='jpg' && $tipo!='jpeg' && $tipo!='png' && $tipo!='gif'){
        echo "Error: El archivo no es una imagen";
    }else if($size > (1024*1024)*3){
        echo "Error: El tamaño máximo permitido es de 3MB";
    }else if($ancho > 1000 || $alto > 1000){
        echo "Error: La anchura y la altura máxima permitida es de 1000px";
    }else if($ancho < 60 || $alto < 60){
        echo "Error: La anchura y la altura mínima permitida es de 60px";
    }else{
        // Si todo está correcto ponemos el nombre y definimos el destino
        $nombreIMG = "drawit_user_".$usuario.".jpg";
        // Descomentar esta linea para no convertir el archivo a jpg
        // $nombreIMG = "drawit_user_".$usuario.".".$tipo;
        $destino=$carpeta.$nombreIMG;
        sleep(1);

        // Copiamos el archivo
        if(!copy($ruta_provisional,$destino)){
            //echo "Error: no se pudo copiar el archivo.";
        }else{
            // Actualizamos la imagen en la BDD y en la sesión actual
            $consulta="UPDATE $usuarios SET FOTO='$nombreIMG' WHERE NOMBRE='$usuario'";
            mysqli_query($conexion,$consulta);
            $_SESSION["foto"]=$nombreIMG;
            
            // Mostramos la imagen cambiada
            echo $nombreIMG;
        }
    }

}

mysqli_close($conexion);

?>