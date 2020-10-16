<?php

// Aquí necesitaremos la conexion del directorio superior para mostrar los usuarios
require("../conexion.php");

// Iniciamos la sesión
session_start();

?>
<html>
<head>
	<title>Panel</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
    <link rel="icon" type="image/png" href="../icono.png" />
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <!-- JQUERY -->
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <!-- FONTAWESOME -->
    <link rel="stylesheet" type="text/css" href="../fontawesome/css/all.min.css">
</head>

<style type="text/css">
    html, body{
        width: 100%;
        height: 100%;
        margin: 0 auto;
        padding: 0 auto;
    }
    
    .container-fluid,.row,#barraLateral{
        height: 100%;
    }

    #barraLateral{
        background: #f1f1f1;
        padding-right: 0px;
        padding-left: 0px;
        border-right: 1px solid black;
        z-index: 1;
    }

    #opciones{
        background: white;
        margin-top: 50%;
    }

    .opcion{
        width: 100%;
        padding: 8%;
        text-align: left;
        font-size: 20px;
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        background: white;
    }

    .usuarios{
        margin-top: 2%;
    }

    .seccion{
        display: none;
    }

    /*Tabla de Reportes */

    #tablaReportes td{
        padding-right: 0px!important;
        padding-left: 0px!important;
    }

    .imgReporte{
        width: 40%;
    }

    button#silenciar{
        color: white;
        background: #ff8007;
    }
</style>

<script type="text/javascript">

    $(document).ready(eventosPanel);

    function eventosPanel(){
    
        /* Cambiar colores de la sección selecionada */
        $(".opcion").on("click",function(){

            // Reiniciamos los colores de los botones y ocultamos las secciones
            $(".opcion").css('background-color', 'white');
            $(".seccion").hide();

            $(this).css('background-color', '#ccecff');

            var v = this.value;
            $("."+v).fadeIn(300);
        });

        /* Silenciar usuarios */

        $("button#silenciar").click(function(){
            $fila = $(this).parent().parent();
            userNombre = $fila.find(".userNombre").html();
            userId = $fila.find(".userId").html();

            // Reiniciamos el div de resultados y mandamos la informacion a la ventana
            $("#resultadoSil").removeClass("alert-success alert-danger").html("");
            $("h5.usuarioSilenciar").html("Silenciar a <font color='#7d0000'>"+userNombre+"</font>");
        });

        $("button#confirmarSilencio").click(function(){
            // Recogemos el tiempo en horas
            var tiempo = $("#tiempoSil").val();

            // Procedemos a silenciar
            $.post("../admin/silenciar.php",{userId:userId,tiempo:tiempo},function(error){
                if(error=="0"){
                    $("#resultadoSil").addClass("alert-success")
                    .html("El usuario <b>"+userNombre+"</b> fue silenciado correctamente.");
                    $fila.find(".botonEstado button").attr("disabled","disabled").html("Silenciado");
                }else{
                    $("#resultadoSil").addClass("alert-danger")
                    .html("Error: No se pudo silenciar al usuario, prueba en otro momento.");
                }
            });
        });

        /* Borrar usuarios */

        $("button#borrar").click(function(){
            var vBorrar = confirm("¿Seguro que desea eliminar a este usuario?");
            var $botonBorrar = $(this);

            if(vBorrar){
                var id = $botonBorrar.val();
                $.post("../admin/borrar.php",{idUsuario:id},function(){
                    alert("Usuario borrado correctamente");
                    $botonBorrar.parent().parent().fadeOut();
                });
            }
        });

        /* Reportar usuarios */

        $("#tablaReportes button").click(function(){

            var accion = $(this).attr("id");
            var v = $(this).val();
            var idReporte, idPost;
            var urlImagen = $(this).parent().parent().find(".imgReporte").attr("src");
            $botonPulsado = $(this);

            v=v.split("/");
            idReporte=v[0];
            idPost=v[1];

            if(accion=="mantenerPost"){
                $.post("../admin/resolverReporte.php",{IDR:idReporte, IDP:idPost, accion:0},function(){
                    $botonPulsado.parent().parent().fadeOut();
                });
            }else{
                var vBorrar = confirm("¿Seguro que desea eliminar esta publicación?");
                if(vBorrar){
                    $.post("../admin/resolverReporte.php",{IDR:idReporte, IDP:idPost, urlImagen:urlImagen, accion:1},function(){
                        $botonPulsado.parent().parent().fadeOut();
                    });
                }
            }
            
        });

        /* Dar de alta a un administrador */

        $("button#altaAdmin").click(function(){
            var nombre = $("#nombre").val().trim();
            var correo = $("#correo").val().trim();
            var pass = $("#pass").val().trim();

            reiniciarReg();

            if(validarRegistro()){
                $.post("/drawit/admin/alta_admin.php",{nombre:nombre, correo:correo, pass:pass},function(error){
                    if(error=="0"){
                        $("#resultadoRegistro").addClass("alert-success")
                        .html("Se ha enviado un correo al administrador con los datos de acceso.");
                        $("#nombre").val("").focus();
                        $("#correo,#pass").val("");
                    }else{
                        $("#resultadoRegistro").addClass("alert-danger").html(error);
                    }
                });
            }
        });

        function validarRegistro(){

            var validacion=true;
            var regExpUser = /^\w+$/g;
            var regExpEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.([a-zA-Z]){2,4}$/;
            var nombre = $("#nombre").val().trim();
            var correo = $("#correo").val().trim();
            var password = $("#pass").val();

            if(nombre.length<3 || nombre.length>25){
                $("#nombre").addClass("is-invalid");
                $("#resultadoRegistro").addClass("alert-danger")
                .html("Error: El nombre deber tener entre 3 y 25 caracteres.")
                .fadeIn(500);
                validacion=false;
            }else if(!regExpUser.test(nombre)){
                $("#nombre").addClass("is-invalid");
                $("#resultadoRegistro").addClass("alert-danger")
                .html("Error: El nombre de administrador es incorrecto, solo puede tener letras, números o subrayados.")
                .fadeIn(500);
                validacion=false;
            }else if(!regExpEmail.test(correo)){
                $("#correo").addClass("is-invalid");
                $("#resultadoRegistro").addClass("alert-danger")
                .html("Error: El correo introducido no es válido.")
                .fadeIn(500);
                validacion=false;
            }else if(password.length<5){
                $("#pass").addClass("is-invalid");
                $("#resultadoRegistro").addClass("alert-danger")
                .html("Error: La contraseña debe tener al menos 5 caracteres.")
                .fadeIn(500);
                validacion=false;
            }
            return validacion;
        }

        function reiniciarReg(){
            $("#resultadoRegistro").removeClass("alert-danger alert-success");
            $("#nombre, #correo, #pass").removeClass("is-invalid");
        }



    }
