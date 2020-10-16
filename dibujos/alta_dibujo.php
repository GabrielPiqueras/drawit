<?php

require("../conexion.php");

// Iniciamos la sesión y recogemos el nombre de usuario
session_start();
$sesionID=$_SESSION["id"];

// Con el id ya bastaria
$usuario=$_SESSION["nombre"];

// Recogemos los datos del formulario
$titulo=$_POST["titulo"];
$descripcion=$_POST["descripcion"];
$categoria=$_POST["categoria"];

/* SUBIDA DE LA IMAGEN */

if(isset($_FILES["archivo"]) && $_SESSION["nombre"]){

    // Obtenemos la extension, ruta temporal
    $tipo = $_FILES["archivo"]["type"];
    $tipo = strtolower(explode('/',$tipo)[1]);
    $ruta_provisional = $_FILES["archivo"]["tmp_name"];

    // Convierto los bytes en MB, obtengo dimensiones e indico la carpeta
    $size = $_FILES["archivo"]["size"]/1000000;
    $dimensiones = getimagesize($ruta_provisional);
    $width = $dimensiones[0];
    $height = $dimensiones[1];
    $carpeta = "img/";
  
    if($tipo!="jpeg" && $tipo!="jpg" && $tipo!="png" && $tipo!="gif"){
        echo "Error: El formato de la imagen debe ser JPEG, JPG, PNG o GIF.";
    }else if($size>6){
        echo "Error: El peso máximo permitido es de 6MB.";
    }else if($width<400 || $height<400){
        echo "Error: La imagen debe ser de 500x500 como mínimo.";
    }else{

        $nombreIMG = "drawit_art_".date("d-m-y-h-i-s").".".$tipo;
        $destino = $carpeta.$nombreIMG;
    
        if (!copy($_FILES['archivo']['tmp_name'], $destino)){
            echo "Error: No se pudo subir el archivo";
        }else{
    
            // Hacemos y ejecutamos la consulta
            $consulta="INSERT $dibujos (IDU,TITULO,IMAGEN,DESCRIPCION,CATEGORIA,LIKES,COMENTARIOS,FECHA) VALUES('$sesionID','$titulo','$nombreIMG','$descripcion','$categoria','0','0',NOW())";
            $ejecutar= mysqli_query($conexion,$consulta);
    
            // Si no hay error muestro un mensaje de éxito, en caso contrario el error
            $error= mysqli_errno($conexion);
            
            if($error==0){
                echo "¡Tu dibujo fue subido con éxito!";
                echo "<div class='row'><img class='col-md-12 mt-3' src='/drawit/dibujos/$destino'></div>";
            }else if($error==1062){
                echo "Error: No se pudo subir el archivo, vuelve a intentarlo.";
            }else{
                echo "Error: No se pudo subir el archivo, vuelve a intentarlo.";
                $numerror=mysqli_errno($conexion); 
                $descrerror=mysqli_error($conexion); 
                echo "Se ha producido un error nº $numerror que corresponde a: $descrerror"; 
            }        
        }
    }

}

// Cerramos la conexión
mysqli_close($conexion);

?>