<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Verificacion</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <!-- JQUERY -->
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <!-- JS -->
    <script type="text/javascript" src="../js/funcionalidad.js"></script>
    <!-- FONTAWESOME -->
    <link rel="stylesheet" type="text/css" href="../fontawesome/css/all.min.css">
</head>

<!-- QUITAR DESPUES DE LA PRESENTACION -->

<style type="text/css">
button#numNotif {
    display: none;
}
</style>
<body>
<?php include("../header.php");

// Recogemos el codigo hash por get
$hash= $_GET["hash"];

$consulta="SELECT * FROM $upendientes WHERE USERHASH='$hash'";
$resultado= mysqli_query($conexion,$consulta);
$nregistros= mysqli_num_rows($resultado);

if($nregistros>0){
        // Si existe damos de alta al usuario en la tabla principal
        // Metemos los datos del registro anterior en variables
        $registro= mysqli_fetch_array($resultado);

        $nombre=$registro['NOMBRE'];
        $correo=$registro['CORREO'];
        $pass=$registro['PASS'];
        $foto=$registro['FOTO'];
        $ingreso=$registro['INGRESO'];
        $estado=0;

        // Lo damos de alta en la tabla principal
        // No hay que borrarlo de la tabla pendientes porque de eso se encargará el borrado automático del SCHEDULE
        $consulta="INSERT $usuarios (NOMBRE,CORREO,PASS,FOTO,INGRESO,ESTADO) VALUES ('$nombre','$correo','$pass','$foto','$ingreso','$estado')";
        $resultado= mysqli_query($conexion,$consulta);

        $error= mysqli_errno($conexion);

        // Si no hay error mostramos un mensaje de éxito
        if($error==0){
                $eliminar="DELETE FROM $upendientes WHERE USERHASH='$hash'";
                $ejecutar=mysqli_query($conexion,$eliminar); ?>
                <div id="resultado" class="alert alert-success">¡Tu cuenta fue activada correctamente! Ya puedes iniciar sesión.</div>
        <?php }else{ ?>
                <div id="resultado" class="alert alert-danger">Error: El código de activación ha caducado, vuelve a registrarte.</div>     
        <?php }
                //$numerror=mysqli_errno($conexion); 
                //$descrerror=mysqli_error($conexion); 
                // echo "Usuario no registrado. Se ha producido un error nº $numerror que corresponde a: $descrerror"; 
        }else{ ?>
                <div id="resultado" class="alert alert-danger">Error: El código de activación ha caducado, vuelve a registrarte.</div>
        <?php }
        
?>

<?php if($_SESSION["nombre"]){
        // Si hay sesion iniciada redirijo al usuario a la pagina principal
        echo "<div class='alert alert-info'>Redirigiendo a la pagina principal...</div>";
        echo "<script type='text/javascript'>setTimeout(function(){window.location = '/drawit'},4000);</script>";
} ?>
    
</body>
</html>