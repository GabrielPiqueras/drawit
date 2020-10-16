
<div class="container-fluid">
<h2 class="mt-3 mb-4 ml-3">Dar administrador de Alta</h2>

    <?php if($_SESSION["admin"]){ ?>

        <!-- CAMPOS DE REGISTRO -->
            <div class="col-md-12">
                <div class="form-group">
                    <input type="text" id="nombre" class="form-control mb-2" placeholder="Usuario" autocomplete="off">
                    <input type="email" id="correo" name="correo" class="form-control mb-2" placeholder="Correo">
                    <input type="password" id="pass" class="form-control mb-3" placeholder="Contraseña">
                    <button id="altaAdmin" class="btn btn-secondary">Enviar</button>
                </div>
                <div id="resultadoRegistro" class="alert"></div>
            </div>

    <?php } ?>
</div>
