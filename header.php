<div id="zonaBuscar">
    <div id="cajaBuscador">
        <i id="cerrarBusqueda" class="fas fa-times"></i><br>
            <input type="text" id="buscador" autocomplete="off">
            <button id="iniciarBusqueda" class="btn btn-lg btn-info"><i class="fas fa-search"></i> Buscar</button>
    </div>
</div>

<?php

require("conexion.php");

// Datos de la sesion
$sesionID= $_SESSION["id"];
$nombre= $_SESSION["nombre"];

// Comprobamos si el usuario está silenciado
$comprobarEstado="SELECT ESTADO FROM $usuarios WHERE IDU='$sesionID'";
$resulEstado= mysqli_query($conexion,$comprobarEstado);
$resulEstado=mysqli_fetch_array($resulEstado);
$estado=$resulEstado["ESTADO"];

if($estado==-1){
    header("location: /drawit/salir");
}

// Ponemos la foto de perfil
$usuarioSesionFoto= "/drawit/img/perfiles/".$_SESSION['foto'];

// Si hay sesion iniciada
if($_SESSION['id']){

    function tiempoNot($fecha){
        // Para sacar el tiempo que tiene una notificación
        $instanteActual = new DateTime(date("Y-m-d H:i:s"));
        $fechaPublicacion = new DateTime($fecha);
        $diff = $instanteActual->diff($fechaPublicacion);

        $dias = ($diff->days);
        $horas = ($diff->h);
        $minutos = ($diff->i);
        $segundos = $diff->s;
        $total=$dias*86400+$horas*3600+$minutos*60+$segundos;
        $tiempoN;

        switch(true){
            case $total<60: $tiempoN=$segundos." seg.";break;
            case $total>=60 && $total<3600: $tiempoN= $minutos." min.";break;
            case $total>=3600 && $total<7200: $tiempoN= $horas."h ".$minutos." m";break;
            case $total>=7200 && $total<86400: $tiempoN= $horas."h ".$minutos." m";break;
            case $total>=86400 && $total<172800: $tiempoN= $dias." día";break;
            case $total>=172800: $tiempoN=$dias." días";
        }
        return $tiempoN;
    }
}

