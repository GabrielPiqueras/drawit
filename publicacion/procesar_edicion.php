<?php

require("../conexion.php");

// Iniciamos la sesión y recogemos el nombre de usuario
session_start();
$usuario=$_SESSION["nombre"];

// Recogemos los datos del formulario
$idPost=$_POST["id"];
$titulo=$_POST["titulo"];
$descripcion=$_POST["descripcion"];
$categoria=$_POST["categoria"];

// Comprobamos si existe el registro a editar en la base de datos
$consulta="SELECT * FROM $dibujos WHERE IDP='$idPost'";
$resultado=mysqli_query($conexion,$consulta);
$registro=mysqli_fetch_array($resultado);
$nregistros=mysqli_num_rows($resultado);

if($nregistros=1){
    // Para los errores de la imagen
    $errorImagen=0;

    // Existe la publicacion a editar, realizamos el proceso

    /* Si se ha cambiado la imagen, la subimos */
    if(isset($_FILES["archivo"])){
    
        // Obtenemos la extension, ruta temporal
        $tipo = $_FILES["archivo"]["type"];
        $tipo = strtolower(explode('/',$tipo)[1]);
        $ruta_provisional = $_FILES["archivo"]["tmp_name"];
    
        // Convierto los bytes en MB, obtengo dimensiones e indico la carpeta
        $size = $_FILES["archivo"]["size"]/1000000;
        $dimensiones = getimagesize($ruta_provisional);
        $width = $dimensiones[0];
        $height = $dimensiones[1];
        $carpeta = "../dibujos/img/";
      
        if($tipo!="jpeg" && $tipo!="jpg" && $tipo!="png" && $tipo!="gif"){
            $errorImagen=1;
            echo "Error: El formato de la imagen debe ser JPEG, JPG, PNG o GIF.<br>";
        }else if($size>6){
            $errorImagen=1;
            echo "Error: El peso máximo permitido es de 6MB.<br>";
        }else if($width<400 || $height<400){
            $errorImagen=1;
            echo "Error: La imagen debe ser de 500x500 como mínimo.<br>";
        }else{
            // Guardamos en la variable el nombre que tiene la imagen subida (LA VIEJA)
            $imagenVieja=$registro["IMAGEN"];

            // La borramos de la carpeta (para no tener problemas con la extension)
            unlink($carpeta.$imagenVieja);

            // Creamos la ruta de la  imagen nueva con el nombre de la vieja + la nueva extension
            $imagenNueva=explode(".",$imagenVieja)[0];
            $imagenNueva = $imagenNueva.".".$tipo;
            $destino = $carpeta.$imagenNueva;
        
            if (!copy($_FILES['archivo']['tmp_name'], $destino)){
                $errorImagen=1;
            }else{
                // Actualizamos la imagen en la base de datos
                $actualizarImagen="UPDATE $dibujos SET IMAGEN='$imagenNueva' WHERE IDP='$idPost'";
                mysqli_query($conexion,$actualizarImagen);
                
                // Si hubo error actualizando la imagen
                $errorImagen= mysqli_errno($conexion);
            }
        }
    }

    // SE HAYA SUBIDO NUEVA UNA NUEVA IMAGEN O NO, actualizamos el resto de campos
    $editar="UPDATE $dibujos SET TITULO='$titulo',DESCRIPCION='$descripcion',CATEGORIA='$categoria' WHERE IDP='$idPost'";
    mysqli_query($conexion,$editar);

    // Si no hay error muestro un mensaje de éxito, en caso contrario el error
    $errorCampos= mysqli_errno($conexion);
    
    if($errorCampos==0 && $errorImagen==0){
        echo "¡Tu publicación fue actualizada con éxito!<br>Refrescando página...";
    }else if($errorCampos==0 && $errorImagen==1){
        echo "Error: La imagen no se actualizó, pero si el resto de campos.";
    }else{
        echo "Error: No se pudo actualizar la publicación";
        $numerror=mysqli_errno($conexion); 
        $descrerror=mysqli_error($conexion); 
        echo "Se ha producido un error nº $numerror que corresponde a: $descrerror"; 
    }  

}

// Cerramos la conexión
mysqli_close($conexion);

?>