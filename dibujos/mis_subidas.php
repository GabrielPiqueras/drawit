<?php

require("../conexion.php");
session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Mis subidas</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <meta name="description" content="Drawit">
    <meta name="author" content="Gabriel Piqueras">
    <link rel="icon" type="image/png" href="../icono.png" />
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

    .total {
        font-size: 26px;
        background: #89de89;
        width: 80px;
        padding: 1px 5px 1px 5px;
        border-radius: 5px;
        color: #292929;
    }

    #imgTablaSubidas {
        width: 100%!important;
        object-fit: cover!important;
        height: 100px;
    }
    #tablaSubidas td{
        vertical-align: middle;
    }
    #tablaSubidas td.dibujo{
        padding: 0px;
        width: 12%;
    }

    i#likes{ color: #d4152f; font-size: 20px }
    i#comentarios{ color: #d41515; font-size: 20px }

    td.titulo { width: 15%; }
    td.categoria { width: 12%; }
    td.editar, td.eliminar { width: 10%; }

    td.editar > a{ font-size: 19px; color: #c34a03; background: none; border: none; }
    td.eliminar > button{ font-size: 22px;  color: #b70202; background: none; border: none; }

    td.editar > a:hover{ color: #ff6205 }
    td.eliminar > button:hover{ color: #ff0505 }

</style>

<script type="text/javascript">

    $(document).ready(evSubidas);

    function evSubidas(){
        $("td.eliminar button").click(function(){
            var $fila = $(this).parent().parent();
            var idp= $(this).val();
            var idSesion= "<?php echo $_SESSION["id"]; ?>";
            var nSubidas= $(".total").html();
            var conf = confirm("Esta publicación se borrará permanentemente. ¿Estás seguro?");

            if(conf){
                $fila.css("background","#ffabab");
                $.post("/drawit/publicacion/borrar_post.php",{idp:idp, idSesion:idSesion},function(){
                    $fila.fadeOut();

                    // Restamos -1 al número de subidas
                    $(".total").html(nSubidas-1);
                });
            }
        });
    }
</script>

<?php

// Cargamos el header
include("../header.php");

if($_SESSION["nombre"]){

    // Obtenemos todos los dibujos subidos por el usuario que tiene sesion iniciada (ID)
    $sesionID= $_SESSION["id"];
    
    $consulta="SELECT * from $dibujos WHERE IDU='$sesionID' ORDER BY FECHA DESC";
    $resultado=mysqli_query($conexion,$consulta);
    $nregistros= mysqli_num_rows($resultado);
    $registro= mysqli_fetch_array($resultado);
    
    ?>
    
    <?php if($nregistros==0){ ?>
        <div class='alert alert-danger'>No has realizado ninguna subida por el momento.</div>
    <?php }else{ ?>
        <div class="container">
            <!-- MIS SUBIDAS -->
            <h2 class="mt-4 mb-5"><i class="fas fa-file-upload"></i> Mis subidas <span class="total"><?php echo $nregistros ?></span></h2>
            
            <table id="tablaSubidas" class="col-12 table table-bordered table-hover text-center">
                <tr>
                    <th>Dibujo</th>
                    <th>Ver</th>
                    <th>Titulo</th>
                    <th><i id="likes" title="Likes" class="fas fa-heart" style=""></i></th>
                    <th><i id="comentarios" title="Comentarios" class="fas fa-comment" style="color: #1758ea; font-size: 20px"></i></th>
                    <th>Categoría</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                    <th>Fecha</th>
                </tr>
            <?php
            while($registro){
                
                echo "<tr>
                        <td class='dibujo'><img id='imgTablaSubidas' style='width: 100px' src='../dibujos/img/".$registro['IMAGEN']."'></td>
                        <td class='ver'><a href='/drawit/dibujo/".$registro['IDP']."' title='Ver publicación' target='_blank'><i class='fas fa-external-link-alt'></i></a></td>
                        <td class='titulo'>".$registro['TITULO']."</td>
                        <td>".$registro['LIKES']."</td>
                        <td>".$registro['COMENTARIOS']."</td>
                        <td class='categoria'>".$registro['CATEGORIA']."</td>
                        <td class='editar'><a id='editar' href='../editar/".$registro['IDP']."' title='Editar'><i class='fas fa-edit'></i></a></td>
                        <td class='eliminar'><button value='".$registro['IDP']."' title='Eliminar'><i class='fas fa-times'></i></button></td>
                        <td class='fecha'>".$registro['FECHA']."</td>
                    </tr>";
        
                // Avanzamos al siguiente registro
                $registro=mysqli_fetch_array($resultado);
            } ?>
        </table>
    </div>
    <?php }
    // Cerramos la conexión
    mysqli_close($conexion);
}else{
    echo "<div class='alert alert-danger'>Debes iniciar sesión para ver tus subidas.</div>";
} ?>

</body>
</html>