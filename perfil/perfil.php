<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

// Obtengo el usuario del perfil
$nick=$_GET["user"];

// OBTENEMOS LOS DATOS DEL PERFIL
$consulta="SELECT * FROM $usuarios WHERE NOMBRE='$nick'";
$resultado= mysqli_query($conexion,$consulta);
$registro= mysqli_fetch_array($resultado);
$existe= mysqli_num_rows($resultado);

// Id del perfil del usuario
$perfilID= $registro["IDU"];

// Foto de perfil del usuario
$fotoPerfil= "../img/perfiles/".$registro["FOTO"];

?>
<!DOCTYPE html>
<html>
<head>
    <?php
    if($_SESSION["nombre"]==$nick){
        echo "<title>Tu perfil</title>";
    }else{
        echo "<title>".ucfirst($nick)." - Perfil</title>";
    } ?>
	
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

    #comentarios { padding-left: 0px; padding-right: 0px!important; }
    #listaComentarios { padding-left: 15px; }
    #contenidoCom { padding-left: 0px; }
    /* De momento igual para comentarios pares e impares */
    #comentario:nth-child(odd),#comentario:nth-child(even) {
        background: #e4e4e4;
    }

    @media (max-width: 575px) {
        /* Comentarios */
        div#contenidoCom {
            padding-left: 10%;
        }
    }
</style>

