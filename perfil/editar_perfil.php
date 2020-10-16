<?php

require("../conexion.php");

// Iniciamos la sesion
session_start();

// ID del usuario en sesión
$sesionID=$_SESSION["id"];

// Para cambiar nombre
$viejoNombre= $_POST["viejo"];
$nuevoNombre= $_POST["nuevo"];
$viejaFoto= $_POST["foto"];

// Para cambiar la contraseña
$password= $_POST["pass"];

if(!empty($nuevoNombre)){
    // Primero comprobamos si el nuevo nombre existe en la DB
    $consulta="SELECT * FROM $usuarios WHERE NOMBRE='$nuevoNombre'";
    $resultado= mysqli_query($conexion,$consulta);
    $nregistros=mysqli_num_rows($resultado);

    // Si no existe le permitimos cambiarlo
    if($nregistros==0){
        $consulta="UPDATE $usuarios SET NOMBRE='$nuevoNombre' WHERE NOMBRE='$viejoNombre'";
        $resultado=mysqli_query($conexion,$consulta); 

        $error= mysqli_errno($conexion);

        if($error==0){
            $_SESSION["nombre"]=$nuevoNombre;
            
            // Conseguimos el nombre del archivo en la url
            $viejaFoto= explode("/",$viejaFoto);
            $viejaFoto=end($viejaFoto);
            
            // Si el usuario no tiene la foto por defecto
            if($viejaFoto!="default.jpg"){
                $rutaPerfiles="../img/perfiles/";
    
                // Y reemplazamos el nuevo nombre de archivo
                $nuevaFoto=str_replace($viejoNombre,$nuevoNombre,$viejaFoto);            
                rename($rutaPerfiles.$viejaFoto,$rutaPerfiles.$nuevaFoto);
    
                // Actualizamos la imagen en la base de datos y sesion actual
                $actualizarFoto="UPDATE $usuarios SET FOTO='$nuevaFoto' WHERE FOTO='$viejaFoto'";
                $ejecutar=mysqli_query($conexion,$actualizarFoto); 

                $_SESSION["foto"]=$nuevaFoto;
            }
        }
    }else{
        echo 1;
    }
}

if(!empty($password)){
    $password= password_hash($password,PASSWORD_BCRYPT);
    $cambiarPass="UPDATE $usuarios SET PASS='$password' WHERE IDU='$sesionID'"; 
    mysqli_query($conexion,$cambiarPass);

    $error= mysqli_errno($conexion);

    if($error==0){
        echo "0";
    }
}

// Cerramos la conexión
mysqli_close($conexion);

?>