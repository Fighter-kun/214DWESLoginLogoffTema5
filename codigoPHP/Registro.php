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
    header('Location: ../indexLoginLogoffTema5.php'); // Llevo al usuario a la pagina 'index.LoginLogoffTema5.php'
    exit();
}
// Incluyo la librería de validación para comprobar los campos y el fichero de configuración de la BD
require_once '../core/231018libreriaValidacion.php';
require_once "../config/confDBPDO.php";
require_once '../config/configIdiomas.php'; // Incluimos el arrays con los mensajes según el idioma seleccionado
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
                        <?php
                        /**
                         * @author Carlos García Cachón
                         * @version 1.0
                         * @since 21/12/2023
                         */
                        // Incluyo la libreria de validación para comprobar los campos
                        require_once '../core/231018libreriaValidacion.php';
                        // Incluyo la configuración de conexión a la BD
                        require_once '../config/confDBPDO.php';

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
                                $aErrores['T01_Password'] = validacionFormularios::validarPassword($_REQUEST['T01_Password'], 20, 8, 1, OBLIGATORIO);
                                $aErrores['repetirPassword'] = validacionFormularios::validarPassword($_REQUEST['repetirPassword'], 20, 8, 1, OBLIGATORIO);

                                // Ahora validamos que el codigo introducido no exista en la BD, haciendo una consulta 
                                if ($aErrores['T01_CodUsuario'] == null) {

                                    // CONEXION BASE DE DATOS
                                    // Iniciamos la conexión con la BD
                                    $miDB = new PDO(DSN, USERNAME, PASSWORD);
                                    // CONSULTA
                                    // En esta línea utilizo 'quote()' se utiliza para escapar y citar el valor del $_REQUEST['T01_CodUsuario'], ayudando a prevenir la inyección de SQL.
                                    $codUsuario = $miDB->quote($_REQUEST['T01_CodUsuario']);
                                    // Utilizamos una consulta simple para comprobar el codigo del usuario
                                    $consultaComprobarCodUsuario = $miDB->query("SELECT T01_CodUsuario FROM T01_Usuario WHERE T01_CodUsuario = $codUsuario");
                                    // Y obtenemos el resultado de la consulta como un objeto.
                                    $usuarioExistente = $consultaComprobarCodUsuario->fetchObject();

                                    // COMPROBACION DE ERRORES
                                    if ($usuarioExistente) {
                                        $aErrores['CodDepartamento'] = "El usuario ya existe";
                                    }
                                    
                                    if ($aErrores['T01_Password'] == null && $aErrores['repetirPassword'] == null) {
                                        if ($aErrores['T01_Password'] != $aErrores['repetirPassword']) {
                                            $aErrores['T01_Password']  = "Las contraseñas no coinciden.";
                                            $aErrores['repetirPassword'] = "Las contraseñas no coinciden.";
                                        }
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
                            } else {
                                ?>
                                <!-- Codigo del formulario -->
                                <form name="insercionValoresTablaDepartamento" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                    <fieldset>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="rounded-top" colspan="3"><legend>Creación de Departamento</legend></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <!-- CodDepartamento Obligatorio -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="CodDepartamento">Codigo de Departamento:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input class="obligatorio d-flex justify-content-start" type="text" placeholder="AAD" name="CodDepartamento" value="<?php echo (isset($_REQUEST['CodDepartamento']) ? $_REQUEST['CodDepartamento'] : ''); ?>">
                                                    </td>
                                                    <td class="error">
        <?php
        if (!empty($aErrores['CodDepartamento'])) {
            echo $aErrores['CodDepartamento'];
        }
        ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <!-- DescDepartamento Obligatorio -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="DescDepartamento">Descripción de Departamento:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input class="obligatorio d-flex justify-content-start" type="text" name="DescDepartamento" placeholder="Departamento de Ventas" value="<?php echo (isset($_REQUEST['DescDepartamento']) ? $_REQUEST['DescDepartamento'] : ''); ?>">
                                                    </td>
                                                    <td class="error">
        <?php
        if (!empty($aErrores['DescDepartamento'])) {
            echo $aErrores['DescDepartamento'];
        }
        ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <!-- FechaCreacionDepartamento Opcional -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="FechaCreacionDepartamento">Fecha de Creación:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input disabled class="d-flex justify-content-start bloqueado" type="text" name="FechaCreacionDepartamento"  value="<?php echo ($fechaYHoraActualCreacionFormateada); ?>">
                                                    </td>
                                                    <td class="error">
        <?php
        if (!empty($aErrores['FechaCreacionDepartamento'])) {
            echo $aErrores['FechaCreacionDepartamento'];
        }
        ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <!-- VolumenNegocio Obligatorio -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="VolumenDeNegocio">Volumen de Negocio:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input class="obligatorio d-flex justify-content-start" type="text" name="VolumenDeNegocio" placeholder="1234.5" value="<?php echo (isset($_REQUEST['VolumenDeNegocio']) ? $_REQUEST['VolumenDeNegocio'] : ''); ?>">
                                                    </td>
                                                    <td class="error">
        <?php
        if (!empty($aErrores['VolumenDeNegocio'])) {
            echo $aErrores['VolumenDeNegocio'];
        }
        ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <!-- FechaBaja Opcional -->
                                                    <td class="d-flex justify-content-start">
                                                        <label for="FechaBaja">Fecha de Baja:</label>
                                                    </td>
                                                    <td>                                                                                                <!-- El value contiene una operador ternario en el que por medio de un metodo 'isset()'
                                                                                                                                                        comprobamos que exista la variable y no sea 'null'. En el caso verdadero devovleremos el contenido del campo
                                                                                                                                                        que contiene '$_REQUEST' , en caso falso sobrescribira el campo a '' .-->
                                                        <input disabled class="d-flex justify-content-start bloqueado" type="text" name="FechaBaja" placeholder="YYYY/mm/dd HH:ii:ss" value="<?php echo (isset($_REQUEST['FechaBaja']) ? $_REQUEST['FechaBaja'] : ''); ?>">
                                                    </td>
                                                    <td class="error">
        <?php
        if (!empty($aErrores['FechaBaja'])) {
            echo $aErrores['FechaBaja'];
        }
        ?> <!-- Aquí comprobamos que el campo del array '$aErrores' no esta vacío, si es así, mostramos el error. -->
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="submit" name="registrarse"><?php echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['resgistrarse'] ?></button>
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
                <a href="../214DWESProyectoDWES/indexProyectoDWES.html" style="color: white; text-decoration: none; background-color: #666"><?php  echo $aIdiomaSeleccionado[$_COOKIE['idioma']]['inicio']?></a>
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


