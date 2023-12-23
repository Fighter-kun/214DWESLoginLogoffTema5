<?php
/**
 * @author Carlos García Cachón
 * @version 1.0
 * @since 23/12/2023
 * @copyright Todos los derechos reservados a Carlos García
 * 
 * @Annotation Proyecto LoginLogoffTema5 - Parte de 'cambiarPassword' 
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
    header('Location: editarPerfil.php'); // Llevo al usuario a la pagina 'editarPerfil.php'
    exit();
}

// Incluyo la librería de validación para comprobar los campos y el fichero de configuración de la BD
require_once '../core/231018libreriaValidacion.php';
require_once "../config/confDBPDO.php";
require_once '../config/configIdiomas.php'; // Incluimos el arrays con los mensajes según el idioma seleccionado
// Declaracion de la variable de confirmación de envio de formulario correcto
$entradaOK = true;

// Declaramos el array de errores y lo inicializamos vacío
$aErrores = ['contraseñaActual' => ""];
$aErrores = ['nuevaContraseña' => ""];
$aErrores = ['repetirNuevaContraseña' => ""];

// Declaramos el array de respuestas y lo inicializamos vacío
$aRespuestas = ['contraseñaActual' => ""];
$aRespuestas = ['nuevaContraseña' => ""];
$aRespuestas = ['repetirNuevaContraseña' => ""];
// Bloque para recoger datos que mostramos en el formulario
if (isset($_REQUEST['confirmarCambios'])) { // Comprobamos que el usuario haya enviado el formulario para 'confirmar los cambios'
    $aErrores['contraseñaActual'] = validacionFormularios::validarPassword($_REQUEST['contraseñaActual'], 8, 3, 1, 1);
    $aErrores['nuevaContraseña'] = validacionFormularios::validarPassword($_REQUEST['nuevaContraseña'], 8, 3, 1, 1);
    $aErrores['repetirNuevaContraseña'] = validacionFormularios::validarPassword($_REQUEST['repetirNuevaContraseña'], 8, 3, 1, 1);

    try {
        // CONEXION BASE DE DATOS
        // Iniciamos la conexión con la BD
        $miDB = new PDO(DSN, USERNAME, PASSWORD);
        // CONSULTA
        // Utilizamos una consulta preparada para comprobar que  'contraseñaActual' es realmente la contraseña del usuario actual
        $hashPassword = hash("sha256", ($_SESSION['user214DWESLoginLogoffTema5'] . $_REQUEST['contraseñaActual'])); // Obtenemos el 'hash' de la "contraseña actual" segun los valores
        $consultaComprobarContraseñaUsuario = $miDB->prepare("SELECT T01_CodUsuario FROM T01_Usuario WHERE T01_CodUsuario = :usuario AND T01_Password = :hashPassword");
$consultaComprobarContraseñaUsuario->bindParam(':usuario', $_SESSION['user214DWESLoginLogoffTema5'], PDO::PARAM_STR);
$consultaComprobarContraseñaUsuario->bindParam(':hashPassword', $hashPassword, PDO::PARAM_STR);
$consultaComprobarContraseñaUsuario->execute();

$oContraseñaUsuario = $consultaComprobarContraseñaUsuario->fetchObject();

if (!$oContraseñaUsuario) {
    $aErrores['contraseñaActual'] = "Contraseña incorrecta";
}

        if ($_REQUEST['nuevaContraseña'] != $_REQUEST['repetirNuevaContraseña']) {
            $aErrores['nuevaContraseña'] = "No coinciden las contraseñas";
            $aErrores['repetirNuevaContraseña'] = "No coinciden las contraseñas";
        }
    } catch (PDOException $miExcepcionPDO) {
        $errorExcepcion = $miExcepcionPDO->getCode(); // Almacenamos el código del error de la excepción en la variable '$errorExcepcion'
        $mensajeExcepcion = $miExcepcionPDO->getMessage(); // Almacenamos el mensaje de la excepción en la variable '$mensajeExcepcion'

        echo "<span style='color: red;'>Error: </span>" . $mensajeExcepcion . "<br>"; //Mostramos el mensaje de la excepción
        echo "<span style='color: red;'>Código del error: </span>" . $errorExcepcion; //Mostramos el código de la excepción
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
    try {
        // CONSULTA
        // Obtenemos el 'hash' de la nueva contraseña para realizar el UPDATE
        $hashPassword = hash("sha256", ($_SESSION['user214DWESLoginLogoffTema5'] . $_REQUEST['nuevaContraseña']));
        // Usamos un 'UPDATE' para aplicar los cambios de la nueva contraseña al usuario actual
        $sqlUpdateUsuario = $miDB->prepare("UPDATE T01_Usuario SET T01_Password = '" . $hashPassword . "' WHERE T01_CodUsuario = '" . $_SESSION['user214DWESLoginLogoffTema5'] . "';"); // Preparamos la consulta
        $sqlUpdateUsuario->execute(); // Ejecutamos la consulta


        header('Location: editarPerfil.php'); // Llevo al usuario a la pagina 'editarPerfil.php'
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
                                                <th class="rounded-top" colspan="3"><legend>Editar Perfil</legend></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <!-- contraseñaActual Obligatorio -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="contraseñaActual">Contraseña Actual:</label>
                                                </td>
                                                <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                    comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                    que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                    <input class="obligatorio d-flex justify-content-start" type="password" name="contraseñaActual" value="<?php echo (isset($_REQUEST['contraseñaActual']) ? $_REQUEST['contraseñaActual'] : ''); ?>">
                                                </td>
                                                <td class="error">
                                                    <?php
                                                    if (!empty($aErrores['contraseñaActual'])) {
                                                        echo $aErrores['contraseñaActual'];
                                                    }
                                                    ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <!-- nuevaContraseña Obligatorio -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="nuevaContraseña">Nueva Contraseña:</label>
                                                </td>
                                                <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                    comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                    que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                    <input class="obligatorio d-flex justify-content-start" type="password" name="nuevaContraseña" value="<?php echo (isset($_REQUEST['nuevaContraseña']) ? $_REQUEST['nuevaContraseña'] : ''); ?>">
                                                </td>
                                                <td class="error">
                                                    <?php
                                                    if (!empty($aErrores['nuevaContraseña'])) {
                                                        echo $aErrores['nuevaContraseña'];
                                                    }
                                                    ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <!-- repetirNuevaContraseña Obligatorio -->
                                                <td class="d-flex justify-content-start">
                                                    <label for="repetirNuevaContraseña">Repetir Nueva Contraseña:</label>
                                                </td>
                                                <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                    comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                    que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                    <input class="obligatorio d-flex justify-content-start" type="password" name="repetirNuevaContraseña" value="<?php echo (isset($_REQUEST['repetirNuevaContraseña']) ? $_REQUEST['repetirNuevaContraseña'] : ''); ?>">
                                                </td>
                                                <td class="error">
                                                    <?php
                                                    if (!empty($aErrores['repetirNuevaContraseña'])) {
                                                        echo $aErrores['repetirNuevaContraseña'];
                                                    }
                                                    ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-center">
                                        <button class="btn btn-secondary" aria-disabled="true" type="submit" name="confirmarCambios">Confirmar Cambios</button>
                                        <button class="btn btn-secondary" aria-disabled="true" type="submit" name="cancelar">Cancelar</button>
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
