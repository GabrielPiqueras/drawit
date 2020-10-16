
<div class="row">
    <div class="col-md-1"></div>
    <div class="usuarios col-md-10">
        <h2 class="mt-6">Lista de usuarios</h2>
            <table class="table table-bordered table-hover text-center">
                <?php
                    // Hacemos la consulta para obtener todos los usuarios
                    $consulta="SELECT * FROM $usuarios ORDER BY NOMBRE ASC";
                    $resultado= mysqli_query($conexion,$consulta);
                    $nregistros=mysqli_num_rows($resultado);

                    $registro= mysqli_fetch_array($resultado);

                ?>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>PERFIL</th>
                    <th>SILENCIAR</th>
                    <th>BORRAR</th>
                </tr>

                <?php
                    while($registro){
                        
                        // Para evitar silenciar a un usuario ya silenciado
                        if($registro["ESTADO"]==0){
                            $botonEstado="<button href='#vSilenciar' value='".$registro['IDU']."' id='silenciar' class='btn btn-warning' data-toggle='modal'><i class='fas fa-clock'></i> Silenciar</button></td>";
                        }else if($registro["ESTADO"]==-1){
                            $botonEstado="<button id='usuarioSilenciado' class='btn btn-warning' disabled><i class='fas fa-clock'></i> Silenciado</button></td>";
                        }
                        echo "<tr>
                                <td class='userId'>".$registro['IDU']."</td>
                                <td class='userNombre'>".$registro['NOMBRE']."</td>
                                <td><a href='/drawit/user/".$registro['NOMBRE']."' target='_blank'>Link</a></td>
                                <td class='botonEstado'>$botonEstado</td>
                                <td><button value='".$registro['IDU']."' id='borrar' class='btn btn-danger'><i class='fas fa-times-circle'></i> Borrar</button></td>
                            </tr>";

                        // Avanzamos al siguiente registro
                        $registro=mysqli_fetch_array($resultado);
                    }

                    // Cerramos la conexión
                    mysqli_close($conexion);
                ?>
                                
            </table>
    </div>
</div>

<!-- VENTANA PARA SILENCIAR -->
<div class="modal fade text-dark" id="vSilenciar" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <!-- Encabezado de la ventana -->
                        <div class="modal-header">
                            <h5 class="modal-title usuarioSilenciar">Silenciar</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>

                        <!-- Contenido de la ventana -->
                        <div class="modal-body">
                                <div class="col-md-12">

                                    <!-- MOTIVO -->
                                    <span>Duración:</span><br>
                                    <select id="tiempoSil" class="custom-select mt-2 mb-3 col-7" require>
                                        <option value=1 selected>1 hora</option>
                                        <option value=2>2 horas</option>
                                        <option value=3>3 horas</option>
                                        <option value=4>4 horas</option>
                                        <option value=5>5 horas</option>
                                        <option value=6>6 horas</option>
                                        <option value=7>7 horas</option>
                                        <option value=8>8 horas</option>
                                        <option value=9>9 horas</option>
                                        <option value=10>10 horas</option>
                                        <option value=11>11 horas</option>
                                        <option value=12>12 horas</option>
                                        <option value=13>13 horas</option>
                                        <option value=14>14 horas</option>
                                        <option value=15>15 horas</option>
                                        <option value=16>16 horas</option>
                                        <option value=17>17 horas</option>
                                        <option value=18>18 horas</option>
                                        <option value=19>19 horas</option>
                                        <option value=20>20 horas</option>
                                        <option value=21>21 horas</option>
                                        <option value=22>22 horas</option>
                                        <option value=23>23 horas</option>
                                        <option value=24>24 horas</option>
                                    </select>
                                    
                                    <!-- RESULTADO Y BOTON -->
                                    <div>
                                        <button id="confirmarSilencio" class="btn btn-warning">
                                            <span id="iconCargando" style="display:none;" class="spinner-border spinner-border-sm"></span>
                                            <i id="iconEntrar" class="fas fa-clock"></i> Silenciar
                                        </button>
                                    </div>
                                    <div id="resultadoSil" class="alert mt-3"></div>
                                </div>
                        </div>
                        <!-- Footer de la ventana -->
                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>