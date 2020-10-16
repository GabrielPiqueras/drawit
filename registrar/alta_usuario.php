<?php

require("../conexion.php");
require('../librerias/phpmailer/PHPMailer.php');
require('../librerias/phpmailer/SMTP.php');
require('../librerias/phpmailer/Exception.php');
            
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Recogemos los datos del formulario
$nombre=$_POST["usuario"];
$correo=$_POST["correo"];
$password=password_hash($_POST["pass"],PASSWORD_BCRYPT);

// Comprobamos si existe en la tabla de usuarios principal
$consulta="SELECT * FROM $usuarios WHERE NOMBRE='$nombre' OR CORREO='$correo'";
$resultado=mysqli_query($conexion,$consulta);
$nregistros= mysqli_num_rows($resultado);

if($nregistros==0){
    // El usuario no existe en la tabla principal
    // Comprobamos si existe en la tabla de usuarios pendientes
    $consulta="SELECT * FROM $upendientes WHERE NOMBRE='$nombre' OR CORREO='$correo'";
    $resultado=mysqli_query($conexion,$consulta);
    $nregistros= mysqli_num_rows($resultado);

    if($nregistros==0){
        // No existe en ninguna de las 2 tablas, realizamos el proceso:

        // Genero el hash y lo concateno al link de activacion (AMIGABLE)
        $hash = md5($correo).rand(10000,99999);
        $linkActivacion="http://89.29.146.227/drawit/verificar/".$hash;
        //$linkActivacion="http://localhost/drawit/verificar/".$hash;
        
        
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
            $mail->Subject = "Activación de tu cuenta";						
                                    
            // TIEMPO DE ESPERA
            // el valor por defecto 10 de Timeout es un poco escaso ya que es una cuenta SMTP gratuita 
            // por tanto lo ponemos a 30  
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
                                            <h1><b>Bienvenid@ a DrawIt!</b></h1>
                                            <br>
                                            <br>
                                            <br>
                                            <p>Estás a un paso de activar tu cuenta, para ello haz clic en el siguiente enlace de activación:</p>
                                            <p><a href='".$linkActivacion."'><b>Activar cuenta</b></a></p>
                                            <br><br>
                                            </body> 
                                            </html> 
                                            "; 
            
        
        
            //**************************************************
            // enviamos el mensaje
            $exito = $mail->Send();
            //**************************************************
            
            // comprobamos si hay problemas
            if(!$exito){
                    // Error: El correo electrónico no se pudo enviar, vuelve a intentarlo más tarde.
                    echo "3";
                    // echo "<br/>".$mail->ErrorInfo;
                }else{
                    
                    // Si el correo se ha enviado con éxito
                    // Damos de alta al usuario en la tabla de pendientes
                    $consulta="INSERT $upendientes (NOMBRE,CORREO,PASS,FOTO,INGRESO,USERHASH) VALUES('$nombre','$correo','$password','default.jpg',NOW(),'$hash')";
                    mysqli_query($conexion,$consulta);
                    
                    $eventoTemporal="CREATE EVENT BORRAR_$nombre
                                     ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 15 MINUTE
                                    DO 
                                    BEGIN
                                        DELETE FROM $upendientes WHERE NOMBRE='$nombre';
                                    END;";
                    mysqli_query($conexion,$eventoTemporal);
                    
                    $error= mysqli_errno($conexion);
                    
                    // Si no hay error enviamos el link de activacion por correo y un mensaje de éxito
                    if($error==0){
                        echo "Se ha enviado un enlace de activación a tu correo para completar tu registro. Caducará en 15 minutos.";
                    }else if($error==1062){
                        echo "1";
                    }else{
                        $numerror=mysqli_errno($conexion); 
                        $descrerror=mysqli_error($conexion); 
                        echo "Error: No se pudo enviar el enlace de verificación a tu correo. Revisa tu conexión a internet y vuelve a intentarlo."; 
                        // echo "Usuario no registrado. Se ha producido un error nº $numerror que corresponde a: $descrerror"; 
                    }
            }
        
        /************* FIN ENVIAR ENLACE DE ACTIVACION POR CORREO *************/
        
    }else{
        // El usuario está pendiente de activación
        echo "2";
    }

}else{
    // El usuario ya está registrado
    echo "1";
}


// Cerramos la conexión
mysqli_close($conexion);

?>