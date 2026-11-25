<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "config/database.php";

$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "No se recibieron datos válidos"
    ]);
    exit;
}

$nombre = trim($datos["nombre"] ?? "");
$categoria = trim($datos["categoria"] ?? "");
$marca = trim($datos["marca"] ?? "");
$contenido = trim($datos["contenido"] ?? "");
$codigo = trim($datos["codigo_barras"] ?? "");
$precio = $datos["precio"] ?? null;
$cantidad = $datos["cantidad"] ?? null;

if (
    $nombre === "" ||
    $categoria === "" ||
    $codigo === "" ||
    !is_numeric($precio) ||
    !is_numeric($cantidad) ||
    (float)$precio < 0 ||
    (int)$cantidad < 0
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "Verifica los datos obligatorios del producto"
    ]);
    exit;
}

try {
    $sql = "INSERT INTO productos
            (nombre, categoria, marca, contenido, codigo_barras, precio, cantidad)
            VALUES
            (:nombre, :categoria, :marca, :contenido, :codigo, :precio, :cantidad)";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":nombre" => $nombre,
        ":categoria" => $categoria,
        ":marca" => $marca,
        ":contenido" => $contenido,
        ":codigo" => $codigo,
        ":precio" => $precio,
        ":cantidad" => $cantidad
    ]);

    echo json_encode([
        "success" => true,
        "mensaje" => "Producto guardado correctamente"
    ]);

} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "mensaje" => "Ya existe un producto con ese código de barras"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "mensaje" => "No fue posible guardar el producto"
        ]);
    }
}