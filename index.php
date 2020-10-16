<?php

    $visitas;
    $fichero=@fopen('cont.txt', 'rb');

    if(!$fichero)
    {
        $fichero=fopen('cont.txt', 'wb');
        fwrite($fichero, "0");
        fclose($fichero);

        $fichero=fopen('cont.txt', 'rb');
    }

    $visitas=(int)fgets($fichero);
    fclose($fichero);

    $visitas++;

    $fichero=fopen('cont.txt', 'wb');
    fwrite($fichero, $visitas);
    fclose($fichero);

    function muestraCont()
    {
        $fichero=fopen('cont.txt', 'rb');
        echo fgets($fichero);
        fclose($fichero);
    }

?>

<?php

// Iniciamos la sesión
session_start();

?>
<!DOCTYPE html>
<html>
<head>
	<title>Drawit</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Drawit">
    <meta name="author" content="Gabriel Piqueras">
    <link rel="icon" type="image/png" href="icono.png" />
	<!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
    <!-- FONTAWESOME -->
    <link rel="stylesheet" type="text/css" href="fontawesome/css/all.min.css">
    <!-- JQUERY -->
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <!-- JS -->
    <script type="text/javascript" src="js/paginaPrincipal.js"></script>
    <!-- PLUGIN GALERIA -->
    <script type="text/javascript" src="plugins/justifiedGallery/jquery.justifiedGallery.min.js"></script>
    <link rel="stylesheet" href="plugins/justifiedGallery/justifiedGallery.min.css">
    <!-- PLUGIN TABLE SORTER -->
    <script type="text/javascript" src="plugins/tablesorter/jquery.tablesorter.min.js"></script>
</head>

<body>

<?php

    // Cargar header
    include("header.php");

    // Cargar cuerpo
    include("body.php");
    
    // Cargar footer
    include("footer.php");

?>
</body>
</html>