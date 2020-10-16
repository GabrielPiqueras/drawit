<?php

// Iniciamos la sesion
session_start();

// Vaciamos el array de las sesiones y destruimos la sesion
$_SESSION = array();
session_destroy();

// Borramos la cookie al darle un tiempo negativo
setcookie(session_name(), '', time()-60); 

header("location: /drawit/");

?>