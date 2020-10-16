<script type="text/javascript">
    
</script>
<?php

require("../conexion.php");

// Iniciamos la sesión
session_start();

// Obtenemos la categoria por Get
$categoria=$_GET["cat"];

$consultaCat="SELECT * FROM $categorias WHERE NOMBRE='$categoria'";
$resulCat=mysqli_query($conexion,$consultaCat);
$nregistrosCat=mysqli_num_rows($resulCat);

?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $categoria ?></title>
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

<?php include("../header.php");

if($nregistrosCat>0){ ?>   

    <script type="text/javascript">
        $(document).ready(iniciarGaleria);

        function iniciarGaleria(){
            $('#galeriaCat').justifiedGallery({
                rowHeight: 170,
                margins: 8
            });
        }

        // Scroll de la galeria
        $(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() == $(document).height()) {
                for (var i = 0; i < 5; i++) {
                $('#galeriaCat').append('<a>' +
                    '<img src="http://path/to/image" />' + 
                    '</a>');
                }
                $('#galeriaCat').justifiedGallery('norewind');
            }
        });

    </script>

    <div id="cuerpoCategoria" class="container-fluid">
        <!-- La caja de imagenes tendrá el máximo, el container de arriba irá actuando segun -->
        <div class="container col-sm-10 col-md-11">
            <div class="principal main row">
                <div class="columna2 col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <!-- TITULO CATEGORIA -->
                    <h2 id="tituloCategoria" class="mb-6">
                        <i class="fas fa-folder"></i>
                        <?php echo $categoria ?><br><br>
                    </h2>
        
                    <?php
                        require("../conexion.php");

                        // Obtengo los dibujos, como máximo aparecerán 300 en la pagina principal
                        $consulta="SELECT * FROM $dibujos WHERE CATEGORIA='$categoria' ORDER BY IDP DESC LIMIT 300";
                        $resultado= mysqli_query($conexion,$consulta);
                        $registro= mysqli_fetch_array($resultado);
                        $nregistros=mysqli_num_rows($resultado);
                        
                        ?>
        
                        <div id="galeriaCat">
        
                        <?php
                            while($registro){
                                        // AL PONER LO DEL SPAN DEL USUARIO  NO FUNCIONA EL SCROLL
                                        echo "<div class='foto'>
                                                <a href='../dibujo/".$registro['IDP']."'>
                                                    <img id='".$registro['LIKES']."' style='object-fit: cover; padding-bottom: 30px;' alt='".$registro['TITULO']."' src='../dibujos/img/".$registro['IMAGEN']."'/>
                                                </a>
                                                    
                                            </div>";
                                            // <!--<span class='row'>Usuario</span>-->
                                        $registro= mysqli_fetch_array($resultado);
                            }
                        ?>
        
                        </div>
                </div>
            </div>
        </div>
    </div>
<?php

// Cerramos la conexion
mysqli_close($conexion);

}else{
    echo "<div class='alert alert-danger'>Esta categoría no existe.</div>";
} ?>

</body>
</html>