<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Certificado.php';

$certificado = new Certificado(); // ✅ Esto es LO QUE FALTABA
$ruta_base = Conectar::ruta();

switch ($_GET["op"]) {
case "guardar_url_certificado":
    $curd_id = $_POST["curd_id"] ?? null;
    $url_certificado = $_POST["url_certificado"] ?? null;

    if (!$curd_id || !$url_certificado) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Faltan datos"]);
        exit;
    }

    $resultado = $certificado->guardar_url_certificado($curd_id, $url_certificado);

    if ($resultado) {
        echo json_encode(["status" => "ok"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "No se pudo guardar la URL"]);
    }
    break;


case "obtener_url_certificado":
    $id_certificado = $_POST["id_certificado"] ?? null;

    if (!$id_certificado) {
        http_response_code(400);
        echo json_encode(["url" => ""]);
        exit;
    }

    $datos = $certificado->obtener_url_certificado($id_certificado);

    if ($datos && isset($datos["url_certificado"])) {
        echo json_encode(["url" => $datos["url_certificado"]]);
    } else {
        echo json_encode(["url" => ""]);
    }
    break;


   case "actualizar_certificado":
    try {
        $ID_certificado = $_POST["curd_id"] ?? null;
        $ID_curso = $_POST["SelectCurso"] ?? null;
        $ID_usuario = $_POST["SelectUsu"] ?? null;
        $fecha_emision = $_POST["fecha_emision"] ?? null;
        $fecha_vencimiento = $_POST["fecha_vencimiento"] ?? null;
        $nota = $_POST["nota_curso"] ?? null;


        if (!$ID_certificado || !$ID_usuario || !$ID_curso || !$fecha_emision || !$fecha_vencimiento || !$nota) {
            throw new Exception("Faltan datos obligatorios.");
        }

        if ($fecha_emision > $fecha_vencimiento) {
            echo json_encode([
                "status" => "error",
                "message" => "La fecha de emisión no puede ser mayor que la fecha de vencimiento."
            ]);
            exit;
        }

        // Verificar duplicidad
        if ($certificado->existe_certificado_usuario_curso($ID_usuario, $ID_curso, $ID_certificado)) {
            echo json_encode([
                "status" => "error",
                "message" => "Este usuario ya tiene un certificado registrado para este curso."
            ]);
            exit;
        }

        $actualizado = $certificado->update_certificado(
            $ID_certificado,
            $ID_usuario,
            $ID_curso,
            $fecha_emision,
            $fecha_vencimiento,
            $nota
        );

        $mensaje = $actualizado ? "Certificado actualizado correctamente" : "No se realizaron cambios";

        echo json_encode([
            "status" => "success",
            "message" => $mensaje
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error interno: " . $e->getMessage()
        ]);
    }
    break;

}
?>
