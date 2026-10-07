<?php
require __DIR__ . "/config.php";

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch ($method) {

    case 'GET':
        if ($id) {
            // GET /api/v1/clientes/{id} -> un solo registro
            $stmt = $pdo->prepare("SELECT * FROM cliente WHERE id_cliente = ?");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch();

            if ($cliente) {
                http_response_code(200);
                echo json_encode(["status" => "success", "data" => $cliente]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Cliente no encontrado"]);
            }
        } else {
            // GET /api/v1/clientes -> lista completa
            try {
                $stmt = $pdo->query("SELECT * FROM cliente ORDER BY id_cliente");
                $clientes = $stmt->fetchAll();
                http_response_code(200);
                echo json_encode(["status" => "success", "data" => $clientes]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error al consultar los clientes"]);
            }
        }
        break;

    case 'POST':
        // POST /api/v1/clientes -> crear
        $body = json_decode(file_get_contents("php://input"), true);

        if (!$body || empty($body['nombre']) || empty($body['apellido']) || empty($body['correo']) || empty($body['fecha_registro'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios: nombre, apellido, correo, fecha_registro"]);
            break;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO cliente (nombre, apellido, correo, telefono, fecha_registro) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $body['nombre'],
                $body['apellido'],
                $body['correo'],
                $body['telefono'] ?? null,
                $body['fecha_registro']
            ]);
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Registro creado exitosamente", "id" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "No se pudo crear el registro (correo duplicado o dato inválido)"]);
        }
        break;

    case 'PUT':
        // PUT /api/v1/clientes/{id} -> actualizar
        if (!$id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Debes indicar el ID del cliente a actualizar"]);
            break;
        }

        $body = json_decode(file_get_contents("php://input"), true);
        if (!$body || empty($body['nombre']) || empty($body['apellido']) || empty($body['correo']) || empty($body['fecha_registro'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios: nombre, apellido, correo, fecha_registro"]);
            break;
        }

        $check = $pdo->prepare("SELECT id_cliente FROM cliente WHERE id_cliente = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Cliente no encontrado"]);
            break;
        }

        try {
            $stmt = $pdo->prepare("UPDATE cliente SET nombre=?, apellido=?, correo=?, telefono=?, fecha_registro=? WHERE id_cliente=?");
            $stmt->execute([
                $body['nombre'],
                $body['apellido'],
                $body['correo'],
                $body['telefono'] ?? null,
                $body['fecha_registro'],
                $id
            ]);
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Registro actualizado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "No se pudo actualizar el registro"]);
        }
        break;

    case 'DELETE':
        // DELETE /api/v1/clientes/{id} -> eliminar
        if (!$id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Debes indicar el ID del cliente a eliminar"]);
            break;
        }

        $check = $pdo->prepare("SELECT id_cliente FROM cliente WHERE id_cliente = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Cliente no encontrado"]);
            break;
        }

        $stmt = $pdo->prepare("DELETE FROM cliente WHERE id_cliente = ?");
        $stmt->execute([$id]);
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Registro eliminado correctamente"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido"]);
        break;
}
