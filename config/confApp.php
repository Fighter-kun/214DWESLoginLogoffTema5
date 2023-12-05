<?php
/**
 * @author Carlos García Cachón
 * @version 1.0
 * @since 05/12/2023
*/
function redireccionarAPrograma() {
    if ($_SERVER['SERVER_NAME'] == 'daw214.isauces.local') {
        header('Location: Programa.php');
        exit(); // Asegura que la redirección se realice de inmediato
    } elseif ($_SERVER['SERVER_NAME'] == 'daw214.ieslossauces.es') {
        echo '<meta http-equiv="refresh" content="0;url=Programa.php">';
        exit();
    }
}

function redireccionarADetalle() {
    if ($_SERVER['SERVER_NAME'] == 'daw214.isauces.local') {
        header('Location: Detalle.php');
        exit(); // Asegura que la redirección se realice de inmediato
    } elseif ($_SERVER['SERVER_NAME'] == 'daw214.ieslossauces.es') {
        echo '<meta http-equiv="refresh" content="0;url=Detalle.php">';
        exit();
    }
}

function redireccionarAIndex() {
    if ($_SERVER['SERVER_NAME'] == 'daw214.isauces.local') {
        header('Location: ../indexLoginLogoffTema5.html');
        exit(); // Asegura que la redirección se realice de inmediato
    } elseif ($_SERVER['SERVER_NAME'] == 'daw214.ieslossauces.es') {
        echo '<meta http-equiv="refresh" content="0;url=../indexLoginLogoffTema5.html">';
        exit();
    }
}