?>
<header>
    <div class="container cajaHeader">
        <div class="row">
            
            <!-- 1 LOGO -->
            <div class="titulo col-3 col-sm-3 col-md-3 col-lg-2">
                <h1><a style="color:white" href="/drawit">Drawit!</a></h1>
            </div>

            <!-- 2 BUSCADOR -->
            <div class="col-2 col-sm-3 col-md-3 col-lg-2">
                <button id="buscar" class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
            </div>

            <?php
            
            // Si la sesión no existe muestro el login
            if(!isset($_SESSION["nombre"])){
                
            ?>

            <!-- 3 LOGIN -->
            <div class="zonaLogin col-7 col-sm-6 col-md-6 col-lg-8">
                <a href="/drawit/registro/" class="btn btn-primary mt-2 mb-2 float-right"><i class="fas fa-plus"></i> Registrate</a>
                <a id="botonIniciarSesion" href="#vLogin" class="btn btn-success mr-2 mt-2 float-right" data-toggle="modal"><i class="fas fa-sign-in-alt"></i> Iniciar sesión</a>
                
                <!-- VENTANA DE LOGIN EMERGENTE -->
                <div class="modal fade text-dark" id="vLogin" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <!-- Encabezado de la ventana -->
                            <div class="modal-header">
                                <h5 class="modal-title">Iniciar sesión</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <!-- Contenido de la ventana LOGIN -->
                            <div class="modal-body">
                                <form class="formulario" name="formulario" action="" method="post" autocomplete="off">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" id="usuario" name="usuario" class="form-control mb-2" placeholder="Usuario">
                                            <input type="password" id="pass" name="pass" class="form-control mb-2" placeholder="Contraseña">
                                            <div class="form-check mb-2">
                                                <input id="recordar" class="form-check-input" type="checkbox" name="recordar">
                                                <label class="form-check-label" for="defaultCheck1">Recuérdame</label>
                                            </div>
                                            <div id="resultado" class="alert">

                                            </div>
                                            <button id="iniciar" class="btn btn-success">
                                                <span id="iconCargando" style="display:none;" class="spinner-border spinner-border-sm"></span>
                                                <i id="iconEntrar" class="fas fa-sign-in-alt">
                                                </i> Entrar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Footer de la ventana -->
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php }else{ ?>
                <!-- 4 FOTO DE PERFIL Y MENU DE USUARIO -->
                 <div class="col-7 col-sm-6 col-md-6 col-lg-8">
                    <div class="float-right mt-2 usermenu">
                        <span id="nombreUsuario" class="mr-2"><?php echo $_SESSION["nombre"]; ?></span>
                        <!--<a href="/drawit/user/<?php echo $_SESSION["nombre"]; ?>" clas="bsoton_perfil btn btn-info mr-1"><i class="fas fa-user-alt"></i></i> Perfil</a>-->
                        <img id="fotoPerfil" class="rounded-circle" src="<?php echo $usuarioSesionFoto ?>">
                        <div class="usermenu-opciones">
                            <a class="usermenu-perfil" href="/drawit/user/<?php echo $_SESSION["nombre"]; ?>"><i class="fas fa-user-alt"></i> Mi perfil</a>
                            <a href="/drawit/subir"><i class="fas fa-upload"></i> Subir</a>
                            <a href="/drawit/mis-subidas/"><i class="fas fa-upload"></i> Mis subidas</a>
                            <a href="/drawit/salir"><i class="fas fa-times-circle"></i> Cerrar sesión</a>
                        </div>
                    </div>
                    <?php
                        
                        // NOTIFICACIONES DE COMENTARIOS EN PUBLICACIONES
                        $notifComentariosP="SELECT $notificaciones.FECHA,$notificaciones.IDLUGAR,$notificaciones.LEIDA,$dibujos.IMAGEN,$usuarios.NOMBRE
                                        FROM ($dibujos INNER JOIN $notificaciones
                                        ON $dibujos.IDP=$notificaciones.IDLUGAR)
                                        INNER JOIN $usuarios
                                        ON $notificaciones.EMISOR=$usuarios.IDU
                                        WHERE $notificaciones.TIPO='P'
                                        AND $notificaciones.RECEPTOR='$sesionID' AND $notificaciones.EMISOR!='$sesionID'";
                        $resulNotifComP=mysqli_query($conexion,$notifComentariosP);
                        $notifComP=mysqli_fetch_array($resulNotifComP);
                        $nNotif=mysqli_num_rows($resulNotifComP);

                        // NOTIFICACIONES DE COMENTARIOS EN MURO
                        $notifComentariosM="SELECT $usuarios.NOMBRE,$notificaciones.FECHA,$notificaciones.IDLUGAR,$notificaciones.LEIDA
                                           FROM $usuarios INNER JOIN $notificaciones
                                           ON $usuarios.IDU=$notificaciones.EMISOR
                                           WHERE $notificaciones.TIPO='M'
                                           AND $notificaciones.RECEPTOR='$sesionID' AND $notificaciones.EMISOR!='$sesionID'";
                        $resulNotifComM=mysqli_query($conexion,$notifComentariosM);
                        $notifComM=mysqli_fetch_array($resulNotifComM);
                        $nNotif+=mysqli_num_rows($resulNotifComM);
                    ?>                  
                    <div class="float-right mt-2 notificaciones">
                    <button id="numNotif" class="btn btn-danger float-right"><?php echo $nNotif ?></button>
                    <img id="campanaN" class="rounded-circle" src="/drawit/img/campana_off.png" title="Notificaciones">
                        <div class="ventanaNotif">
                            <table id="tablaNotificaciones" class="tablesorter">
                                <thead>
                                    <tr>
                                        <th></th><th></th><th></th><th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php

                                if($nNotif==0){
                                    echo "No tienes notificaciones pendientes";
                                }else{
                                    ?>
                                        <tr><button id="borrarNotif" class="btn btn-danger float-right"><i class="fas fa-trash-alt"></i> Borrar todo</button></tr>
                                    <?php
                                
                                    $notifNoLeidas=0;

                                    while($notifComP){
                                        if($notifComP['LEIDA']=='N'){
                                            $notifNoLeidas++;
                                            $estadoN="noleida";
                                        }else{
                                            $estadoN="leida";
                                        }
                                        $tiempo=tiempoNot($notifComP['FECHA']);
                                        echo "<tr id='".$notifComP['IDLUGAR']."' class='notifP $estadoN'>
                                            <td class='imagenN'><img src='/drawit/dibujos/img/".$notifComP['IMAGEN']."'></td>
                                            <td class='usuarioN'><b>".$notifComP['NOMBRE']."</b> ha comentado tu publicación </td>
                                            <td class='tiempoN'>".$tiempo." </td>
                                            <td class='fechaN'>".$notifComP['FECHA']."</td>
                                        </tr>";

                                        $notifComP=mysqli_fetch_array($resulNotifComP);
                                    }
    
                                    while($notifComM){
                                        if($notifComM['LEIDA']=='N'){
                                            $notifNoLeidas++;
                                            $estadoN="noleida";
                                        }else{
                                            $estadoN="leida";
                                        }
                                        $tiempo=tiempoNot($notifComM['FECHA']);
                                        echo "<tr id='".$notifComP['IDLUGAR']."' class='notifM $estadoN'>
                                            <td class='imagenN'><img src='/drawit/img/notif_muro.png'></td>
                                            <td class='usuarioN'>".$notifComM['NOMBRE']." ha comentado en tu muro </td>
                                            <td class='tiempoN'>".$tiempo." </td>
                                            <td class='fechaN'>".$notifComM['FECHA']."</td>
                                        </tr>";

                                        $notifComM=mysqli_fetch_array($resulNotifComM);
                                    }
                                }

                            ?></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</header>

<script type="text/javascript">
    // Convertirmos la tabla notificaciones en una tabla ordenable
    $("#tablaNotificaciones").tablesorter({theme : 'blue',sortList: [[3,1]]});

    // Número de notificaciones NO leidas
    var notifNoLeidas = "<?php echo $notifNoLeidas; ?>";

    // Si están todas leídas, ocultamos el número
    // En caso contrario mostramos la campana en rojo y el número
    if(notifNoLeidas==0){
        $('#numNotif').hide();
    }else{
        $("img#campanaN").attr("src","/drawit/img/campana_on.png");
        $('#numNotif').html(notifNoLeidas);
    }
</script>
