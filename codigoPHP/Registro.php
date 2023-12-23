<?php
/**
 * @author Carlos García Cachón
 * @version 1.0
 * @since 21/12/2023
 * @copyright Todos los derechos reservados a Carlos García
 * 
 * @Annotation Proyecto LoginLogoffTema5 - Parte de 'Registro' 
 * 
 */
// Estructura del boton cancelar, si el ususario pulsa el botón
if (isset($_REQUEST['cancelar'])) {
    header('Location: login.php'); // Llevo al usuario a la pagina 'login.php'
    exit();
}
// Incluyo la librería de validación para comprobar los campos y el fichero de configuración de la BD
require_once '../core/231018libreriaValidacion.php';
require_once "../config/confDBPDO.php";
require_once '../config/configIdiomas.php'; // Incluimos el arrays con los mensajes según el idioma seleccionado
// Declaración de constantes por OBLIGATORIEDAD
define('OPCIONAL', 0);
define('OBLIGATORIO', 1);

// Declaración de variables de estructura para validar la ENTRADA de RESPUESTAS o ERRORES
// Valores por defecto
$entradaOK = true;

$aRespuestas = [
    'T01_CodUsuario' => "",
    'T01_DescUsuario' => "",
    'T01_Password' => "",
    'repetirPassword' => ""
];

