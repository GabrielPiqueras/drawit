<?php

// Iniciamos la sesión
session_start();

?>
<!DOCTYPE html>
<html>
<head>
	<title>BNMA App</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/estilos.css">
</head>

<style type="text/css">

</style>

<body>
<div class="container-fluid">
    <h2 class="mt-3 mb-4 ml-3">Dar administrador de Alta</h2>

    <?php if($_SESSION["admin"]){ ?>

        <!-- FORMULARIO DE REGISTRO -->
        <form class="formularioAdministrador" name="formularioAdministrador" action="alta_admin.php" method="post" autocomplete="off">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="text" id="usuario" name="usuario" class="form-control mb-2" placeholder="Usuario">
                    <input type="password" id="pass" name="pass" class="form-control mb-3" placeholder="Contraseña">
                    <button class="btn btn-secondary">Enviar</button>
                </div>
            </div>
        </form>

    <!-- FOOTER -->
    <footer>


    <?php }else{
        echo "<div class='alert alert-danger'>Solo un administrador puede dar de alta a otros administradores</div>";
    } ?>
</div>
<div class="container">
            <h3>...</h3>
        </div>
    </footer>
	<!-- JQUERY -->
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/bootstrap.min.js"></script>
</body>
</html>