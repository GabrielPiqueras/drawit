<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

// Obtengo el id del registro por GET y consigo la URL de la imagen
$idPOST= $_GET['id'];

// ID del usuario con session iniciada
$idSesion = $_SESSION["id"];

/* CONSULTA TABLA DIBUJOS */

$consulta="SELECT $usuarios.IDU,$usuarios.NOMBRE,TITULO,IMAGEN,DESCRIPCION,CATEGORIA,LIKES
           FROM $dibujos INNER JOIN $usuarios
           ON $dibujos.IDU=$usuarios.IDU
           WHERE IDP='$idPOST'";
$resultado= mysqli_query($conexion,$consulta);
$nregistros= mysqli_num_rows($resultado);

$registro=mysqli_fetch_array($resultado);

$idUsuario= $registro['IDU'];
$usuario= $registro['NOMBRE'];
$titulo= ucfirst($registro['TITULO']);
$descripcion= ucfirst($registro['DESCRIPCION']);
$categoria= $registro['CATEGORIA'];
$nLikes= $registro['LIKES'];

$urlIMG= "../dibujos/img/".$registro['IMAGEN'];

/* CONSULTA TABLA DE LIKES */

$consulta="SELECT * FROM $likes WHERE IDP='$idPOST' AND IDU='$idSesion'";
$resultado=mysqli_query($conexion,$consulta);
$haySesion=mysqli_num_rows($resultado);

?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $titulo ?></title>
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

<?php include("../header.php"); ?>

<script type="text/javascript">

