<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

// Obtenemos el termino de busqueda por get
$termino=$_GET["termino"];

// Consulta para publicaciones
$consultaBusqueda="SELECT * FROM $dibujos WHERE TITULO LIKE '%$termino%' OR DESCRIPCION LIKE '%$termino%' ";
$resulBusquedaPosts=mysqli_query($conexion,$consultaBusqueda);
$registroPost=mysqli_fetch_array($resulBusquedaPosts);
$nResulPosts=mysqli_num_rows($resulBusquedaPosts);

// Consulta para usuarios
$consultaBusqueda="SELECT * FROM $usuarios WHERE NOMBRE LIKE '%$termino%' ORDER BY NOMBRE";
$resulBusquedaUsers=mysqli_query($conexion,$consultaBusqueda);
$registroUser=mysqli_fetch_array($resulBusquedaUsers);
$nResulUsers=mysqli_num_rows($resulBusquedaUsers);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Buscar "<?php echo $termino ?>"</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <link rel="icon" type="image/png" href="../icono.png" />
    <!-- JQUERY -->
	<script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <!-- JS -->
    <script type="text/javascript" src="../js/funcionalidad.js"></script>
    <!-- FONTAWESOME -->
    <link rel="stylesheet" type="text/css" href="../fontawesome/css/all.min.css">
    <!-- PLUGIN GALERIA -->
    <script type="text/javascript" src="../plugins/justifiedGallery/jquery.justifiedGallery.min.js"></script>
    <link rel="stylesheet" href="../plugins/justifiedGallery/justifiedGallery.min.css">
    <!-- PLUGIN TABLE SORTER -->
    <script type="text/javascript" src="../plugins/tablesorter/jquery.tablesorter.min.js"></script>
</head> 

<body>

<?php include("../header.php"); ?>

<script type="text/javascript">
    $(document).ready(iniciarGaleria);

    function iniciarGaleria(){
        $("input#buscar").val("<?php echo $termino ?>");
        $('#galeriaBusqueda').justifiedGallery({
            rowHeight: 170,
            margins: 8
        });
    }

    // Scroll de la galeria
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            for (var i = 0; i < 5; i++) {
            $('#galeriaBusqueda').append('<a>' +
                '<img src="http://path/to/image" />' + 
                '</a>');
            }
            $('#galeriaBusqueda').justifiedGallery('norewind');
        }
    });

</script>

<div id="cuerpoBusqueda" class="container-fluid">
    <div class="container col-sm-10 col-md-11">
        <div class="principal main row">
            <div class="columna2 col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <!-- TITULO BUSQUEDA -->
                <h2 id="tituloBusqueda">
                    <i class="fas fa-search"></i>
                    Resultados de "<?php echo $termino ?>"<br>
                </h2>

                <!-- PESTAÑAS DE RESULTADOS -->
                <ul id="resultadosBusq" class="nav nav-tabs mt-5 mb-3"  role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tabPublicaciones" data-toggle="tab" href="#publicaciones" role="tab" aria-controls="home" aria-selected="true"><i class="fas fa-images"></i> Publicaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tabUsuarios" data-toggle="tab" href="#usuarios" role="tab" aria-controls="profile" aria-selected="false"><i class="fas fa-users"></i> Usuarios</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <!-- RESULTADOS PUBLICACIONES -->
                    <div id="publicaciones" class="tab-pane fade show active" role="tabpanel" aria-labelledby="tabPublicaciones">
                    
                        <div class='alert alert-info mt-4'>Se han encontrado <b><?php echo $nResulPosts ?> publicaciones.</b></div>
            
                            <div id="galeriaBusqueda">
            
                            <?php
                                while($registroPost){
                                    // AL PONER LO DEL SPAN DEL USUARIO  NO FUNCIONA EL SCROLL
                                    echo "<div class='foto'>
                                            <a href='../dibujo/".$registroPost['IDP']."'>
                                                <img id='".$registroPost['LIKES']."' style='object-fit: cover; padding-bottom: 30px;' alt='".$registroPost['TITULO']."' src='../dibujos/img/".$registroPost['IMAGEN']."'/>
                                            </a>
                                                
                                        </div>";
                                        // <!--<span class='row'>Usuario</span>-->
                                    $registroPost= mysqli_fetch_array($resulBusquedaPosts);
                                }
                            ?>
                            </div>
                    </div>
                    
                    <!-- RESULTADOS USUARIOS -->
                    <div id="usuarios" class="tab-pane fade" role="tabpanel" aria-labelledby="tabUsuarios">
                        <div class='alert alert-info mt-4 mb-4'>Se han encontrado <b><?php echo $nResulUsers ?> usuarios.</b></div>
                        
                        <table id='tablaUsuarios'>
                        <?php
                            while($registroUser){
                                // AL PONER LO DEL SPAN DEL USUARIO  NO FUNCIONA EL SCROLL
                                echo "<tr class='mb-3'>
                                        <td class='tdFoto'><a href='/drawit/user/".$registroUser['NOMBRE']."'><img class='rounded-circle' src='../img/perfiles/".$registroUser['FOTO']."'/></a></td>
                                        <td class='tdNombre'><a href='/drawit/user/".$registroUser['NOMBRE']."'>".ucwords($registroUser['NOMBRE'])."</a></td>
                                    <tr>";
                                    // <!--<span class='row'>Usuario</span>-->
                                $registroUser= mysqli_fetch_array($resulBusquedaUsers);
                            }
                        ?>
                        </table>
                    </div>       
                </div>
            </div>
        </div>
    </div>
</div>
<?php if($nResulPosts>0){ ?>   

<?php

// Cerramos la conexion
mysqli_close($conexion);

} ?>

</body>
</html>