<?php
/**
 * @author Carlos García Cachón
 * @version 1.0
 * @since 28/11/2023
 * @copyright Todos los derechos reservados a Carlos García
 * 
 * @Annotation Proyecto LoginLogoffTema5 - Parte de 'Programa' 
 * 
 */
session_start(); //Reanudamos la sesion existente

if (!isset($_SESSION['user214DWESLoginLogoffTema5'])) { // Si el usuario no se ha autentificado
    header('Location: Login.php'); //Redirigimos a el usuario al login
    exit();
}

if (isset($_REQUEST['cerrarSesion'])) { // Si el usuario hace click en el botón 'Salir' 
    session_destroy(); // Se destruye su sesión
    header('Location: ../indexLoginLogoffTema5.php'); //Redirigimos a el usuario al login
    exit;
}

// Se valida si el usuario hace click en el botón 'Detalle' 
if (isset($_REQUEST['detalle'])) {
    // Se redirige al usuario a Detalle
    header('Location: Detalle.php');
    // Termina el programa
    exit();
}
require_once '../config/configIdiomas.php'; // Incluimos el arrays con los mensajes según el idioma seleccionado
?>
<!DOCTYPE html>
<!--
        Descripción: CodigoPrograma
        Autor: Carlos García Cachón
        Fecha de creación/modificación: 05/12/2023
-->
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Carlos García Cachón">
        <meta name="description" content="CodigoPrograma">
        <meta name="keywords" content="CodigoPrograma">
        <meta name="generator" content="Apache NetBeans IDE 19">
        <meta name="generator" content="60">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Carlos García Cachón</title>
        <link rel="icon" type="image/jpg" href="../webroot/media/images/favicon.ico"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="../webroot/css/style.css">
        <script type="text/javascript" src="../webroot/javascript/reloj.js"></script>
        <style>
            /* RELOJ */
            #date {
                letter-spacing:10px;
                font-size:20px;
                font-family:'helvetica';
                color:#D4AF37;
            }

            .digit {
                width: 50px;
                height: 100px;
                display: inline-block;
                background-size: cover;
            }
        </style>
    </head>

    <body onload="startTime()"><!-- Uso de la función JS 'startTime()' para iniciar el reloj -->
        <header class="text-center">
            <h1><?php  echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['titulo']?> LoginLogoffTema5:</h1>
        </header>
        <main>
            <div class="container mt-3">
                <div class="row d-flex justify-content-start">
                    <div class="col"><!-- Formulario donde recogemos los botones para ir a detalle o cerrar sesión -->
                        <form name="Programa" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <button class="btn btn-secondary" aria-disabled="true" type="submit" name="cerrarSesion"><?php  echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['botonCerrarSesion']?></button><br><br>
                            <button class="btn btn-secondary" aria-disabled="true" type="submit" name="detalle"><?php  echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['botonDetalle']?></button>
                        </form>        
                    </div>
                    <div class="col">
                        <?php
                        /**
                         * @author Carlos García Cachón
                         * @version 1.0
                         * @since 28/11/2023
                         * @copyright Todos los derechos reservados a Carlos García
                         * 
                         * @Annotation Proyecto LoginLogoffTema5 - Parte de 'Programa' 
                         * 
                         */
                        if ($_SESSION['NumeroConexiones'] == 1) { // Compruebo si es la primera vez que se conecta y omito la fecha y hora de última conexión
                            echo("<div>".$aIdiomaSeleccionado[$_COOKIE['idioma']]['bienvenido']." ". $_SESSION['DescripcionUsuario']." ". 
                                $aIdiomaSeleccionado[$_COOKIE['idioma']]['estaEsLa']." ". $_SESSION['NumeroConexiones'] ." ".
                                $aIdiomaSeleccionado[$_COOKIE['idioma']]['vezQueTeConectas'].";</div>");
                        } else {
                            // Si se a conectado más veces muestro toda la información
                            echo("<div>".$aIdiomaSeleccionado[$_COOKIE['idioma']]['bienvenido']." ". $_SESSION['DescripcionUsuario']." ". 
                                $aIdiomaSeleccionado[$_COOKIE['idioma']]['estaEsLa']." ". $_SESSION['NumeroConexiones'] ." ".
                                $aIdiomaSeleccionado[$_COOKIE['idioma']]['vezQueTeConectas']."; "." ".
                                $aIdiomaSeleccionado[$_COOKIE['idioma']]['ultimaConexion'] ." " . $_SESSION['FechaHoraUltimaConexionAnterior'] . "</div>");
                        }
                        ?> 
                    </div>
                </div>
                <div class="row">
                    <div class="col"></div>
                    <div class="col mt-5">
                        <div id="clockdate"><!-- Bloque de código HTML donde va el reloj -->
                            <div id="clock"></div>
                            <div id="date"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="position-fixed bottom-0 end-0">
            <div class="row text-center">
                <div class="footer-item">
                    <address>© <a href="../../index.html" style="color: white; text-decoration: none; background-color: #666">Carlos García Cachón</a>
                        IES LOS SAUCES 2023-24 </address>
                </div>
                <div class="footer-item">
                    <a href="../indexLoginLogoffTema5.php" style="color: white; text-decoration: none; background-color: #666"><?php  echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['inicio']?></a>
                </div>
                <div class="footer-item">
                    <a href="https://github.com/Fighter-kun/214DWESLoginLogoffTema5.git" target="_blank"><img
                            src="../webroot/media/images/github.png" alt="LogoGitHub" class="pe-5"/></a>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    </body>

</html>