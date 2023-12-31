<?php
/**
 * @author Carlos García Cachón
 * @version 1.0
 * @since 21/12/2023
 * @copyright Todos los derechos reservados a Carlos García
 * 
 * @Annotation Proyecto LoginLogoffTema5 - Parte de 'editarPerfil' 
 * 
 */
// Recuperamos la sesión
session_start();

if (!isset($_SESSION['user214DWESLoginLogoffTema5'])) { // Si el usuario no se ha autentificado
    header('Location: login.php'); //Redirigimos a el usuario al login
    exit();
}

// Estructura del boton cancelar, si el ususario pulsa el botón
if (isset($_REQUEST['cancelar'])) {
    header('Location: programa.php'); // Llevo al usuario a la pagina 'programa.php'
    exit();
}

// Estructura del boton registrarse, si el ususario pulsa el botón
if (isset($_REQUEST['cambiarContraseña'])) {
    header('Location: cambiarPassword.php'); // Llevo al usuario a la pagina 'registro.php'
    exit();
}
// Incluyo la librería de validación para comprobar los campos y el fichero de configuración de la BD
require_once '../core/231018libreriaValidacion.php';
require_once "../config/confDBPDO.php";
require_once '../config/configIdiomas.php'; // Incluimos el arrays con los mensajes según el idioma seleccionado
// Declaracion de la variable de confirmación de envio de formulario correcto
$entradaOK = true;

// Declaramos el array de errores y lo inicializamos vacío
$aErrores = ['T01_DescUsuario' => ""];
$aRespuestas = ['T01_DescUsuario' => ""];

// Bloque para recoger datos que mostramos en el formulario
try {
    $miDB = new PDO(DSN, USERNAME, PASSWORD); // Instanciamos un objeto PDO y establecemos la conexión
    // CONSULTA
    // Hacemos un 'SELECT' sobre la tabla 'T01_Usuario' para recuperar toda la información del usuario en curso
    $sqlUsuario = $miDB->prepare("SELECT * FROM T01_Usuario WHERE T01_CodUsuario = '" . $_SESSION['user214DWESLoginLogoffTema5'] . "';");

    $sqlUsuario->execute(); //Ejecuto la consulta con el array de parametrosMostrar
    $oUsuarioAEditar = $sqlUsuario->fetchObject(); //Obtengo un objeto con el usuario
    // Almaceno la información del usuario actual en las siguiente variables, para mostrarlas en el formulario
    $descripcionUsuarioAEditar = $oUsuarioAEditar->T01_DescUsuario;
    $nConexionesUsuarioAEditar = $oUsuarioAEditar->T01_NumConexiones;
    $fechaHoraUltimaConexionAnteriorUsuarioAEditar = $oUsuarioAEditar->T01_FechaHoraUltimaConexion;
    $passwordUsuarioAEditar = $oUsuarioAEditar->T01_Password;
} catch (PDOException $miExcepcionPDO) {
    $errorExcepcion = $miExcepcionPDO->getCode(); // Almacenamos el código del error de la excepción en la variable '$errorExcepcion'
    $mensajeExcepcion = $miExcepcionPDO->getMessage(); // Almacenamos el mensaje de la excepción en la variable '$mensajeExcepcion'

    echo "<span class='errorException'>Error: </span>" . $mensajeExcepcion . "<br>"; // Mostramos el mensaje de la excepción
    echo "<span class='errorException'>Código del error: </span>" . $errorExcepcion; // Mostramos el código de la excepción
}

if (isset($_REQUEST['eliminarUsuario'])) { // Comprobamos que el usuario a pulsado el boton 'Eliminar Usuario'
    $sqlDelete = $miDB->prepare("DELETE FROM T01_Usuario WHERE T01_CodUsuario = '" . $_SESSION['user214DWESLoginLogoffTema5'] . "';"); // Preparo la consulta antes de ejecutarla
    $sqlDelete->execute(); // Ejecuto la consulta de borrado

    session_destroy(); // Elimino la sesion
    header('Location: ../indexLoginLogoffTema5.php'); // Llevo al usuario a la pagina 'index.loginLogoffTema5.php'
    exit();
}

