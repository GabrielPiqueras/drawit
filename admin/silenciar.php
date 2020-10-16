<?php

// Iniciamos la sesion
session_start();

require("../conexion.php");

// Obtengo el ID del usuario y el tiempo
$userId=$_POST["userId"];
$nHoras=$_POST["tiempo"];

if(isset($_SESSION["admin"])){

    // Cambiamos el estado del usuario de 0 a -1 para indicar que está silenciado
    $silenciar = "UPDATE $usuarios SET ESTADO=-1 WHERE IDU='$userId'";				
    mysqli_query($conexion,$silenciar);

    // Compruebo si hay errores, si todo está bien procedo a silenciar
    $error= mysqli_errno($conexion);
            
    if($error==0){
        echo "0";

        // En las horas indicadas, el estado del usuario volverá a 0, mientras tanto no podrá iniciar sesión
        $eventoTemporal="CREATE EVENT SILENCIAR_$userId
                         ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL $nHoras HOUR
                         DO
                         BEGIN
                            UPDATE $usuarios SET ESTADO=0 WHERE IDU='$userId';
                         END;";
        mysqli_query($conexion,$eventoTemporal);
    }
}

// Cerramos la conexion
mysqli_close($conexion);

?>