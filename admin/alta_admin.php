<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

require('../librerias/phpmailer/PHPMailer.php');
require('../librerias/phpmailer/SMTP.php');
require('../librerias/phpmailer/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Recogemos los datos del formulario
$nombre=$_POST['nombre'];
$correo=$_POST['correo'];
$password=password_hash($_POST['pass'],PASSWORD_BCRYPT);

if(isset($_SESSION["admin"])){

    // Hacemos y ejecutamos la consulta
    $consulta="INSERT $administradores (NOMBRE,CORREO,PASS) VALUES('$nombre','$correo','$password')";
    $ejecutar= mysqli_query($conexion,$consulta);
    
    // Si no hay error muestro un mensaje de confirmacion, en caso contrario el error
    $error= mysqli_errno($conexion);
    
    if($error==0){
        $linkPanel="http://89.29.146.227/drawit/administracion";
        //$linkPanel="http://localhost/drawit/administracion";
        
        
        /************* ENVIAR ENLACE DE ACTIVACION POR CORREO  *************/
            
            // creamos objeto de correo
            $mail = new PHPMailer();
    
            // Acentos
            $mail->CharSet = 'UTF-8';
    
            // para ver los mensajes y diálogo entre servidor local y servidor de correo
            // se pueden poner varios valores según información deseda 
            
            // Linea para depurar 
            // $mail->SMTPDebug =1;
    
            //********************** FORMATO ************************
            // Le dice a PHPMailer que vamos a usar TEXTO para enviar el correo
            // $mail->IsSMTP();
            // Número máximo de caracteres que tendrá cada linea
            $mail->WordWrap = 50; 
            // Le dice a PHPMailer que vamos a usar HTML para enviar el correo
            $mail->IsHTML();
            
            //******************* DATOS CUENTA ************************
            // identificación de quien envía el correo
            // hay servidores que te obligan a identificarte para poder enviar correos 
            // HOTMAIL y GMAIL te obligan a ello->SMTPAuth = true; 
            
            // habilitamos la AUTENTICACIÓN SMTP
            $mail->SMTPAuth = true;
            // datos de configuración de quien envía el correo
            $mail->Username = "drawit.oficial@gmail.com"; 
            $mail->Password = "adventour77";
            
            // para que el receptor sepa quien envía el correo
            $mail->From = "drawit.oficial@gmail.com";
            $mail->FromName = "DrawIt Oficial";
            
            //******************* CONFIGURACIÓN **************************
            // Establece el método para enviar el mensaje puede ser MAIL, SENDMAIL o SMTP 
            // Le indicamos que vamos a usar un servidor SMTP  
            $mail->Mailer = "smtp";
            // Establece Gmail como el servidor  de correo saliente ->SMTP
            $mail->Host = "smtp.gmail.com";
        
            // seguridad: capa de conexión segura (SSL-Secure Sockets Layer)
            $mail->SMTPSecure = 'ssl';
            //$mail->SMTPSecure = 'tls';
        
            // Establece el puerto del servidor SMTP de Gmail
            $mail->Port = 465;    // para 'ssl'
            //$mail->Port = 587; // para 'tls'
            
            // esto antes no habia que especificarlo
            // en caso de error de conexión (en algunos casos no da error):
            // esto hay que ponerlo si el servidor no cuenta  con un certificado de seguridad SSL válido
            // obligatorio para PHP 5.6 o superior (certificado de seguridad SSL válido)
                
                $mail->SMTPOptions = array(
                                    'ssl' => array(
                                    'verify_peer' => false,
                                    'verify_peer_name' => false,
                                    'allow_self_signed' => true));
        
                                    
            // asunto del mensaje
            $mail->Subject = "Acceso a Drawit/Administración";						
                                    
            $mail->Timeout=30;
        
            //******************* DESTINATARIOS **************************    
            // configuramos a quien enviamos el correo
            // podemos poner más direcciones de correo
            // tendremos que hacer un "AddAddress" por cada dirección
            $mail->AddAddress($correo);
        
            //****************** CONTENIDO CORREO ***********************  
            
            // FORMATO: texto puro
            // $mail->Body ="HOLA PEPE, ESTO ES UNA PRUEBA";
            
            
            // FORMATO: html
            
            $mail->Body =" 
                    <html> 
                    <head> 
                    <meta charset='UTF-8'>
                    </head> 
                    <body>
                    <h3>¡Hola ".$nombre."!</h3>
                    <h2><font color='#297140'>Has sido de alta como Administrador en DrawIt!</font></h2>
                    <br>
                    <br>
                    <h3>Enlace al Panel de Administración: <a href='".$linkPanel."'>".$linkPanel."</a></h3>
                    <br>
                    <p><u>Datos de acceso:</u></p>
                    <h7><b>Usuario: </b>".$nombre."</h7><br>
                    <h7><b>Contraseña: </b>".$_POST['pass']."</h7>
                    <br><br>
                    </body> 
                    </html> 
                    "; 
            
        
        
            //**************************************************
            // enviamos el mensaje
            $exito = $mail->Send();
            //**************************************************
            
            // Comprobamos si hay problemas con el correo
            if(!$exito){
                    echo "3";
                }else{
                    echo "0";
                }
    }else if($error==1062){
        echo "Error: Ya existe un administrador con ese nombre. Elige otro.";
    }else {
        echo "Registro NO AÑADIDO</b>";
        $numerror=mysqli_errno($conexion); 
        $descrerror=mysqli_error($conexion); 
        echo "Se ha producido un error nº $numerror que corresponde a: $descrerror  <br>"; 
    }
    
    // Cerramos la conexión
    mysqli_close($conexion);
}

?>