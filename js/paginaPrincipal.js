/******* FUNCIONALIDAD LOGIN ********/

$(document).ready(eventosGaleriaPrincipal);
$(document).ready(eventosLogin);
$(document).ready(eventosNotificaciones);
$(document).ready(eventosBusqueda);

function eventosGaleriaPrincipal(){
    $("#mygallery").load("/drawit/publicaciones.php",{opcion:"r"},function(){

        $('#mygallery').justifiedGallery({
            rowHeight: 170,
            margins: 8
        });

        $(".foto").mouseenter(function(){ $(".caption.caption-visible",this).fadeIn(150); });
        $(".foto").mouseleave(function(){ $(".caption.caption-visible",this).fadeOut(150); });
        $(".foto").click(function(){ window.location=$(this).find("a").attr("href"); });
    });

    $('#ordenPublicaciones').change(function(){
        var op= $(this).val();
        //$('#mygallery').empty();
        $("#mygallery").load("/drawit/publicaciones.php",{opcion:op},function(){
            $('#mygallery').justifiedGallery({
                rowHeight: 170,
                margins: 8
            });

            $(".foto").mouseenter(function(){ $(".caption.caption-visible",this).fadeIn(150); });
            $(".foto").mouseleave(function(){ $(".caption.caption-visible",this).fadeOut(150); });
            $(".foto").click(function(){ window.location=$(this).find("a").attr("href"); });
        });
    });

    // Scroll de la galeria
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            for (var i = 0; i < 5; i++) {
                $('#mygallery').append('<a>' +
                '<img src="http://path/to/image" />' + 
                '</a>');
            }
            $('#mygallery').justifiedGallery('norewind');
        }
    });

}

function eventosLogin(){
    $("#resultado").fadeOut().removeClass("alert-success alert-danger");
    $("#botonIniciarSesion").click(function(){
        setTimeout(function(){
            $("input#usuario").focus();
        }, 500);
    });
    
    $("#iniciar").click(iniciarSesion);
}

function iniciarSesion(){
    var user = $("#usuario").val();
    var password = $("#pass").val();
    var casilla = $("#recordar"); 

    reiniciarLog();

    if(validarLogin()){
        // Si la casilla está marcada mandamos "si" en la llamada
        if(casilla.checked){
            casilla="si";
        }else{
            casilla="no";
        }
        
        // Hacemos la llamada y si da error en el php, los controlamos
        $.post("/drawit/verificarLogin.php",{usuario:user,pass:password, recordar: casilla},function(error){
            // Posibles errores
            // -1 = Usuario silenciado temporalmente
            // 0 = Usuario + pass válidos
            // 1 = Usuario válido pero contraseña no
            // 2 = Usuario no existe
            // 3 = Usuario pendiente de activación

            if(error==1){
                $("#pass").addClass("is-invalid");
                $("#resultado").addClass("alert-danger")
                .html("Error: La contraseña introducida no corresponde a este usuario.");
            }else if(error==2){
                $("#usuario").addClass("is-invalid");
                $("#resultado").addClass("alert-danger")
                .html("Error: El usuario no existe en el sistema o caducó su enlace de activación.");
            }else if(error==3){
                $("#resultado").addClass("alert-danger")
                .html("El usuario no está verificado. Revisa tu correo electrónico para activar tu cuenta.");
            }else if(error==-1){
                $("#resultado").addClass("alert-warning")
                .html("Fuiste silenciado temporalmente por un administrador, inténtalo de nuevo más tarde.");
            }else{
                reiniciarLog();

                $("#usuario,#pass").addClass("is-valid");
                
                // Muestro el circulo de cargando y el mensaje "Entrando...""
                $("#iconCargando").css("display","inherit");
                $("#iconEntrar").css("display","none");
                $("#resultado").addClass("alert-success").html("Entrando...");

                // Espero un segundo y actualizo la página
                setTimeout(function(){
                    location.reload();
                }, 1000);
            }
            $("#resultado").fadeIn(500);
        });
    }

    return false;
}

