<?php
/**
 * @author Carlos García Cachón
 * @version 1.2
 * @since 13/12/2023
 * @copyright Todos los derechos reservados a Carlos García
 * 
 * @Annotation Proyecto LoginLogoffTema5 - Parte de 'Index' 
 * 
 */
if (!isset($_COOKIE['idioma'])) { // Comprobamos si la cookie esta declarada
    setcookie("idioma", "SP", time() + 2592000); // En caso negativo la creamos y ponemos el valor por defecto
}

if (isset($_REQUEST['botonIdioma'])) { // Comprobamos si el usuario a pulsado algún botón de idioma
    setcookie("idioma", $_REQUEST['botonIdioma'], time() + 2592000); // En caso afirmativo creamos la cookie y la cargamos con el valor seleccionado y ponemos una fecha de caducidad de 1 mes
    header('Location: indexLoginLogoffTema5.php'); // Recrgamos la página con el nuevo idioma
    exit();
}
require_once './config/configIdiomas.php'; // Incluimos el arrays con los mensajes según el idioma seleccionado
?>
<!DOCTYPE html>
<!--
        Descripción: 214DWESLoginLogoffTema5
        Autor: Carlos García Cachón
        Fecha de creación/modificación: 28/11/2023
-->
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Carlos García Cachón">
        <meta name="description" content="214DWESLoginLogoffTema5">
        <meta name="keywords" content="214DWESLoginLogoffTema5, DWES">
        <meta name="generator" content="Apache NetBeans IDE 19">
        <meta name="generator" content="60">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Carlos García Cachón</title>
        <link rel="icon" type="image/jpg" href="webroot/media/images/favicon.ico"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="webroot/css/style.css">
        <style>
            button {
                all: unset;
            }
        </style>
    </head>

    <body>
        <header class="text-center">
            <h1><?php  echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['titulo']?> LoginLogoffTema5</h1>
        </header>
        <main>
            <div class="container mt-3">
                <div class="row mb-5">
                    <div class="col text-center">
                        <form class="opcionesDelIdioma">
                            <button type="submit" value="UK" name="botonIdioma"><img src="doc/icono_UK.png" class="img-fluid" alt="Bandera_UK"></button>
                            <button type="submit" value="JP" name="botonIdioma"><img src="doc/icono_JP.png" class="img-fluid" alt="Bandera_JP"></button>
                            <button type="submit" value="SP" name="botonIdioma"><img src="doc/icono_SP.png" class="img-fluid" alt="Bandera_SP"></button>
                        </form>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col text-center">
                        <img src="doc/esquemaApp.jpg" class="img-fluid" alt="Mapeo de la Aplicación">
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center">
                        <a class="btn btn-secondary" role="button" aria-disabled="true" href='codigoPHP/login.php'>LOGIN</a>
                    </div>
                </div>
            </div>
        </main>
        <footer class="position-fixed bottom-0 end-0">
            <div class="row text-center">
                <div class="footer-item">
                    <address>© <a href="../index.html" style="color: white; text-decoration: none;">Carlos García Cachón</a>
                        IES LOS SAUCES 2023-24 </address>
                </div>
                <div class="footer-item">
                    <a href="../214DWESProyectoDWES/indexProyectoDWES.html" style="color: white; text-decoration: none;"><?php  echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['inicio']?></a>
                </div>
                <div class="footer-item">
                    <a href="https://github.com/Fighter-kun/214DWESLoginLogoffTema5.git" target="_blank"><img
                            src="webroot/media/images/github.png" alt="LogoGitHub" /></a>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    </body>

</html>