</script>

<body>
    <?php

    if(isset($_SESSION["admin"])){
        //echo "Estás logueado como un administrador!<br><br>";

        ?>
        <div class="container-fluid">
            <div class="row">

                <!-- (Espacio para poder fijar la barra) -->
                <div class="col-4 col-sm-3 col-md-3 col-lg-2"></div>
                <!-- BARRA LATERAL -->
                <div id="barraLateral" class="position-fixed col-4 col-sm-3 col-md-3 col-lg-2">
                    
                    <!-- CABECERA -->
                    <div class="col-md-12">
                        <h3><i class="fas fa-wrench"></i> Drawit</h3>
                        <h5>Administracion</h5>
                        <div class="mt-3">Conectado como <b><?php echo $_SESSION["admin"]; ?></b></div>
                        <a href="../admin/salir.php" class="boton_salir">Salir</a>
                    </div>

                    <!-- OPCIONES -->
                    <div id="opciones">
                        <button id="opUsuarios" class="btn opcion" value="usuarios"><i class="fas fa-user"></i> Usuarios</button>
                        <button class="btn opcion" value="reportes"><i class="fas fa-exclamation-triangle"></i> Reportes</button>
                        <button class="btn opcion" value="admins"><i class="fas fa-crown"></i> Administradores</button>
                     </div>

                </div>

                <!-- CONTENIDO. SECCIONES DEL PANEL -->
                <div id="contenido" class="col-8 col-sm-9 col-md-9 col-lg-10">

                    <div class="seccion usuarios">
                        <?php include("../admin/zonas/usuarios.php"); ?>
                    </div>

                    <div class="seccion reportes">
                        <?php include("../admin/zonas/reportes.php"); ?>
                    </div>

                    <div class="seccion admins">
                        <?php include("../admin/zonas/registro_admin.php"); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php }else{ ?>
            <div class="alert alert-danger col-md-4 mt-4 ml-4 mb-4" role="alert">
                Error: No tienes permiso para ver este contenido. Inicia sesión como administrador.
            </div>

            <a href="/drawit/administracion" class="btn btn-secondary ml-4"><i class="fas fa-undo"></i> Volver al Login</a>
        <?php } ?>
</body>
</html>