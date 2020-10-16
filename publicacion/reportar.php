<?php

// Iniciamos la sesion
session_start();

require("../conexion.php");

// Obtengo los datos del reporte
$sesionID=$_SESSION["id"];

$tipo=$_POST["tipo"];
$motivo=$_POST["razon"];
$idPost=$_POST["idp"];
$comentarios=$_POST["comentarios"];
			
// Hacemos la consulta
$consulta = "INSERT $reportes (TIPO,MOTIVO,IDP,IDU,COMENTARIOS,FECHA) VALUES('$tipo','$motivo','$idPost','$sesionID','$comentarios',NOW())";				
mysqli_query($conexion,$consulta);

// Compruebo si hay errores
$error= mysqli_errno($conexion);
            
if($error==0){
    echo "0";
}

// Cerramos la conexion
mysqli_close($conexion);

?>