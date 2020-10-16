<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

$sesionID=$_SESSION["id"];
$idPost=$_GET["id"];

// Comprobamos si existe en la tabla dibujos 
$consultaP="SELECT * FROM $dibujos WHERE IDP='$idPost' AND IDU='$sesionID'";
$resultadoP=mysqli_query($conexion,$consultaP);
$nregistrosP=mysqli_num_rows($resultadoP);

$registroP=mysqli_fetch_array($resultadoP);

// Obtenemos los datos de la publicacion
$titulo=$registroP["TITULO"];
$url=$registroP["IMAGEN"];
$descripcion=$registroP["DESCRIPCION"];
$categoria=$registroP["CATEGORIA"];

// Recogemos las categorias de la BDD menos la categoria seleccionada
$consultaC="SELECT * FROM $categorias WHERE NOMBRE!='$categoria' ORDER BY NOMBRE ASC ";
$resulC=mysqli_query($conexion,$consultaC);
$registroC=mysqli_fetch_array($resulC);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Editar publicación</title>
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
    <!-- PLUGIN TABLE SORTER -->
    <script type="text/javascript" src="../plugins/tablesorter/jquery.tablesorter.min.js"></script>
</head> 

<body>

<style type="text/css">
    #imgEditarPost img{
        width: 60%;
    }
    html,body{
        height: auto;
    }
</style>

<script type="text/javascript">

    $(document).ready(inicializarEventos);

    function inicializarEventos(){
        $("#archivo").change(ocultarFoto);
        $("#enviar").click(editar);
        $("input#titulo").focus();
    }

    // Esto simplemente oculta la foto vieja que se muestra en el formulario
    function ocultarFoto(){
        $("#imgEditarPost img").hide(250);
    }

    function editar(){
        var formData, ruta;

        if(validarSubida()){

            // formData = new FormData($("#formulario")[0]);
            ruta = "/drawit/publicacion/procesar_edicion.php";

            formData = new FormData();
            formData.append("id", <?php echo $idPost; ?>);
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

                        // Espero 4 segundos y actualizo la página
                        setTimeout(function(){
                            location.reload();
                        }, 4000);
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

    if(isset($_SESSION["nombre"])){
        
        if($nregistrosP>0){ ?>
            <div id="editarPrincipal" class="container-fluid">
                
                <div id="editarSecundario" class="col-10 col-sm-8 col-md-7 col-lg-6 col-xl-5 mx-auto">
                    <a href="/drawit/mis-subidas/" class="btn btn-info mt-3 mb-4 ml-1"><i class="fas fa-arrow-left"></i> Volver</a>
                        <!-- BOTÓN DE VOLVER Y H2 -->
                        <h2 class="mt-3 mb-5 ml-1">Editar publicación</h2>

                        <!-- CAMPOS DE REGISTRO -->
                        <form method="post" id="formulario" enctype="multipart/form-data" autocomplete="off">
                            <div class="form-group">
                                <input type="text" class="titulo form-control mb-3" id="titulo" value="<?php echo $titulo; ?>" placeholder="Título">
                                <h5 class="mt-4 mb-3">Cambiar imagen</h5>
                                <input type="file" class="archivo form-control-file" id="archivo">
                            </div>
                            <div class="form-group" id="imgEditarPost">
                                <img src="/drawit/dibujos/img/<?php echo $url; ?>">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control mb-3 mt-2" rows="3" id="descripcion" name="descripcion" placeholder="Descripción"><?php echo $descripcion; ?></textarea>
                                <select id="categoria" class="custom-select mb-4">
                                    <option value="<?php echo $categoria; ?>" selected>
                                        <?php echo $categoria; ?>
                                    </option>
                                    <?php while($registroC){
                                        echo "<option value='".$registroC['NOMBRE']."'>".$registroC['NOMBRE']."</option>";
                                        $registroC=mysqli_fetch_array($resulC);
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
        <?php }else{
            echo "<div class='alert alert-danger'>Esta publicación no existe.</div>";
        } ?>
    <?php }else{ // Si no existe:
        echo "<div class='alert alert-danger'>Tienes que iniciar sesión para realizar subidas.</div>";
    } ?>
    
</body>
</html>