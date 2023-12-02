
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
                            header('Location: Login.php'); //Redirigimos al usuario al ejercicio01.php para que se autentifique
                            exit();
                        }

                        if (isset($_REQUEST['salir'])) { // Si el usuario hace click en el botón 'Salir' 
                            session_destroy(); // Se destruye su sesión
                            header('Location: Login.php'); // Y se manda de nuevo a 'Login.php'
                            exit;
                        }

                        // Incluyo la librería de validación para comprobar los campos y el fichero de configuración de la BD
                        require_once '../core/231018libreriaValidacion.php';
                        require_once "../config/confDBPDO.php";

                        try {
                            $miDB = new PDO(DSN, USERNAME, PASSWORD); // Instanciamos un objeto PDO y establecemos la conexión

                            $sql = "SELECT T01_DescUsuario, T01_NumConexiones, T01_FechaHoraUltimaConexion FROM T01_Usuario WHERE T01_CodUsuario=:user";
                            $consulta = $miDB->prepare($sql); // Preparamos la consulta
                            $parametros = [":user" => $_SESSION['user214DWESLoginLogoffTema5']];

                            $consulta->execute($parametros); // Ejecutamos la consulta
                            $registro = $consulta->fetchObject(); // Obtenemos el primer registro de la consulta

                            $descUsuario = $registro->T01_DescUsuario; // Almacenamos la descripción del usuario
                            $nConexiones = $registro->T01_NumConexiones; // Almacenamos el número de conexiones del usuario en '$nConexiones'
                            $fechaUltimaConexionUsuario = $registro->T01_FechaHoraUltimaConexion; // Almacenamos la fecha y hora de última conexión del usuario
                        } catch (PDOException $miExcepcionPDO) {
                                $errorExcepcion = $miExcepcionPDO->getCode(); // Almacenamos el código del error de la excepción en la variable '$errorExcepcion'
                                $mensajeExcepcion = $miExcepcionPDO->getMessage(); // Almacenamos el mensaje de la excepción en la variable '$mensajeExcepcion'

                                echo "<span class='errorException'>Error: </span>" . $mensajeExcepcion . "<br>"; // Mostramos el mensaje de la excepción
                                echo "<span class='errorException'>Código del error: </span>" . $errorExcepcion; // Mostramos el código de la excepción
                        } finally {
                            unset($miDB); // Cerramos la conexión con la base de datos
                        }
                        ?>
                        <!DOCTYPE html>
<!--
        Descripción: CodigoPrograma
        Autor: Carlos García Cachón
        Fecha de creación/modificación: 02/11/2023
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
        <style>
            .obligatorio {
                background-color: #ffff7a;
            }
            .bloqueado:disabled {
                background-color: #665 ;
                color: white;
            }
            .error {
                color: red;
                width: 450px;
            }
            .errorException {
                color:#FF0000;
                font-weight:bold;
            }
            .respuestaCorrecta {
                color:#4CAF50;
                font-weight:bold;
            }
        </style>
    </head>

    <body>
        <header class="text-center">
            <h1>Proyecto LoginLogoffTema5:</h1>
        </header>
        <main>
            <div class="container mt-3">
                <div class="row d-flex justify-content-start">
                    <div class="col">
                        <form name="Programa" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            
                             <button type="submit" name="salir">Cerrar Sesión</button>
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
                        
                        echo ("<h5>Bienvenido ".$descUsuario." esta es la ".$nConexiones." vez que te conectas; "
                                . "usted se conectó por última vez el ".$fechaUltimaConexionUsuario."</h5>");
                        ?> 
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
                    <a href="../indexLoginLogoffTema5.html" style="color: white; text-decoration: none; background-color: #666"> Inicio</a>
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
