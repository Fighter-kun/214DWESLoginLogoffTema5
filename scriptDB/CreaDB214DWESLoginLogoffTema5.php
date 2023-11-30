<?php
// Configuración de conexión con la base de datos
require_once '../config/confDB.php';

try {
    // Crear conexión
    $conn = new PDO(DSN, USERNAME, PASSWORD);

    // Eliminamos las Tablas de la base de datos en caso de que exista alguna
    $query1 = "DROP TABLE IF EXISTS T01_Usuario;";
    
    $query2 = "DROP TABLE IF EXISTS T02_Departamento;";

    // Utilizar la base de datos recién creada
    $query3 = "USE dbs12302455;";

    // Crear la tabla T01_Usuario
    $query4 = "CREATE TABLE T01_Usuario (
        T01_CodUsuario CHAR(8) PRIMARY KEY,
        T01_Password VARCHAR(64),
        T01_DescUsuario VARCHAR(255),
        T01_NumConexiones INT DEFAULT 0,
        T01_FechaHoraUltimaConexion DATETIME DEFAULT CURRENT_TIMESTAMP,
        T01_Perfil ENUM('usuario','administrador') DEFAULT 'usuario',
        T01_ImagenUsuario BLOB
    )ENGINE=INNODB;";

    // Crear la tabla T02_Departamento
    $query5 = "CREATE TABLE T02_Departamento (
        T02_CodDepartamento CHAR(3) PRIMARY KEY,
        T02_DescDepartamento VARCHAR(255),
        T02_FechaCreacionDepartamento DATETIME DEFAULT CURRENT_TIMESTAMP,
        T02_VolumenDeNegocio FLOAT,
        T02_FechaBajaDepartamento DATETIME
    )ENGINE=INNODB;";

    // Ejecutar consultas SQL
    $sql_queries = [$query1, $query2, $query3, $query4, $query5];

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



