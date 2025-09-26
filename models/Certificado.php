<?php
class Certificado extends Conectar {

public function guardar_url_certificado($id_certificado, $url_certificado) {
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "UPDATE certificado SET url_certificado = ? WHERE ID_certificado = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $url_certificado);
    $stmt->bindValue(2, $id_certificado);
    return $stmt->execute(); // ✅ DEVUELVE TRUE o FALSE
}


public function obtener_url_certificado($id_certificado) {
    $conectar = parent::conexion();
    parent::set_names();
    $sql = "SELECT url_certificado FROM certificado WHERE ID_certificado = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $id_certificado);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function update_certificado($ID_certificado, $ID_usuario, $ID_curso, $fecha_emision, $fecha_vencimiento, $nota) {
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "UPDATE certificado 
            SET ID_usuario = ?, ID_curso = ?, fecha_emision = ?, fecha_vencimiento = ?, nota = ?
            WHERE ID_certificado = ?";
    
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $ID_usuario, PDO::PARAM_INT);
    $stmt->bindValue(2, $ID_curso, PDO::PARAM_INT);
    $stmt->bindValue(3, $fecha_emision);
    $stmt->bindValue(4, $fecha_vencimiento);
    $stmt->bindValue(5, $nota);
    $stmt->bindValue(6, $ID_certificado, PDO::PARAM_INT);

    return $stmt->execute();
}


public function existe_certificado_usuario_curso($ID_usuario, $ID_curso, $ID_certificado) {
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "SELECT COUNT(*) as total 
            FROM certificado 
            WHERE ID_usuario = ? 
              AND ID_curso = ? 
              AND ID_certificado != ?";

    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $ID_usuario, PDO::PARAM_INT);
    $stmt->bindValue(2, $ID_curso, PDO::PARAM_INT);
    $stmt->bindValue(3, $ID_certificado, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result["total"] > 0;
}



}
?>