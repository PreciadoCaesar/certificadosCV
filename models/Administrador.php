<?php
class Administrador extends Conectar {
public function delete_admin($ID_administrador) {
    $conectar = parent::conexion();
    parent::set_names();
    $sql = "UPDATE administrador SET estado = 'Inactivo' WHERE ID_administrador = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $ID_administrador, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        return $stmt->rowCount(); // Devuelve el número de filas afectadas
    } else {
        return 0;
    }
}

public function restablecer_administrador($ID_administrador) {
    $conectar = parent::conexion();
    parent::set_names();
    $sql = "UPDATE administrador SET estado = 'Activo' WHERE ID_administrador = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $ID_administrador, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return $stmt->rowCount(); // Devuelve cuántas filas fueron afectadas
    } else {
        return 0;
    }
}

public function insert_admin($nombre, $ape_pat, $ape_mat, $sexo, $correo, $telefono, $password, $foto = null) {
    $conectar = parent::conexion();
    parent::set_names();
    
    $sql = "INSERT INTO administrador 
           (nom_admin, ape_paterno, ape_materno, sexo, correo, telefono, password, foto) 
           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conectar->prepare($sql);   
    $stmt->bindValue(1, $nombre);
    $stmt->bindValue(2, $ape_pat);
    $stmt->bindValue(3, $ape_mat);
    $stmt->bindValue(4, $sexo);
    $stmt->bindValue(5, $correo);
    $stmt->bindValue(6, $telefono);
    $stmt->bindValue(7, $password);
    $stmt->bindValue(8, $foto); 
    
    $stmt->execute();
    return $conectar->lastInsertId();
}


public function insertar_o_actualizar_permiso($admin_id, $tipo_permiso) {
    $conectar = parent::conexion();
    parent::set_names();

    // Primero verificamos si ya existe permiso para este admin
    $sql_check = "SELECT COUNT(*) FROM permiso WHERE ID_administrador = ?";
    $stmt_check = $conectar->prepare($sql_check);
    $stmt_check->execute([$admin_id]);
    $existe = $stmt_check->fetchColumn();

    if ($existe > 0) {
        // Actualizar permiso existente
        $sql_update = "UPDATE permiso SET tipo_permiso = ?, fecha_asignacion = CURRENT_TIMESTAMP WHERE ID_administrador = ?";
        $stmt_update = $conectar->prepare($sql_update);
        $stmt_update->execute([$tipo_permiso, $admin_id]);
    } else {
        // Insertar nuevo permiso
        $sql_insert = "INSERT INTO permiso (ID_administrador, tipo_permiso) VALUES (?, ?)";
        $stmt_insert = $conectar->prepare($sql_insert);
        $stmt_insert->execute([$admin_id, $tipo_permiso]);
    }
}



public function update_admin($id, $nombre, $ape_pat, $ape_mat, $sexo, $correo, $telefono, $password = null) {
    $conectar = parent::conexion();
    parent::set_names();

    if (!empty($password)) {
        // Actualiza también la contraseña
        $sql = "UPDATE administrador 
                SET nom_admin = ?, ape_paterno = ?, ape_materno = ?, sexo = ?, correo = ?, telefono = ?, password = ?
                WHERE ID_administrador = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([
            $nombre,
            $ape_pat,
            $ape_mat,
            $sexo,
            $correo,
            $telefono,
            $password,
            $id
        ]);
    } else {
        // No actualiza la contraseña
        $sql = "UPDATE administrador 
                SET nom_admin = ?, ape_paterno = ?, ape_materno = ?, sexo = ?, correo = ?, telefono = ?
                WHERE ID_administrador = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([
            $nombre,
            $ape_pat,
            $ape_mat,
            $sexo,
            $correo,
            $telefono,
            $id
        ]);
    }

    return $stmt->rowCount() > 0;
}

   public function get_administrador_id($ID_administrador) {
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "SELECT a.*, p.tipo_permiso 
            FROM administrador a 
            LEFT JOIN permiso p ON a.ID_administrador = p.ID_administrador 
            WHERE a.ID_administrador = ?;
            ";

    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $ID_administrador);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC); // o fetch() si es solo uno
}




    public function obtener_foto($admin_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT foto FROM administrador WHERE ID_administrador = ?";
        $stmt = $conectar->prepare($sql); // corregido
        $stmt->execute([$admin_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['foto'] : null;
    }

    public function update_foto($admin_id, $foto) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE administrador SET foto = ? WHERE ID_administrador = ?";
        $stmt = $conectar->prepare($sql); // corregido
        $stmt->execute([$foto, $admin_id]);
    }

    public function existe_correo($correo) {
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "SELECT * FROM administrador WHERE correo = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->execute([$correo]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}

  public function listar_logs($limite = 10) {
    $sql = "SELECT l.*, a.nom_admin 
            FROM logs_administracion l 
            LEFT JOIN administrador a ON l.ID_admin = a.ID_administrador 
            ORDER BY l.fecha DESC 
            LIMIT 10";

    $conectar = parent::conexion();
    $stmt = $conectar->prepare($sql);
    $stmt->execute(); 
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

} public function get_historial()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
    l.*, 
    a.nom_admin 
FROM 
    logs_administracion l
LEFT JOIN 
    administrador a ON l.ID_admin = a.ID_administrador
ORDER BY 
    l.fecha DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}
?>