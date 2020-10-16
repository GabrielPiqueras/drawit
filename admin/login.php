<?php

// Iniciamos la sesión
session_start();

if(isset($_SESSION["admin"])){
    header("location: administracion/panel");
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Administración</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
	<!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
    <!-- JQUERY -->
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</head>

<style type="text/css">
    body{
        height: 100%;
    }
    .loginAdmin{
        margin-top: 15%;
    }
</style>

<script type="text/javascript">
$(document).ready(eventosLoginAdmin);

function eventosLoginAdmin(){
    $("#resultado").fadeOut().removeClass("alert-success alert-danger");
    $("input#usuario").focus();
    $("#entrar").click(iniciarSesionAdmin);
}

function iniciarSesionAdmin(){
    var user = $("#usuario").val();
    var password = $("#pass").val();

    reiniciarLoginAdmin();

    if(validarLogin()){
        
        // Hacemos la llamada y si da error en el php, los controlamos
        $.post("admin/verificarAdmin.php",{usuario:user,pass:password},function(error){
            
            if(error!=0){
                $("#usuario").addClass("is-invalid");
                $("#pass").addClass("is-invalid");
                $("#resultado").addClass("alert-danger").html("Error: Usuario o contraseña incorrectos.");
            }else{
                reiniciarLoginAdmin();

                // ponemos todo verde
                $("#usuario").addClass("is-valid");
                $("#pass").addClass("is-valid");
                
                // Muestro el circulo de cargando y el mensaje "Entrando...""
                $("#iconCargando").css("display","inherit");
                $("#iconEntrar").css("display","none");
                $("#resultado").addClass("alert-success").html("Entrando...");

                // Espero un segundo y actualizo la página
                setTimeout(function(){
                    location.reload();
                }, 1000);
            }
            $("#resultado").fadeIn(500);
        });
    }

    return false;
}

/* Funcion que valida los campos al iniciar sesion */
function validarLogin(){

    var validacion=true;
    var usuario = $("#usuario").val();
    var password = $("#pass").val();

    if(usuario==""){
        $("#usuario").addClass("is-invalid");
        $("#resultado").addClass("alert-danger")
        .html("Error: Hay campos sin completar.")
        .fadeIn(500);
        validacion=false;
    }
                    
    if(password==""){
        $("#pass").addClass("is-invalid");
        $("#resultado").addClass("alert-danger")
        $("#resultado").html("Error: Hay campos sin completar.")
        .fadeIn(500);
        validacion=false;
    }
    return validacion;
}

function reiniciarLoginAdmin(){
    $("#resultado").removeClass("alert-danger alert-success");
    $("#usuario").removeClass("is-invalid");
    $("#pass").removeClass("is-invalid");
}

</script>

<body>
    <div class="loginAdmin container-fluid col-md-4 text-center">
            <h2 class="mt-3 mb-4 ml-3">Panel Administración</h2>
            <!-- FORMULARIO DE LOGIN -->
            <form class="formularioAdmin" name="formularioAdmin" method="post" autocomplete="off">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="text" id="usuario" name="usuario" class="form-control form-control-lg mb-2" placeholder="Usuario">
                        <input type="password" id="pass" name="pass" class="form-control form-control-lg mb-3" placeholder="Contraseña">
                        <button id="entrar" class="btn btn-lg btn-secondary">Enviar</button>
                    </div>
                    <div id="resultado" class="alert">
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>