<script type="text/javascript">

    $(document).ready(inicio);

    function inicio(){
        viejo = $("#nombre").val();
        $("#cambiarNombre").click(editarNombre);
        //$("input#archivo").on("change",subir());
        $("#archivo").change(cambiarFoto);
        $("#botonComentar").click(comentar);
    }

    /***** FUNCIONALIDAD PARA EDITAR DATOS *****/

    function editarNombre(){
        // Obtengo el nuevo nombre y la foto
        // Si se cambia el nombre también cambio el nombre de la foto
        var nuevo = $("#nombre").val();
        var foto = $("#fotoEditarPerfil").attr("src");

        // Deshabilitamos el botón de la contraseña y reiniciamos el div de resultado
        $("#cambiarPass").addClass('disabled');
        $("#resulPerfil").hide().removeClass("alert-danger alert-success alert-info");

        // Si no hay errores, procedemos a hacer el cambio
        if(validarNombre()){
            $.post("/drawit/perfil/editar_perfil.php",{viejo:viejo, nuevo:nuevo, foto:foto},function(error){
                if(error==1){
                    $("#nombre").addClass("is-invalid");
                    $("#resulPerfil").addClass("alert-danger")
                    .html("Error: Este nombre de usuario no está disponible. Elige otro.")
                    .fadeIn(500);
                }else{
                    location.href="/drawit/user/"+nuevo;
                }
            });
        }
    }

    function validarNombre(){

        var validacion=true;
        var regExpUser = /^\w+$/g;
        var nuevo = $("#nombre").val().trim();

        reiniciarResulPerfil();

        if(viejo==nuevo){
            $("#resulPerfil").addClass("alert-info").html("No se han producido cambios.").fadeIn(500);
            validacion=false;
        }else if(nuevo.length<3 || nuevo.length>25){
            $("#nombre").addClass("is-invalid");
            $("#resulPerfil").addClass("alert-danger")
            .html("Error: El nombre deber tener entre 3 y 25 caracteres.")
            .fadeIn(500);
            validacion=false;
        }else if(!regExpUser.test(nuevo)){
            $("#nombre").addClass("is-invalid");
            $("#resulPerfil").addClass("alert-danger")
            .html("Error: El nombre de usuario es incorrecto, solo puede tener letras, números o subrayados.")
            .fadeIn(500);
            validacion=false;
        }
        $("#cambiarPass").removeClass('disabled');
        return validacion;
    }

    function editarPass(){
        // Obtengo la nuevo contraseña
        // Si se cambia el nombre también cambio el nombre de la foto
        var pass = $("#pass").val();

        $("#cambiarNombre").addClass('disabled');
        reiniciarResulPerfil();

        // Si la contraseña cumple los requisitos, procedemos a hacer el cambio
        if(validarPass()){
           $.post("/drawit/perfil/editar_perfil.php",{pass:pass},function(error){
                if(error=="0"){
                    $("#resulPerfil").addClass("alert-success").html("La contraseña fue cambiada correctamente.")
                    .fadeIn(500);
                    $("#cambiarNombre").removeClass('disabled');
                }else{
                    $("#pass").addClass("is-invalid");
                    $("#resulPerfil").addClass("alert-danger")
                    .html("Error: La contraseña no se pudo cambiar debido a un error desconocido.")
                    .fadeIn(500);
                }
            });
        }
    }

    function validarPass(){
        var validacion=true;
        var pass = $("#pass").val().trim();       

        if(pass.length<5){
            $("#pass").addClass("is-invalid");
            $("#resulPerfil").addClass("alert-danger")
            .html("Error: La contraseña debe tener al menos 5 carácteres.")
            .fadeIn(500);
            validacion=false;
        }
        $("#cambiarNombre").removeClass('disabled');
        return validacion;
    }

    function reiniciarResulPerfil(){
        $("#pass").removeClass("is-invalid");
        $("#resulPerfil").hide().removeClass("alert-danger alert-success alert-info");
    }

    /***** FUNCIONALIDAD PARA CAMBIAR FOTO *****/
    function cambiarFoto(){

        // Creamos el objeto formData, que obtiene los datos del formulario (0=el primero):
        var formData= new FormData($("#formFile")[0]);
        var ruta= "/drawit/perfil/cambiar_foto.php";
        
        // Reiniciamos la caja del resultado
        $("#resultadoF").removeClass("alert-danger alert-success");
        $("#fotoEditarPerfil").removeAttr("src");
        
        if($("#archivo").val()!=""){

            $.ajax({
                url: ruta,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(respuesta){

                    if(respuesta.startsWith("Error")){
                        $("#resultadoF").addClass("alert-danger").html(respuesta);
                    }else{
                        $("#resultadoF").addClass("alert-success")
                        .html("La foto de perfil se actualizó correctamente.");
                        
                        $("#fotoEditarPerfil").attr("src","../img/perfiles/"+respuesta);
                        $("#fotoPerfil").attr("src","../img/perfiles/"+respuesta);
                    }
                    $("#resultadoF").fadeIn(500);

                    // Habilitamos el botón de subir archivos y lo reseteamos:
                    $("#archivo").removeAttr("disabled","disabled").val("");
                }
            });

            // Borramos el contenido del div de respuesta y deshabilitamos el boton de subir:
            $("#resultadoF").html("");
            $("#archivo").attr("disabled","disabled");

        // SI NO HAY NINGÚN archivo selecionado:
        }else{
            
            // Por si acaso hay algo en el div de respuesta lo borramos
            $("#respuesta").html("");
        }
    }

    /***** FUNCIONALIDAD PARA COMENTAR EN EL MURO *****/
    function comentar(){
        var comentario = $("#cajaComentar").val();
        var userid = "<?php echo $_SESSION["id"]; ?>";
        var perfilid = "<?php echo $perfilID ?>";

        var usuario,foto, comHTML;

        if(!comentario=="" && comentario.length<=250){
            $.post("/drawit/perfil/comentar_muro.php",{perfilID:perfilid, userID:userid, texto:comentario},function(error){
                if(error=="0"){
                    usuario = "<?php echo $_SESSION["nombre"]; ?>";
                    foto = $("#fotoPerfil").attr("src");
                    
                    comHTML="<div id='comentario' class='row'><div id='fotoCom' class='col-2 col-md-1'><img src='"+foto+"'></img></div><div id='contenidoCom' class='col-10 col-md-11 '><div id='usuarioCom'><a href='/drawit/user/"+usuario+"'><b>"+usuario+"</b></a></div><div id='textoCom'>"+comentario+"</div></div></div>";
                    $("#listaComentarios").append(comHTML);
                    $("#cajaComentar").val("");
                }
            });
        }else{
            alert("Tu comentario no puede estar vacío ni sobrepasar los 250 caracteres");
        }
    }

</script>

<?php include("../header.php");

