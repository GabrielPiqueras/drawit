<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

?>
<!DOCTYPE html>
<html>
<head>
	<title>Registro</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
	<meta name="description" content="Drawit">
    <meta name="author" content="Gabriel Piqueras">
    <link rel="icon" type="image/png" href="../icono.png" />
    <!-- CSS -->
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

<body style="background: url('../img/registrofondo.jpg');">

<script type="text/javascript">

    $(document).ready(eventosRegistro);

    function eventosRegistro(){
        $("#resultado").fadeOut().removeClass("alert-success alert-danger");
        $("input#usuario").focus();
        $("#enviar").click(registro);
    }

    function registro(){
        var user = $("#usuario").val();
        var email = $("#correo").val();
        var password = $("#pass").val();
        
        reiniciarReg();

        if(validarRegistro()){
            
            // Muestro el loader
            $("#iconEnviar").css("display","none");
            $("#iconCargando").css("display","inherit");
            
            $.post("/drawit/registrar/alta_usuario.php",{usuario:user,correo:email, pass:password},function(respuesta){
                // Oculto el loader
                $("#cargando").css("display","none");

                if(respuesta==1){
                    $("#resultado").addClass("alert-danger")
                    .html("Error: El usuario ya está registrado en el sistema.");
                }else if(respuesta==2){
                    $("#resultado").addClass("alert-danger")
                    .html("Error: La cuenta de este usuario está pendiente de activación desde su correo.");
                }else if(respuesta==3){
                    $("#resultado").addClass("alert-warning")
                    .html("Error: No se pudo enviar el email de activación. Revisa tu conexión a internet.");
                }else if(respuesta.startsWith("Error")){
                    $("#resultado").addClass("alert-warning")
                    .html(respuesta);
                }else{
                    // Y muestro el mensaje
                    $("#resultado").addClass("alert-success").html(respuesta);
                }
                $("#resultado").fadeIn(500);
                
                // Oculto el loader
                $("#iconCargando").css("display","none");
                $("#iconEnviar").css("display","inherit");
            });
        }

        return false;
    }

    function validarRegistro(){

        var validacion=true;
        var regExpUser = /^\w+$/g;
        var regExpEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]{2,4})+$/;
        var usuario = $("#usuario").val().trim();
        var correo = $("#correo").val().trim();
        var password = $("#pass").val().trim();

        if(usuario.length<3 || usuario.length>25){
            $("#usuario").addClass("is-invalid");
            $("#resultado").addClass("alert-danger")
            .html("Error: El nombre deber tener entre 3 y 25 caracteres.")
            .fadeIn(500);
            validacion=false;
        }else if(!regExpUser.test(usuario)){
            $("#usuario").addClass("is-invalid");
            $("#resultado").addClass("alert-danger")
            .html("Error: El nombre de usuario es incorrecto, solo puede tener letras, números o subrayados.")
            .fadeIn(500);
            validacion=false;
        }else if(!regExpEmail.test(correo)){
            $("#correo").addClass("is-invalid");
            $("#resultado").addClass("alert-danger")
            .html("Error: El correo no es válido.")
            .fadeIn(500);
            validacion=false;
        }else if(password.length<5){
            $("#pass").addClass("is-invalid");
            $("#resultado").addClass("alert-danger")
            .html("Error: La contraseña debe tener al menos 5 caracteres.")
            .fadeIn(500);
            validacion=false;
        }
        return validacion;
    }

    function reiniciarReg(){
        $("#resultado").hide().removeClass("alert-danger alert-success alert-warning").html("");
        $("#usuario,#correo,#pass").removeClass("is-invalid");
    }

</script>


<!-- MUESTRO PAGINA DE REGISTRO SI NO HAY NINGUNA SESIÓN INICIADA -->
<?php
    if($_SESSION["nombre"]==null){ ?>
    <div id="registroPrincipal" class="container-fluid">
        
        <a href="/drawit" class="btn btn-info mt-3 mb-4 ml-3"><i class="fas fa-arrow-left"></i> Volver</a>
        <!-- CAMPOS DE REGISTRO -->
        <div id="registroSecundario" class="col-10 col-sm-8 col-md-7 col-lg-6 col-xl-5 mx-auto">
            
            <h2 class="mt-3 mb-4">Registrate!</h2>
            <form autocomplete="off">
                <div class="form-group text-right">
                    <input type="text" id="usuario" name="usuario" class="form-control form-control-lg mb-3" placeholder="Usuario">
                    <input type="email" id="correo" name="correo" class="form-control form-control-lg mb-3" placeholder="Correo">
                    <input type="password" id="pass" name="pass" class="form-control form-control-lg mb-3" placeholder="Contraseña">
                    <button id="enviar" class="btn btn-success btn-lg mt-3">
                        <i id="iconEnviar" class="fas fa-sign-in-alt"></i>
                        <span id="iconCargando" style="display:none;margin-bottom: 3px;" class="spinner-border spinner-border-sm"></span>
                        Enviar</button>
                </div>
            </form>
            <!-- DIV PARA MENSAJES -->
            <div id="resultado" class="mt-3 alert"></div>
        </div>
    </div>

<?php }else{
    include("../header.php");
   echo "<div class='alert alert-info mb-4'>Ya tienes una sesión iniciada.</div>";
} ?>

    <!-- FOOTER 
    <footer>
        <div class="container">
            <h3>Footer</h3>
        </div>
    </footer>-->
</body>
</html>