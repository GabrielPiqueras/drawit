<?php

require("../conexion.php");

// Iniciamos la sesion
session_start();

$error;

// Recogemos los datos del Login
// Protección anti-InyecciónSQL
$usuario=stripslashes($_POST['usuario']);
$usuario=mysqli_real_escape_string($conexion,$usuario);
	
$password=stripslashes($_POST['pass']);
$password=mysqli_real_escape_string($conexion,$password);
		
// Hago la consulta para saber si los datos del usuario son correctos
$consulta = "SELECT * FROM $administradores WHERE NOMBRE = '$usuario'";				
$resultado = mysqli_query($conexion,$consulta);
        
// Calculo el nº de registros devueltos
$nregistros=0;
$nregistros=mysqli_num_rows($resultado);
		
// Si se encuentra el usuario
if($nregistros==1){

	// Recupero la contraseña encriptada
	$result = mysqli_fetch_array($resultado);
	$contrasenia_encriptada=$result["PASS"];
    
	// Compruebo si es válida
	if(password_verify($password, $contrasenia_encriptada)){
                // Si es asi, creamos la sesión
                $_SESSION['ida'] = $result["IDA"];
                $_SESSION['admin'] = $result["NOMBRE"];

                // No hay errores, redirigimos al panel de administracion
                $error=0;
	}else{
                $error=1;
        }
}else{
    // Si no se encuentra el usuario
    $error=1;
}

echo $error;

?>