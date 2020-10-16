<?php

// Iniciamos la sesion
session_start();

require("conexion.php");

// Variable para controlar los errores
// -1 = Usuario silenciado temporalmente
// 0 = Usuario + pass válidos
// 1 = Usuario válido pero contraseña no
// 2 = Usuario no existe
// 3 = Usuario pendiente de activación

$error;

// Recogemos los datos del Login
// Protección anti-InyecciónSQL
$usuario=stripslashes($_POST['usuario']);
$usuario=mysqli_real_escape_string($conexion,$usuario);
	
$password=stripslashes($_POST['pass']);
$password=mysqli_real_escape_string($conexion,$password);
		
// Hago la consulta para saber si los datos del usuario son correctos
// El campo por el que busque tiene que ser clave (único)
$consulta = "SELECT * FROM $usuarios WHERE NOMBRE = '$usuario'";				
$resultado = mysqli_query($conexion,$consulta);
        
// Calculo el nº de registros devueltos
$nregistros=mysqli_num_rows($resultado);
		
// Si se encuentra el usuario
if($nregistros==1){
        $result = mysqli_fetch_array($resultado);
        $estado = $result["ESTADO"];

        // Recupero la contraseña encriptada y compruebo si es válida
        $contrasenia_encriptada=$result["PASS"];

        if (password_verify($password, $contrasenia_encriptada)){
           
           // Si el usuario está silenciado, mostraré un error en el login
           if($estado==-1){
              $error=-1;
           }else{
              // Si todo está bien, creamos la sesión
              $_SESSION['id'] = $result["IDU"];
              $_SESSION['nombre'] = $result["NOMBRE"];
              $_SESSION['foto'] = $result["FOTO"];
                            
              // Si se ha marcado la casilla "Recuerdame" alargamos la Cookie
              if(($_POST["recordar"])=="si"){
                      setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+999999);
              }             
              // Usuario y contraseña correctos
              $error=0;
           }
        }else{
           // El usuario es válido pero la contraseña no incorrecta
           $error=1;			
        }  
}else{
        
        // Si no existe comprobamos si está pendiente de activación
        $consulta="SELECT * FROM $upendientes WHERE NOMBRE='$usuario'";
        $resultado= mysqli_query($conexion,$consulta);
        $nregistros=mysqli_num_rows($resultado);
        
        if($nregistros==0){
                // El usuario no existe o caducó su link de activación
                $error=2;
        }else{ 
                // El usuario está pendiente de activación
                $error=3; 
        }
}

echo $error;

?>