/* Funcion que valida los campos al iniciar sesion */
function validarLogin(){

    var validacion=true;
    var usuario = $("#usuario").val();
    var password = $("#pass").val();

    if(usuario==""){
        $("#usuario").addClass("is-invalid");
        $("#resultado").addClass("alert-danger").html("Error: Hay campos sin completar.").fadeIn(500);
        validacion=false;
    }
                    
    if(password==""){
        $("#pass").addClass("is-invalid");
        $("#resultado").addClass("alert-danger").html("Error: Hay campos sin completar.").fadeIn(500);
        validacion=false;
    }
    return validacion;
}

function reiniciarLog(){
    $("#resultado").removeClass("alert-danger alert-success alert-warning");
    $("#usuario,#pass").removeClass("is-invalid");
}

/*** FUNCIONALIDAD NOTIFICACIONES  ***/

function eventosNotificaciones(){

    // Clic en la campana o el número -> Mostramos la ventana de notificaciones
    $("img#campanaN, #numNotif").click(function(){

        $(".ventanaNotif").fadeIn(100);
        
        // Si tiene la clase ocultarNotificaciones se ocultará
        if($("img#campanaN").hasClass("ocultarNotificaciones")){
            $(".ventanaNotif").fadeOut(100);
            $('#numNotif').fadeOut(300);
            $.post("/drawit/notificaciones/notificacionesLeidas.php",{},function(){
                $("img#campanaN").attr("src","/drawit/img/campana_off.png");
            });
            $("img#campanaN").removeClass("ocultarNotificaciones");
        }else{
            $("img#campanaN").addClass("ocultarNotificaciones");
        }
    });

    // Al abandonar la ventana, la ocultamos y marcamos las notificaciones como leídas
    $(".ventanaNotif").mouseleave(function(){
        $(".ventanaNotif").fadeOut(100);
        $('#numNotif').fadeOut(300);
        $.post("/drawit/notificaciones/notificacionesLeidas.php",{},function(){
            $("img#campanaN").attr("src","/drawit/img/campana_off.png");
        });
        $("img#campanaN").removeClass("ocultarNotificaciones");
    });

    // Al hacer clic en una notificación, vamos al lugar donde se produjo esta
    // Version para publicaciones
    $("tr.notifP").click(function(){
        var id = $(this).attr("id");
        window.location="/drawit/dibujo/"+id;
    });

    // Version para Muros del usuario
    $("tr.notifM").click(function(){
        var linkPerfil = $(".usermenu-perfil").attr("href");
        window.location=linkPerfil;
    });

    // Boton de borrar todas las notificaciones
    $("button#borrarNotif").click(function(){
        $.post("notificaciones/borrarNotificaciones.php",{},function(error){
            if(error==0){
                $("#tablaNotificaciones tr").fadeOut(100);
                $("button#borrarNotif").fadeOut(100);
                $("button#numNotif").fadeOut(100);
                $("img#campanaN").attr("src","/drawit/img/campana_off.png");
            }
        });
    });
    
}

/*** FUNCIONALIDAD PARA BUSCAR  ***/

function eventosBusqueda(){

    // Boton del header
    $("button#buscar").click(function(){
        $(this).fadeOut(200);
        $("#zonaBuscar").fadeIn(200);
        $("#buscador").val("").focus();
    });

    // "X" del buscador
    $("#cerrarBusqueda").click(function(){
        $("#zonaBuscar").fadeOut(200);
        $("button#buscar").fadeIn(200);
    });

    // Iniciar la búsqueda al pulsar enter
    $("input#buscador").keypress(function(e){
        var termino;
        var code = (e.keyCode ? e.keyCode : e.which);

        if(code==13){
            termino = $("input#buscador").val();
            if(termino.length>0){
                location.href="/drawit/buscar/"+termino;
            }
        }
    });

    // Iniciar la búsqueda al pulsar el botón
    $("button#iniciarBusqueda").click(function(){
        termino = $("input#buscador").val();

        if(termino.length>0){
            location.href="/drawit/buscar/"+termino;
        }
    });

}



