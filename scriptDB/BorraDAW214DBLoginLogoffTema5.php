<?php
// Configuración de conexión con la base de datos
require_once '../config/confDB.php';

try {
    // Crear conexión
    $conn = new PDO(DSN, USERNAME, PASSWORD);

    // Utilizar la base de datos 
    $query1 = "USE dbs12302455;";

    // Elimino la tabla T02_Departamento
    $query2 = "DROP TABLE T02_Departamento";
    
    // Elimino la tabla T01_Usuario 
    $query3 = "DROP TABLE T01_Usuario";

    // Ejecutar consultas SQL
    $sql_queries = [$query1, $query2, $query3];

    foreach ($sql_queries as $query) {
        if ($conn->query($query) === FALSE) {
            throw new Exception("Error al ejecutar la consulta: $query - " . $conn->error);
        }
        echo "Consulta ejecutada con éxito: $query<br>";
    }
} catch (PDOException $miExcepcionPDO) {
    $errorExcepcion = $miExcepcionPDO->getCode(); // Almacenamos el código del error de la excepción en la variable '$errorExcepcion'
    $mensajeExcepcion = $miExcepcionPDO->getMessage(); // Almacenamos el mensaje de la excepción en la variable '$mensajeExcepcion'

    echo "<span class='errorException'>Error: </span>" . $mensajeExcepcion . "<br>"; // Mostramos el mensaje de la excepción
    echo "<span class='errorException'>Código del error: </span>" . $errorExcepcion; // Mostramos el código de la excepción
    die($miExcepcionPDO);
} finally {
    // Cerrar la conexión
    if (isset($conn)) {
        $conn = null;
    }
}



