<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "config/database.php";

try {
    $sql = "SELECT * FROM productos ORDER BY id DESC";

    $consulta = $conexion->query($sql);

    echo json_encode([
        "success" => true,
        "productos" => $consulta->fetchAll()
    ]);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "No fue posible consultar los productos"
    ]);
}