$aErrores = [
    'T01_CodUsuario' => "",
    'T01_DescUsuario' => "",
    'T01_Password' => "",
    'repetirPassword' => ""
];
try {
    //En el siguiente if pregunto si el '$_REQUEST' recupero el valor 'registrarse' que enviamos al pulsar el boton de registrarse del formulario.
    if (isset($_REQUEST['registrarse'])) {
        /*
         * Ahora inicializo cada 'key' del ARRAY utilizando las funciónes de la clase de 'validacionFormularios' , la cuál 
         * comprueba el valor recibido (en este caso el que recibe la variable '$_REQUEST') y devuelve 'null' si el valor es correcto,
         * o un mensaje de error personalizado por cada función dependiendo de lo que validemos.
         */
        //Introducimos valores en el array $aErrores si ocurre un error
        $aErrores['T01_CodUsuario'] = validacionFormularios::comprobarAlfabetico($_REQUEST['T01_CodUsuario'], 8, 3, OBLIGATORIO);
        $aErrores['T01_DescUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['T01_DescUsuario'], 255, 3, OBLIGATORIO);
        $aErrores['T01_Password'] = validacionFormularios::validarPassword($_REQUEST['T01_Password'], 8, 3, 1, OBLIGATORIO);
        $aErrores['repetirPassword'] = validacionFormularios::validarPassword($_REQUEST['repetirPassword'], 8, 3, 1, OBLIGATORIO);

        // Ahora validamos que el codigo introducido no exista en la BD, haciendo una consulta 
        if ($aErrores['T01_CodUsuario'] == null) {
            // Caso de que las dos contraseñas validen
            if ($aErrores['T01_Password'] == null && $aErrores['repetirPassword'] == null) {
                // Caso de que las contraseñas sean distintas
                if ($aErrores['T01_Password'] != $aErrores['repetirPassword']) {
                    $aErrores['T01_Password'] = "Las contraseñas no coinciden.";
                    $aErrores['repetirPassword'] = "Las contraseñas no coinciden.";
                }
            }

            // CONEXION BASE DE DATOS
            // Iniciamos la conexión con la BD
            $miDB = new PDO(DSN, USERNAME, PASSWORD);
            // CONSULTA
            // Utilizamos una consulta simple para comprobar el codigo del usuario
            $consultaComprobarCodUsuario = $miDB->prepare("SELECT * FROM T01_Usuario WHERE T01_CodUsuario = ?");
            $consultaComprobarCodUsuario->execute([$_REQUEST['T01_CodUsuario']]);
            // Y obtenemos el resultado de la consulta como un objeto.
            $oUsuarioExistente = $consultaComprobarCodUsuario->fetchObject();
            // COMPROBACION DE ERRORES
            // Caso de que el usuario exista
            if ($oUsuarioExistente) {
                $aErrores['T01_CodUsuario'] = "El usuario ya existe";
            }
        }
        /*
         * En este foreach recorremos el array buscando que exista NULL (Los metodos anteriores si son correctos devuelven NULL)
         * y en caso negativo cambiara el valor de '$entradaOK' a false y borrara el contenido del campo.
         */
        foreach ($aErrores as $campo => $error) {
            if ($error != null) {
                $_REQUEST[$campo] = "";
                $entradaOK = false;
            }
        }
    } else {
        $entradaOK = false;
    }
    //En caso de que '$entradaOK' sea true, cargamos las respuestas en el array '$aRespuestas'
    if ($entradaOK) {
        $aRespuestas['T01_CodUsuario'] = $_REQUEST['T01_CodUsuario'];
        $aRespuestas['T01_DescUsuario'] = $_REQUEST['T01_DescUsuario'];
        $aRespuestas['T01_Password'] = $_REQUEST['T01_Password'];
        $aRespuestas['repetirPassword'] = $_REQUEST['repetirPassword'];

        // Y hacemos un 'INSERT' en la tabla para añadir al usuario
        // Antes de hacer la consulta hacemos el 'hash' de la contraseña, que es el 'codUsuario' más la 'Password'
        $hashPassword = hash("sha256", ($aRespuestas['T01_CodUsuario'] . $aRespuestas['T01_Password']));
        //CONSULTA
        $insertNuevoUsuario = "INSERT INTO T01_Usuario (T01_CodUsuario, T01_Password, T01_DescUsuario) VALUES ('"
                . $aRespuestas['T01_CodUsuario'] . "','" . $hashPassword . "','" . $aRespuestas['T01_DescUsuario'] . "');";
        $resultadoInsertNuevoUsuario = $miDB->prepare($insertNuevoUsuario); // La preparamos por seguridad
        $resultadoInsertNuevoUsuario->execute(); // Ejecuto la consulta 

        session_start(); // Iniciamos la sesión
        // Se almacena en una variable de sesión el codigo del usuario
        $_SESSION['user214DWESLoginLogoffTema5'] = $aRespuestas['T01_CodUsuario'];
        $_SESSION['DescripcionUsuario'] = $aRespuestas['T01_DescUsuario'];
        $_SESSION['NumeroConexiones'] = 1;
        // CONSULTA
        // Hacemos un 'UPDATE' para asignarle su primera conexión y fecha y hora de última conexión, ya que no pasa por 'login.php'
        $consultaUpdate = $miDB->prepare('UPDATE T01_Usuario SET T01_NumConexiones =' . 1 . ', T01_FechaHoraUltimaConexion=now() WHERE T01_CodUsuario="' . $aRespuestas['T01_CodUsuario'] . '";'); // Preparamos la consulta
        $consultaUpdate->execute(); // Pasamos los parámetros a la consulta

        header('Location: programa.php'); // Llevo al usuario a la pagina 'programa.php'
        exit;
    } else {
        ?>
        <!DOCTYPE html>
        <!--
                Descripción: CodigoRegistro
                Autor: Carlos García Cachón
                Fecha de creación/modificación: 21/12/2023
        -->
        <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="author" content="Carlos García Cachón">
                <meta name="description" content="CodigoEjercicio3PDO">
                <meta name="keywords" content="CodigoEjercicio, 3PDO">
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
                </style>
            </head>

            <body>
                <header class="text-center">
                    <h1><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['titulo'] ?> LoginLogoffTema5:</h1>
                </header>
                <main>
                    <div class="container mt-3">
                        <div class="row text-center">
                            <div class="col">
                                <!-- Codigo del formulario -->
                                <form name="insercionValoresTablaDepartamento" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                    <fieldset>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="rounded-top" colspan="3"><legend>Registro</legend></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <!-- T01_CodUsuario Obligatorio -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="T01_CodUsuario">Usuario:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input class="obligatorio d-flex justify-content-start" type="text" name="T01_CodUsuario" value="<?php echo (isset($_REQUEST['T01_CodUsuario']) ? $_REQUEST['T01_CodUsuario'] : ''); ?>">
                                                    </td>
                                                    <td class="error">
                                                        <?php
                                                        if (!empty($aErrores['T01_CodUsuario'])) {
                                                            echo $aErrores['T01_CodUsuario'];
                                                        }
                                                        ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <!-- T01_DescUsuario Obligatorio -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="T01_DescUsuario">Descripcion del Usuario:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input class="obligatorio d-flex justify-content-start" type="text" name="T01_DescUsuario" value="<?php echo (isset($_REQUEST['T01_DescUsuario']) ? $_REQUEST['T01_DescUsuario'] : ''); ?>">
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
                                                    <!-- T01_Password Obligatorio -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="T01_Password">Contraseña:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input class="obligatorio d-flex justify-content-start" type="password" name="T01_Password" value="<?php echo (isset($_REQUEST['T01_Password']) ? $_REQUEST['T01_Password'] : ''); ?>">
                                                    </td>
                                                    <td class="error">
                                                        <?php
                                                        if (!empty($aErrores['T01_Password'])) {
                                                            echo $aErrores['T01_Password'];
                                                        }
                                                        ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <!-- repetirPassword Obligatorio -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="T01_Password">Repetir Contraseña:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input class="obligatorio d-flex justify-content-start" type="password" name="repetirPassword" value="<?php echo (isset($_REQUEST['repetirPassword']) ? $_REQUEST['repetirPassword'] : ''); ?>">
                                                    </td>
                                                    <td class="error">
                                                        <?php
                                                        if (!empty($aErrores['repetirPassword'])) {
                                                            echo $aErrores['repetirPassword'];
                                                        }
                                                        ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button class="btn btn-secondary" aria-disabled="true" type="submit" name="registrarse"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['registrarse'] ?></button>
                                        <button class="btn btn-secondary" aria-disabled="true" type="submit" name="cancelar"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['botonCancelar'] ?></button>
                                    </fieldset>
                                </form>
                                <?php
                            }
                        } catch (PDOException $miExcepcionPDO) {
                            $errorExcepcion = $miExcepcionPDO->getCode(); // Almacenamos el código del error de la excepción en la variable '$errorExcepcion'
                            $mensajeExcepcion = $miExcepcionPDO->getMessage(); // Almacenamos el mensaje de la excepción en la variable '$mensajeExcepcion'

                            echo "<span style='color: red;'>Error: </span>" . $mensajeExcepcion . "<br>"; //Mostramos el mensaje de la excepción
                            echo "<span style='color: red;'>Código del error: </span>" . $errorExcepcion; //Mostramos el código de la excepción
                        } finally {
                            unset($miDB); //Para cerrar la conexión
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
                <a href="https://github.com/Fighter-kun?tab=repositories" target="_blank"><img
                        src="../webroot/media/images/github.png" alt="LogoGitHub" class="pe-5"/></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
</body>

</html>