// Si el perfil existe en la base de datos
if($existe>0){
    
?>

<div class="container">
    <!-- NOMBRE -->
    <h1 class="mt-4 mb-3 text-capitalize"><?php echo $nick ?></h1>

    <!-- SECCIONES -->
    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tabPerfil" data-toggle="tab" href="#perfil" role="tab" aria-controls="home" aria-selected="true"><i class="fas fa-user"></i> Perfil</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="subidas-tab" data-toggle="tab" href="#subidas" role="tab" aria-controls="subidas" aria-selected="false"><i class="fas fa-images"></i> Publicaciones</a>
        </li>
        <?php if($_SESSION["nombre"]==$nick){ ?>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><i class="fas fa-user-edit"></i> Editar perfil</a>
            </li>
        <?php } ?>
        <li class="nav-item">
            <a class="nav-link" id="botonMuro contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false"><i class="fas fa-comment-alt"></i> Muro</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <!-- PERFIL PÚBLICO -->
        <div id="perfil" class="tab-pane fade show active" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
                <?php

                // SUBIDAS DEL USUARIO
                $subidasPerfil="SELECT * FROM $dibujos WHERE IDU='$perfilID'";
                $subidasResul=mysqli_query($conexion,$subidasPerfil);
                $publicacion=mysqli_fetch_Array($subidasResul);
                $nsubidas=mysqli_num_rows($subidasResul);

                // NUMERO DE COMENTARIOS
                $nComentarios="SELECT * FROM $comentarios
                               WHERE IDU='$perfilID'
                               UNION
                               SELECT * FROM $comentarios_muro
                               WHERE IDU='$perfilID'";
                $resulnComentarios=mysqli_query($conexion,$nComentarios);
                $nComentarios=mysqli_num_rows($resulnComentarios);

                // NUMERO DE ME GUSTA
                $nMeGusta="SELECT SUM(LIKES) AS LIKES FROM $dibujos WHERE IDU='$perfilID'";
                $resulnMeGusta=mysqli_query($conexion,$nMeGusta);
                $resulnMeGusta=mysqli_fetch_array($resulnMeGusta);
                $nMeGusta=$resulnMeGusta["LIKES"];

                if($nMeGusta==null){
                    $nMeGusta=0;
                }

                ?>
                <div class="col-md-6">
                    <div id="estadisticasPerfil">
                        <p class="nPublicaciones"><i class="fas fa-images"></i> <b>Publicaciones:</b> <?php echo $nsubidas ?></p>
                        <p class="nMeGusta"><i class="fas fa-heart"></i> <b>Me gusta recibidos:</b> <?php echo $nMeGusta ?></p>
                        <p class="nComentarios"><i class="fas fa-comments"></i> <b>Comentarios:</b> <?php echo $nComentarios ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <img id="fotoPublica" src="<?php echo $fotoPerfil ?>">
                </div>
            </div>
        </div>
        
        <!-- PUBLICACIONES REALIZADAS -->
        <div class="tab-pane fade show" id="subidas" role="tabpanel" aria-labelledby="subidas-tab">
            <?php
            
            $cont=0;

            if($nsubidas>0){ ?>

                <table class="col-12 col-md-12">
                    <tr>
                    <?php while($publicacion){
                        echo "<td class='publicacion'><a href='/drawit/dibujo/".$publicacion['IDP']."'><img class='img-fluid' src='/drawit/dibujos/img/".$publicacion['IMAGEN']."'></td>";
                        $publicacion=mysqli_fetch_Array($subidasResul);
                        $cont++;

                        if($cont==6){
                            $cont=0; ?>
                            </tr><tr>
                        <?php } 
                    } ?>
                </table>
            <?php }else{
                echo "$nick no ha publicado nada todavía.";
            }

            ?>
        </div>
        
        <!-- EDITAR PEFIL -->
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
                <div class="col-md-6">
                    <!-- CAMBIAR NOMBRE DE USUARIO -->
                    <label>Nombre:</label>
                    <div class="input-group input-group-default mb-3">
                        <div class="input-group-prepend"><span class="input-group-text" id="basic-addon1"><i class="fas fa-pencil-alt"></i></span></div>
                        <input type="text" id="nombre" class="form-control mr-3" value="<?php echo $_SESSION['nombre']; ?>" aria-label="Nombre" aria-describedby="basic-addon1">
                    </div>
                    
                    <button id="cambiarNombre" class="btn btn-success" style="cursor: pointer"><i class="fas fa-sync-alt"></i> Cambiar nombre</button>
                    
                    <!-- CAMBIAR CONTRASEÑA -->
                    <div class="input-group input-group-default mt-5 mb-3">
                        <div class="input-group-prepend"><span class="input-group-text" id="basic-addon1"><i class="fas fa-key"></i></span></div>
                            <input type="password" id="pass" class="form-control mr-3" placeholder="Nueva contraseña" aria-label="Contraseña" aria-describedby="basic-addon1">
                        </div>
                        
                        <button id="cambiarPass" class="btn btn-info" onClick="editarPass()"><i class="fas fa-sync-alt"></i> Cambiar contraseña</button>
            
                        <div id="resul" class="alert col-md-9 mt-4 mb-4 d-none" role="alert">
                    </div>
                    <div id="resulPerfil" class="mt-3 alert"></div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <!-- ESPACIO VACÍO -->
                        <div class="col-md-1">
                            
                        </div>
                        <!-- FOTO DE PERFIL -->
                        <div class="col-md-8">
                            <p>Foto de perfil:</p>
                            <img id="fotoEditarPerfil" class="img-fluid" src="../img/perfiles/<?php echo $_SESSION['foto']; ?>">
                            <div class="custom-file mt-2">
                                <form method="post" id="formFile" enctype="multipart/form-data">
                                    <!-- INPUT FILE -->
                                    <input type="file" id="archivo" name="archivo">
                                    <!--<input type="file" class="custom-file-input" id="archivo" name="archivo customFileLang" lang="es"> -->
                                    <!--<label class="custom-file-label" for="customFileLang">Foto</label> -->
                                    <input type="hidden" name="userfoto" value="<?php echo $nick; ?>">
                                </form>
                                <div id="resultadoF" class="alert mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>       

        <!-- MURO -->
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <div id="comentarios" class="text-left col-12" style="/*background: red*/">
                <div id="listaComentarios">
                    <?php
                        $consultaMuro="SELECT $usuarios.NOMBRE,$usuarios.FOTO,$comentarios_muro.TEXTO
                        FROM $usuarios INNER JOIN $comentarios_muro
                        WHERE $comentarios_muro.PERFILID='$perfilID' AND $comentarios_muro.IDU=$usuarios.IDU";

                        $resulMuro=mysqli_query($conexion,$consultaMuro);
                        $nComMuro=mysqli_num_rows($resulMuro);
                        $comentario=mysqli_fetch_array($resulMuro);

                        if($nComMuro>0){
                            while($comentario){
                                echo "<div id='comentario' class='row'>
                                        <div id='fotoCom' class='col-2 col-md-2 col-lg-1'><img src='/drawit/img/perfiles/".$comentario['FOTO']."'></img></div>
                                        <div id='contenidoCom' class='col-10 col-md-10 col-lg-11'>
                                            <div id='usuarioCom'>
                                                <a href='/drawit/user/".$comentario['NOMBRE']."'>
                                                    <b>".$comentario['NOMBRE']."</b>
                                                </a>
                                            </div>
                                            <div id='textoCom'>".$comentario['TEXTO']."</div>
                                        </div> 
                                    </div>";
                                $comentario=mysqli_fetch_array($resulMuro);
                            }
                        }else{
                            echo "Este muro no tiene comentarios. ¡Se el primero!";
                        }
                    ?>
                </div>
                <?php if($_SESSION["nombre"]){ ?>
                        <textarea id="cajaComentar" class="form-control mt-6" rows="3" placeholder="Escribe tu comentario"></textarea>
                        <button id="botonComentar" class="btn btn-success"><i class="fab fa-telegram-plane"></i> Enviar</button><br><br><br>
                <?php } ?>          
        </div>
    </div>

</div>

<?php }else{ // Si no existe:
        echo "<div class='alert alert-danger'>Este usuario no existe.</div>";

} ?>
    
</body>
</html>