<?php

require("conexion.php");

// Obtenemos el termino de busqueda por get
$opcion=$_POST["opcion"];

if($opcion==null){
    $opcion="r";
}

if($opcion=="r"){
    $consulta="SELECT * FROM $dibujos ORDER BY FECHA DESC LIMIT 300";
}else if($opcion=="p"){
    $consulta="SELECT * FROM $dibujos ORDER BY LIKES DESC LIMIT 300";
}else if($opcion=="c"){
    $consulta="SELECT * FROM $dibujos ORDER BY COMENTARIOS DESC LIMIT 300";
}

// Obtengo los dibujos, como máximo aparecerán 300 en la pagina principal
$resultado= mysqli_query($conexion,$consulta);
$registro= mysqli_fetch_array($resultado);
$nregistros=mysqli_num_rows($resultado);
    
?>


<?php
    while($registro){
        // AL PONER LO DEL SPAN DEL USUARIO  NO FUNCIONA EL SCROLL
        echo "<div class='foto'>
                <a href='dibujo/".$registro['IDP']."'>
                    <img id='".$registro['LIKES']."' style='object-fit: cover; padding-bottom: 30px;' alt='".$registro['TITULO']."' src='dibujos/img/".$registro['IMAGEN']."'/>
                </a>
                            
            </div>";
            // <!--<span class='row'>Usuario</span>-->
        $registro= mysqli_fetch_array($resultado);
    }
?>