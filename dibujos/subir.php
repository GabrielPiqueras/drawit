<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

// Recogemos las categorias de la BDD
$consulta="SELECT * FROM $categorias ORDER BY NOMBRE ASC";
$resulCategorias=mysqli_query($conexion,$consulta);
$registroCat=mysqli_fetch_array($resulCategorias);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Subir dibujo</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
    <link rel="icon" type="image/png" href="icono.png" />
    <!-- JQUERY -->
	<script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <!-- JS -->
    <script type="text/javascript" src="js/funcionalidad.js"></script>
    <!-- FONTAWESOME -->
    <link rel="stylesheet" type="text/css" href="fontawesome/css/all.min.css">
    <!-- PLUGIN TABLE SORTER -->
    <script type="text/javascript" src="plugins/tablesorter/jquery.tablesorter.min.js"></script>
</head> 

<body>

<script type="text/javascript">

    $(document).ready(inicializarEventos);

    function inicializarEventos(){
        $("#enviar").click(subir);
        $("input#titulo").focus();
    }

    function subir(){
        var formData, ruta;

        if(validarSubida()){

            // formData = new FormData($("#formulario")[0]);
            ruta = "/drawit/dibujos/alta_dibujo.php";

            formData = new FormData();
            formData.append("titulo", titulo.value);
            formData.append("archivo", archivo.files[0]);
            formData.append("descripcion", descripcion.value);
            formData.append("categoria", categoria.value);

            // Muestro el loader
            $("#iconEnviar").css("display","none");
            $("#iconCargando").css("display","inherit");

            // Subir imagen
            $.ajax({
                url: ruta,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(respuesta)
                {
                    if(respuesta.startsWith("Error")){
                        $("#resultado").addClass("alert-danger").html(respuesta);
                    }else{
                        $("#resultado").addClass("alert-success").html(respuesta);

                        // Limpiamos los campos 
                        $('#titulo').val("")
                        $('#archivo').val("");
                        $('#descripcion').val("");
                        // Categoria no, para que sea mas rapido subir varios en la misma
                    }
                    $("#resultado").fadeIn(500);

                    // Oculto el loader
                    $("#iconCargando").css("display","none");
                    $("#iconEnviar").css("display","inherit");
                }
            });
        }
        return false;
    }

    function validarSubida(){

        reiniciarSub();

        var validacion=true;
        var regExp = /^\w+$/g;
        var titulo = $("#titulo").val();
        var descripcion = $("#descripcion").val();
        var categoria = $("#categoria").val();

        if(titulo.length<5 || titulo.length>30){
            $("#titulo").addClass("is-invalid");
            $("#resultado").addClass("alert-danger")
            .html("Error: El título debe tener entre 5 y 30 letras.")
            .fadeIn(500);
            validacion=false;
        }else if($("#archivo").val()==""){
            $("#resultado").addClass("alert-danger")
            .html("Error: No se ha seleccionado ningún archivo.")
            .fadeIn(500);
            validacion=false;
        }        
        return validacion;
    }

    function reiniciarSub(){
        $("#titulo").removeClass("is-invalid");
        $("#imagen").removeClass("is-invalid");
        $("#resultado").removeClass("alert-danger alert-success");
    }

</script>

    <?php
    include("../header.php");

    if(isset($_SESSION["nombre"])){ ?>

        <div id="subirPrincipal" class="container-fluid">
            
            <div id="subirSegundario" class="col-10 col-sm-8 col-md-7 col-lg-6 col-xl-5 mx-auto">
                <a href="../drawit/" class="btn btn-info mt-3 mb-4 ml-1"><i class="fas fa-arrow-left"></i> Volver</a>
                    <!-- BOTÓN DE VOLVER Y H2 -->
                    <h2 class="mt-3 mb-5 ml-1">¡Subir dibujo!</h2>

                    <!-- CAMPOS DE REGISTRO -->
                    <form method="post" id="formulario" enctype="multipart/form-data" autocomplete="off">
                        <div class="form-group">
                            <input type="text" class="titulo form-control mb-3" id="titulo" placeholder="Título">
                            <input type="file" class="archivo form-control-file" id="archivo">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control mb-3" rows="3" id="descripcion" name="descripcion" placeholder="Descripción"></textarea>
                            <select id="categoria" class="custom-select mb-4">
                                <option selected>Sin categoría</option>
                                <?php while($registroCat){
                                    echo "<option value='".$registroCat['NOMBRE']."'>".$registroCat['NOMBRE']."</option>";
                                    $registroCat=mysqli_fetch_array($resulCategorias);
                                } ?>
                            </select>
                            <button id="enviar" class="btn btn-success">
                                <i id="iconEnviar" class="fas fa-sign-in-alt"></i>
                                <span id="iconCargando" style="display:none;" class="spinner-border spinner-border-sm"></span>
                                Enviar</button>
                        </div>
                    </form>

                    <!-- DIV PARA MENSAJES -->
                    <div id="resultado" class="mt-3 alert"></div>
                </div>
        </div>
    <?php }else{ // Si no existe:
        echo "<div class='alert alert-danger'>Tienes que iniciar sesión para realizar subidas.</div>";

    } ?>
    
    <!-- FOOTER 
    <footer>
        <div class="container">
            <h3>Footer</h3>
        </div>
    </footer>-->
</body>
</html>