if (isset($_REQUEST['confirmarCambios'])) { // Comprobamos que el usuario haya enviado el formulario para 'confirmar los cambios'
    $aErrores['T01_DescUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['T01_DescUsuario'], 255, 3, 0);

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
    try {
        // CONSULTA
        // Usamos un 'UPDATE' para aplicar los cambios de la nueva descripción al usuario actual
        $sqlUpdateUsuario = $miDB->prepare("UPDATE T01_Usuario SET T01_DescUsuario = '" . $_REQUEST['T01_DescUsuario'] . "' WHERE T01_CodUsuario = '" . $_SESSION['user214DWESLoginLogoffTema5'] . "';");
        $sqlUpdateUsuario->execute();

        $_SESSION['DescripcionUsuario'] = $_REQUEST['T01_DescUsuario']; // Cargo la nueva descripción antes de volver a 'programa.php'

        header('Location: programa.php'); // Llevo al usuario a la pagina 'programa.php'
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
    <!DOCTYPE html>
    <!--
            Descripción: CodigoEditarPerfil
            Autor: Carlos García Cachón
            Fecha de creación/modificación: 21/22/2023
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
                .btn-danger {
                    background-color: red;
                }
            </style>
        </head>

        <body>
            <header class="text-center">
                <h1><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['titulo'] ?> LoginLogoffTema5:</h1>
            </header>
            <main>
                <div class="container mt-3">
                    <div class="row d-flex justify-content-start">
                        <div class="col">
                            <!-- Codigo del formulario -->
                            <form name="controlAcceso" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <fieldset>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="rounded-top" colspan="3"><legend><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['editarPerfil'] ?></legend></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <!-- Usuario deshabilitado -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="user"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['usuario'] ?>:</label>
                                                </td>
                                                <td>
                                                    <input class="bloqueado d-flex justify-content-start" type="text" name="user"
                                                           value="<?php echo ($_SESSION['user214DWESLoginLogoffTema5']); ?>" disabled>
                                                </td>
                                                <td class="error">
                                                </td>
                                            </tr>
                                            <tr>
                                                <!-- Contraseña deshabilitado -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="passwordUsuarioAEditar"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['contraseña'] ?>:</label>
                                                </td>
                                                <td>
                                                    <input class="bloqueado d-flex justify-content-start" type="password" name="passwordUsuarioAEditar"
                                                           value="<?php echo ($passwordUsuarioAEditar); ?>" disabled>
                                                </td>
                                                <td class="error">
                                                </td>
                                            </tr>
                                            <tr>
                                                <!-- descripcionUsuarioAEditar Opcional -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="T01_DescUsuario"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['descUsuario'] ?>:</label>
                                                </td>
                                                <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                    comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                    que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                    <input class="d-flex justify-content-start" type="text" name="T01_DescUsuario" value="<?php echo (isset($_REQUEST['T01_DescUsuario']) ? $_REQUEST['T01_DescUsuario'] : $descripcionUsuarioAEditar); ?>">
                                                </td>
                                                <td class="error">
                                                    <?php
                                                    if (!empty($aErrores['T01_DescUsuario'])) {
                                                        echo $aErrores['T01_DescUsuario'];
                                                    }
                                                    ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <!-- nConexionesUsuarioAEditar deshabilitado -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="nConexionesUsuarioAEditar"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['numeroDeConexiones'] ?>:</label>
                                                </td>
                                                <td>
                                                    <input class="bloqueado d-flex justify-content-start" type="text" name="nConexionesUsuarioAEditar"
                                                           value="<?php echo ($nConexionesUsuarioAEditar); ?>" disabled>
                                                </td>
                                                <td class="error">
                                                </td>
                                            </tr>
                                            <?php
                                            if ($nConexionesUsuarioAEditar > 1) {
                                                echo "<tr>
                                                    <!-- fechaHoraUltimaConexionAnteriorUsuarioAEditar deshabilitado -->
                                                    <td class=\"d-flex justify-content-start\">
                                                        <label for=\"fechaHoraUltimaConexionAnteriorUsuarioAEditar\">".$aIdiomaSeleccionado[$_COOKIE['idioma']]['fechaYHoraUltimaConexion'].":</label>
                                                    </td>
                                                    <td>
                                                        <input class=\"bloqueado d-flex justify-content-start\" type=\"text\" name=\"fechaHoraUltimaConexionAnteriorUsuarioAEditar\"
                                                            value=\"$fechaHoraUltimaConexionAnteriorUsuarioAEditar\" disabled>
                                                    </td>
                                                    <td class=\"error\">
                                                    </td>
                                                </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <div class="text-center">
                                        <button class="btn btn-secondary" aria-disabled="true" type="submit" name="cambiarContraseña"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['cambiarContraseña'] ?></button>
                                        <button class="btn btn-secondary" aria-disabled="true" type="submit" name="confirmarCambios"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['confirmarCambios'] ?></button>
                                        <button class="btn btn-secondary" aria-disabled="true" type="submit" name="cancelar"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['botonCancelar'] ?></button>
                                        <button class="btn btn-danger" aria-disabled="true" type="submit" name="eliminarUsuario"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['eliminarUsuario'] ?></button>
                                    </div>
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
                <a href="../indexLoginLogoffTema5.php" style="color: white; text-decoration: none; background-color: #666"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['inicio'] ?></a>
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