$(document).ready(eventosPost);

    function eventosPost(){

        /***** FUNCIONALIDAD LIKES *****/

        /* Cuando el puntero entra en corazon */
        $("img#corazonPost").on("mouseenter", function(){
            if(this.src.endsWith("corazon_off.png")){
                this.src="../img/corazon_hover.png";
            }
        });

        /* Cuando el puntero sale del corazon */
        $("img#corazonPost").on("mouseleave", function(){
            if(this.src.endsWith("corazon_hover.png")){
                this.src="../img/corazon_off.png";
            }
        });
        
        /* Al dar clic en el corazon */
        $("img#corazonPost").click(function(){

            var accion,id,likes;
            // Si corazon rojo -> sumamos
            // Si corazon gris -> restamos
            if(this.src.endsWith("corazon_hover.png") || this.src.endsWith("corazon_off.png")){
                accion=1;
                this.src="../img/corazon_on.png";
            }else if(this.src.endsWith("corazon_on.png")){
                accion=-1;
                this.src="../img/corazon_off.png";
            }
            // Obtenemos el id de la imagen por la url
            id= window.location.href.split("/").pop();
            
            // Procesamos el like o dislike
            $.post("/drawit/publicacion/likeDislike.php",{id:id, accion:accion},function(error){
                if(error=="1"){
                    alert("Debes iniciar sesión para poder dar Me gusta.");
                    $("#corazonPost").attr("src","../img/corazon_off.png");
                }else{
                    if(accion==1){
                        // Dejamos puesto el corazon rojo
                        //this.src="../img/corazon_on.png";
                        $("img#corazonPost").off("mouseleave");
                        likes = parseInt($("span#likesPost").html())+1;
                    }else{
                        // Dejamos puesto el corazon gris
                       //this.src="../img/corazon_off.png";
                        $("img#corazonPost").off("mouseleave");
                        likes = parseInt($("span#likesPost").html())-1;
                    }

                    // Actualizamos el número de likes
                    $("span#likesPost").html(likes);
                }
            });
        });

        $("#enviarReporte").click(reportar);
        $("#botonCompartir").click(compartir);
        $("#botonComentar").click(comentar);
    }

    /***** FUNCIONALIDAD REPORTES *****/
    function reportar(){
        var t="publicacion";
        var r = $("#motivo").val();
        var c = $("#comentariosAdi").val();
        var idp = $("#post img").attr("id");

        if((validarReporte())){
            $.post("/drawit/publicacion/reportar.php",{tipo:t, razon:r, comentarios:c,idp:idp},function(error){
                if(error=="0"){
                    $("#resultadoR").addClass("alert-success")
                    .html("El reporte fue enviado correctamente.");
                    // Espero un segundo y actualizo la página
                    setTimeout(function(){location.reload();}, 1000);
                }else{
                    $("#resultadoR").addClass("alert-danger")
                    .html("Error: Hubo un problema al enviar el reporte, intentalo de nuevo más tarde.");
                }
                
            });
        }

        function validarReporte(){
            var validacion=true;

            // Reiniciamos la caja de resultados
            $("#resultadoR").removeClass("alert-success alert-danger");

            if(c.length>100){
                validacion=false;
                $("#resultadoR").addClass("alert-danger")
                .html("La longitud máxima del comentario es de 100 caracteres");
            }
            return validacion;
        }

        return false;
    }

    /***** FUNCIONALIDAD PARA COMPARTIR *****/
    function compartir(){
        //var urlVentana= "https://cybmeta.com/obtener-la-url-de-la-pagina-actual-con-javascript-y-sus-componentes";
        var urlVentana= window.location.href;
        var titulo= document.title;
        var tituloWhatsapp= titulo.replace(" ","%20");

        $("a[title='Facebook']").attr("href","http://www.facebook.com/sharer.php?u="+urlVentana);
        $("a[title='Twitter']").attr("href","http://twitter.com/share?text="+titulo+"&url="+urlVentana);
        $("a[title='Correo']").attr("href","mailto:?subject="+titulo+"&body=Echa un vistazo a este dibujo de Drawit: "+urlVentana);
        //$("a[title='Google+']").attr("href","https://plus.google.com/share?url="+urlVentana);
        $("a[title='Pinterest']").attr("href","http://pinterest.com/pin/create/button/?url="+urlVentana+"&media=[MEDIA]");
        $("a[title='Linkedin']").attr("href","http://www.linkedin.com/shareArticle?mini=true&url="+urlVentana);
        $("a[title='Reddit']").attr("href","http://www.reddit.com/submit?url="+urlVentana);
        $("a[title='Whatsapp']").attr("href","whatsapp://send?text="+tituloWhatsapp+"%20"+urlVentana);
    }

    /***** FUNCIONALIDAD PARA COMENTARIOS *****/
    function comentar(){
        var comentario = $("#cajaComentar").val();
        var postid = "<?php echo $idPOST; ?>";
        var postUserID = "<?php echo $idUsuario; ?>";

        var usuario,foto, comHTML;

        if(!comentario=="" && comentario.length<=250){
            $.post("/drawit/publicacion/comentar.php",{postID:postid, postUserID:postUserID, texto:comentario},function(error){
                if(error=="0"){
                    usuario = "<?php echo $_SESSION["nombre"]; ?>";
                    foto = $("#fotoPerfil").attr("src");
                    
                    // Añadimos el comentario a la lista (para no tener que actualizar la pagina)
                    comHTML="<div id='comentario' class='row'><div id='fotoCom' class='col-1'><img src='"+foto+"'></img></div><div id='contenidoCom' class='col-11'><div id='usuarioCom'><a href='/drawit/user/"+usuario+"'><b>"+usuario+"</b></a></div><div id='textoCom'>"+comentario+"</div></div></div>";
                    $("#listaComentarios").append(comHTML);
                    $("#cajaComentar").val("");
                }
            });
        }else{
            alert("Tu comentario no puede estar vacío ni sobrepasar los 250 caracteres");
        }
    }
</script>

