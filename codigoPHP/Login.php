<!DOCTYPE html>
<!--
        Descripción: CodigoLogin
        Autor: Carlos García Cachón
        Fecha de creación/modificación: 02/11/2023
-->
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Carlos García Cachón">
        <meta name="description" content="CodigoLogin">
        <meta name="keywords" content="CodigoLogin">
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
                        <?php
                        /**
                        * @author Carlos García Cachón
                        * @version 1.0
                        * @since 28/11/2023
                        * @copyright Todos los derechos reservados a Carlos García
                        * 
                        * @Annotation Proyecto LoginLogoffTema5 - Parte de 'Login' 
                        * 
                        */
                        // Incluyo la librería de validación para comprobar los campos y el fichero de configuración de la BD
                        require_once '../core/231018libreriaValidacion.php';
                        require_once "../config/confDB.php";
                        //declaracion de variables universales
                        define("OBLIGATORIO", 1);
                        define("OPCIONAL", 0);
                        $entradaOK = true;

                        // Declaramos el array de errores y lo inicializamos a null
                        $aErrores = ['user' => null,
                            'password' => null];

                        if (isset($_REQUEST['Login'])) { // Comprobamos que el usuario haya enviado el formulario
                            $aErrores['user'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['user'], 15, 3, OBLIGATORIO);
                            $aErrores['password'] = validacionFormularios::validarpassword($_REQUEST['password'], 8, 3, 1, OBLIGATORIO);
                            try {// validamos que el nombre de usuario 'user' sea correcto
                                $miDB = new PDO(DSN, USERNAME, PASSWORD); // Instanciamos un objeto PDO y establecemos la conexión

                                $sqlUsuario = "SELECT T01_Password FROM T01_Usuario WHERE T01_CodUsuario=:user";
                                $consultaUsuario = $miDB->prepare($sqlUsuario); //Preparamos la consulta
                                $parametrosUsuario = [":user" => $_REQUEST['user']];

                                $consultaUsuario->execute($parametrosUsuario); // Pasamos los parámetros a la consulta
                                $registro = $consultaUsuario->fetchObject();

                                if ($consultaUsuario->rowCount() > 0) {// Si la consulta devuelve algun registro el codigo del usuario es correcto
                                    $passwordEncriptado = hash("sha256", ($_REQUEST['user'] . $_REQUEST['password']));
                                    if ($passwordEncriptado != $registro->T01_Password) {// Comprobamos que la contraseña sea correcta
                                        $aErrores['user'] = "Error autentificación"; // Si la contraseña no es correcta guardamos un mensaje de error en el array de errores
                                        $aErrores['password'] = "Error autentificación"; // Si la contraseña no es correcta guardamos un mensaje de error en el array de errores
                                    }
                                } else {//Si la consulta no devuelve ningun registro el codigo del usuario no es correcto
                                    $aErrores['user'] = "Error autentificación"; // Almacenamos un mensaje de error en el array de errores
                                    $aErrores['password'] = "Error autentificación"; // Almacenamos un mensaje de error en el array de errores
                                }
                            } catch (PDOException $miExcepcionPDO) {
                                $errorExcepcion = $miExcepcionPDO->getCode(); // Almacenamos el código del error de la excepción en la variable '$errorExcepcion'
                                $mensajeExcepcion = $miExcepcionPDO->getMessage(); // Almacenamos el mensaje de la excepción en la variable '$mensajeExcepcion'

                                echo "<span class='errorException'>Error: </span>" . $mensajeExcepcion . "<br>"; // Mostramos el mensaje de la excepción
                                echo "<span class='errorException'>Código del error: </span>" . $errorExcepcion; // Mostramos el código de la excepción
                            } finally {
                                unset($miDB); // Cerramos la conexión con la base de datos
                            }

                            // Recorremos el array de errores
                            foreach ($aErrores as $campo => $error) {
                                if ($error != null) { // Comprobamos que el campo no esté vacio
                                    $entradaOK = false; // En caso de que haya algún error le asignamos a entradaOK el valor false para que vuelva a rellenar el formulario
                                    $_REQUEST[$campo] = ""; // Limpiamos los campos del formulario
                                }
                            }
                        } else {
                            $entradaOK = false; // Si el usuario no ha enviado el formulario asignamos a entradaOK el valor false para que rellene el formulario
                        }
                        if ($entradaOK) { // Si el usuario ha rellenado el formulario correctamente rellenamos el array aFormulario con las respuestas introducidas por el usuario
                            try {// validamos que el nombre de usuario 'user' sea correcto
                                $miDB = new PDO(DSN, USERNAME, PASSWORD); // Instanciamos un objeto PDO y establecemos la conexión

                                $sql = "SELECT T01_NumConexiones, T01_FechaHoraUltimaConexion FROM T01_Usuario WHERE T01_CodUsuario=:user";
                                $consulta = $miDB->prepare($sql); // Preparamos la consulta
                                $parametros = [":user" => $_REQUEST['user']];

                                $consulta->execute($parametros); // Ejecutamos la consulta
                                $registro = $consulta->fetchObject(); // Obtenemos el primer registro de la consulta

                                $nConexiones = $registro->T01_NumConexiones; // Almacenamos el numero de conexiones almacenado en la base de datos
                                $fechaHoraUltimaConexion = $registro->T01_FechaHoraUltimaConexion; // Almacenamos la fecha hora de la ultima conexion almacenada en la base de datos

                                settype($nConexiones, "integer"); // Convertimos en entero el numero de conexiones devuelto por la consulta

                                $sqlUpdate = "UPDATE T01_Usuario SET T01_NumConexiones = :NumConexiones, T01_FechaHoraUltimaConexion=:FechaHoraUltimaConexion WHERE T01_CodUsuario=:user";
                                $consultaUpdate = $miDB->prepare($sqlUpdate); // Preparamos la consulta
                                $parametrosUpdate = [":NumConexiones" => ($nConexiones + 1),
                                    ":FechaHoraUltimaConexion" => date('Y-m-d H:i:s', time()),
                                    ":user" => $_REQUEST['user']];
                                $consultaUpdate->execute($parametrosUpdate); // Pasamos los parámetros a la consulta

                                session_start(); // Iniciamos la sesión
                                $_SESSION['user214DWESLoginLogoffTema5'] = $_REQUEST['user']; // Almacenamos en una variable de sesión el codigo del usuario
                                $_SESSION['FechaHoraUltimaConexionAnterior'] = $fechaHoraUltimaConexion; // Almacenamos la fecha hora de la última conexión en una variable de sesión
                                header('Location: Programa.php'); // Llevo al usuario a la pagina 'Programa.php'
                                exit();
                            } catch (PDOException $miExcepcionPDO) {
                                $errorExcepcion = $miExcepcionPDO->getCode(); // Almacenamos el código del error de la excepción en la variable '$errorExcepcion'
                                $mensajeExcepcion = $miExcepcionPDO->getMessage(); // Almacenamos el mensaje de la excepción en la variable '$mensajeExcepcion'

                                echo "<span class='errorException'>Error: </span>" . $mensajeExcepcion . "<br>"; // Mostramos el mensaje de la excepción
                                echo "<span class='errorException'>Código del error: </span>" . $errorExcepcion; // Mostramos el código de la excepción
                            } finally {
                                unset($miDB); //Cerramos la conexión con la base de datos
                            }
                        } else {// Si el usuario no ha rellenado el formulario correctamente volverá a rellenarlo
                            ?>
                            <!-- Codigo del formulario -->
                            <form name="controlAcceso" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <fieldset>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="rounded-top" colspan="3"><legend>Iniciar Sesión</legend></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <!-- CodDepartamento Obligatorio -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="user">Introduce usuario:</label>
                                                </td>
                                                <td>
                                                    <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                         comprobamos que exista la variable y no sea 'null'. En el caso verdadero devolveremos el contenido del campo
                                                         que contiene '$_REQUEST' , en caso falso sobrescribirá el campo a '' .-->
                                                    <input class="obligatorio d-flex justify-content-start" type="text" name="user"
                                                           value="<?php echo (isset($_REQUEST['user']) ? $_REQUEST['user'] : ''); ?>">
                                                </td>
                                                <td class="error">
                                                    <?php
                                                    if (!empty($aErrores['user'])) {
                                                        echo $aErrores['user'];
                                                    }
                                                    ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no está vacío, si es así, mostramos el error. -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <!-- CodDepartamento Obligatorio -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="password">Introduce contraseña:</label>
                                                </td>
                                                <td>
                                                    <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                         comprobamos que exista la variable y no sea 'null'. En el caso verdadero devolveremos el contenido del campo
                                                         que contiene '$_REQUEST' , en caso falso sobrescribirá el campo a '' .-->
                                                    <input class="obligatorio d-flex justify-content-start" type="password" name="password"
                                                           value="<?php echo (isset($_REQUEST['password']) ? $_REQUEST['password'] : ''); ?>">
                                                </td>
                                                <td class="error">
                                                    <?php
                                                    if (!empty($aErrores['password'])) {
                                                        echo $aErrores['password'];
                                                    }
                                                    ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no está vacío, si es así, mostramos el error. -->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="submit" name="Login">Iniciar Sesión</button>
                                </fieldset>
                            </form>
                            <?php
                        }
                        ?>
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