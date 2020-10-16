<?php

require("../conexion.php");

$consultaR="SELECT $usuarios.NOMBRE,$reportes.TIPO,$reportes.IDR,$reportes.MOTIVO,$reportes.COMENTARIOS, $reportes.FECHA,$dibujos.IDP,$dibujos.IMAGEN
            FROM ($usuarios INNER JOIN $reportes
            ON $usuarios.IDU=$reportes.IDU)
                INNER JOIN $dibujos
                ON $reportes.IDP=$dibujos.IDP";
$resultadoR=mysqli_query($conexion,$consultaR);
$nregistrosR= mysqli_num_rows($resultadoR);
$registroR= mysqli_fetch_array($resultadoR);

if($nregistrosR==0){
    echo "No hay reportes pendientes";
}else{
    ?>
    <h2 class="mt-4 mb-4">Lista de reportes</h2>
    <table id="tablaReportes" class="col-12 table table-bordered table-hover text-center">
        <tr>
            <th>Tipo</th>
            <th>Motivo</th>
            <th>Imagen</th>
            <th>Link post</th>
            <th>Reporte de</th>
            <th>Detalles</th>
            <th>Fecha</th>
            <th>Acción</th>
        </tr>
    <?php
    while($registroR){

        // VALUE DE LOS BOTONES
        $valueBotonR= $registroR['IDR']."/".$registroR['IDP'];

        echo "<tr>
                <td>".$registroR['TIPO']."</td>
                <td>".$registroR['MOTIVO']."</td>
                <td><img class='imgReporte' style='width: 100px' src='../dibujos/img/".$registroR["IMAGEN"]."'></td>
                <td><a href='/drawit/dibujo/".$registroR['IDP']."' target='_blank'>Link</a></td>
                <td>".$registroR['NOMBRE']."</td>
                <td>".$registroR['COMENTARIOS']."</td>
                <td>".$registroR['FECHA']."</td>
                <td>
                    <input type='hidden' id='oculto' value='".$valueBotonR."'>
                    <button id='mantenerPost' value='".$valueBotonR."' class='btn btn-success' title='Mantener'><i class='fas fa-check'></i></button>
                    <button id='borrarPost' value='".$valueBotonR."' class='btn btn-danger' title='Borrar'><i class='fas fa-times'></i></button>
                </td>
            </tr>
            <tr>
                
            </tr>";

        // Avanzamos al siguiente registro
        $registroR=mysqli_fetch_array($resultadoR);
    } ?>
</table>

<?php }

// Cerramos la conexión
mysqli_close($conexion);

?>