<?php if($nregistros>0){ ?>
    <div class="container-fluid">
        
        <!-- PRIMERA FILA -->
        <div id="cabeceraPost" class="row">
            <div class="text-right col-md-8" style="">
                <div class="row">
                    <div class="col-sm-2" style="/*background: blue*/"></div>
                    <div id="tituloDib" class="text-left col-10" style="/*background: red*/">
                        <h2><?php echo $titulo ?></h2>
                        <span>By
                            <a href="../user/<?php echo $usuario ?>">
                                <?php echo $usuario ?>
                            </a>
                        </span>
                        <span>en 
                            <a href="../categoria/<?php echo $categoria ?>">
                                <?php echo $categoria ?>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEGUNDA FILA -->
        <div class="row">
            <div id="post" class="text-right col-md-8" style="/*background: yellow*/">
                <img id="<?php echo $idPOST; ?>" src="<?php echo $urlIMG; ?>" /><br>
            </div>
            <div class="col-md-4" style="/*background: blue*/"></div>
        </div>  

        <!-- TERCERA FILA (PIE DE IMAGEN) -->
        <div class="row">
            <div class="text-right col-8 col-sm-9 col-md-6" style="/*background: green*/">
                <div>
                    <!--<img class='float-right compartir' src='../img/compartir.png' />
                     BOTON MAS OPCIONES (...) -->
                    <div>
                        <div class="float-right compartir">
                            <a id="botonCompartir" href="#vCompartir" data-toggle="modal"><i class="fas fa-share-alt"></i></a>
                        </div>
                        <?php if($_SESSION["nombre"]){ ?>
                            <div class="float-right reportar">
                                <a id="botonReportar" href="#vReportar" data-toggle="modal"><i class="fas fa-exclamation-triangle"></i></a>
                            </div>
                        <?php } ?>
                        <br>
                        <!--<img class='float-right masOpciones' src='../img/trespuntos.png' />-->
                    </div>
                </div>
            </div>
            <div class="text-right col-4 col-sm-3 col-md-2" style="/*background: GRAY*/">


                <span id="likesPost"><?php echo $nLikes; ?></span>
                
                <?php if($haySesion>0){
                    // Si el usuario de la session ha dado like, mostrar el corazon rojo
                    // En caso contrario, el gris
                    echo "<img id='corazonPost' src='../img/corazon_on.png' />";
                }else{
                    echo "<img id='corazonPost' src='../img/corazon_off.png' />";
                } ?>
            </div>
        </div>

        <!-- VENTANA DE REPORTES -->
        <div class="modal fade text-dark" id="vReportar" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <!-- Encabezado de la ventana -->
                        <div class="modal-header">
                            <h5 class="modal-title">Reportar esta publicación</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>

                        <!-- Contenido de la ventana -->
                        <div class="modal-body">
                            <form class="formReporte" name="formReporte" action="" method="post" autocomplete="off">
                                <div class="col-md-12">

                                    <!-- MOTIVO -->
                                    <span>Razón:</span><br>
                                    <select id="motivo" class="custom-select mt-2 mb-3 col-7" require>
                                        <option value="Inapropiado" selected>Inapropiado</option>
                                        <option value="Incita al odio">Incitación al odio</option>
                                        <option value="Propiedad intelectual">Propiedad intelectual</option>
                                        <option value="Violencia">Violencia</option>
                                        <option value="Desnudos">Desnudos</option>
                                        <option value="Spam">Spam</option>
                                    </select>

                                    <!-- COMENTARIOS ADICIONALES -->
                                    <h2 id="c"></h2>
                                    <textarea id="comentariosAdi" class="form-control mb-4" rows="3" placeholder="Comentarios (opcional)"></textarea>
                                    
                                    <!-- RESULTADO Y BOTON -->
                                    <button id="enviarReporte" class="btn btn-danger">
                                        <span id="iconCargando" style="display:none;" class="spinner-border spinner-border-sm"></span>
                                        <i id="iconEntrar" class="fas fa-sign-in-alt"></i> Enviar
                                    </button>
                                    <div id="resultadoR" class="alert mt-3"></div>
                                </div>
                            </form>
                        </div>
                        <!-- Footer de la ventana -->
                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>

        <!-- VENTANA PARA COMPARTIR -->
        <div class="modal fade text-dark" id="vCompartir" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- Encabezado de la ventana -->
                    <div class="modal-header mb-2">
                        <h5 class="modal-title">¡Compartir esta publicación!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <!-- Contenido de la ventana -->
                    <div class="redesSociales modal-body text-center">
                        <div class="row">
                            <div class="col-4"><a href="#" title="Facebook" target="_blank"><img class="redSocial img-fluid" src="../img/social/facebook.png" /></a></div>
                            <div class="col-4"><a href="#" title="Twitter" target="_blank"><img class="redSocial img-fluid" src="../img/social/twitter.png" /></a></div>
                            <div class="col-4"><a href="#" title="Correo" target="_blank"><img class="redSocial img-fluid" src="../img/social/correo.png" /></a></div>
                            <!--<div class="col-4"><a href="#" title="Google+" target="_blank"><img class="redSocial img-fluid" src="../img/social/googleplus.png" /></a></div>-->
                        </div>
                        <div class="row">
                            <div class="col-4"><a href="#" title="Pinterest" target="_blank"><img class="redSocial img-fluid" src="../img/social/pinterest.png" /></a></div>
                            <div class="col-4"><a href="#" title="Linkedin" target="_blank"><img class="redSocial img-fluid" src="../img/social/linkedin.png" /></a></div>
                            <div class="col-4"><a href="#" title="Reddit" target="_blank"><img class="redSocial img-fluid" src="../img/social/reddit.png" /></a></div>
                        </div>
                        <div class="row">
                            <div class="col-4"><a href="#" title="Whatsapp" target="_blank"><img class="redSocial img-fluid" src="../img/social/whatsapp.png" /></a></div>
                        </div>

                    </div>

                    <!-- Footer de la ventana -->
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <!-- CUARTA FILA -->
        <div id="pieImagen" class="row">
            <div class="text-right col-md-8" style="">
                <div class="row">
                    <div class="col-md-2" style="/*background: blue*/"></div>
                    <div id="descripcionDib" class="text-left col-md-10 mt-2" style="/*background: red*/">
                        <p><?php echo $descripcion; ?></p>
                    </div>
                </div>
            </div>
        </div>  

        <!-- QUINTA FILA -->
        <div class="row">
            <div class="text-right col-md-8" style="">
                <div class="row">
                    <div class="col-sm-2" style="/*background: blue*/"></div>
                    <div id="comentarios" class="text-left col-10" style="/*background: red*/">
                        <h4 class="mb-6">Comentarios <i class="fas fa-comment-alt"></i></h4>
                        <div id="listaComentarios">
                            <?php 
                                $consultaCom="SELECT $usuarios.NOMBRE,$usuarios.FOTO,$comentarios.TEXTO
                                FROM $usuarios INNER JOIN $comentarios
                                WHERE $comentarios.IDP=$idPOST AND $comentarios.IDU=$usuarios.IDU";
                                $resulCom=mysqli_query($conexion,$consultaCom);
                                $nCom=mysqli_num_rows($resulCom);
                                $comentario=mysqli_fetch_array($resulCom);

                                while($comentario){
                                    echo "<div id='comentario' class='row'>
                                            <div id='fotoCom' class='col-1'><img src='/drawit/img/perfiles/".$comentario['FOTO']."'></img></div>
                                            <div id='contenidoCom' class='col-11'>
                                                <div id='usuarioCom'>
                                                    <a href='/drawit/user/".$comentario['NOMBRE']."'>
                                                        <b>".$comentario['NOMBRE']."</b>
                                                    </a>
                                                </div>
                                                <div id='textoCom'>".$comentario['TEXTO']."</div>
                                            </div> 
                                          </div>";
                                    $comentario=mysqli_fetch_array($resulCom);
                                }
                            ?>
                        </div>
                        <?php if($_SESSION["nombre"]){ ?>
                                <textarea id="cajaComentar" class="form-control mt-6" rows="3" placeholder="Escribe tu comentario"></textarea>
                            <button id="botonComentar" class="btn btn-success"><i class="fab fa-telegram-plane"></i> Enviar</button><br><br><br>
                        <?php }else{ ?>
                            <div class='alert alert-info mt-2'>Tienes que iniciar sesión para poder comentar.</div>
                        <?php } ?>
                    </div>
                </div>
                </div>
            </div>
        </div>
        </div>
    </div>
<?php }else{
    echo "<div class='alert alert-danger'>Este dibujo no existe o fue eliminado.</div>";
} ?>
</body>
</html>