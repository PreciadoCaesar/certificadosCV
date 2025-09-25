<?php
    class Instructor extends Conectar{

        public function insert_instructor($inst_nom, $inst_apep, $inst_apem, $inst_correo, $inst_telf) {
            $conectar = parent::conexion();
            parent::set_names();
        
            // Insertar instructor sin imagen
            $sql = "INSERT INTO instructor (nom_instructor, ape_paterno, ape_materno, correo, telefono, foto, estado) 
                    VALUES (?, ?, ?, ?, ?, NULL, 'Activo')";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $inst_nom);
            $stmt->bindValue(2, $inst_apep);
            $stmt->bindValue(3, $inst_apem);
            $stmt->bindValue(4, $inst_correo);
            $stmt->bindValue(5, $inst_telf);
            $stmt->execute();
        
            // Devolver el ID insertado
            return $conectar->lastInsertId();
        }
        
        public function update_foto($inst_id, $nombreArchivo) {
            $conectar = parent::conexion();
            parent::set_names();
        
            $sql = "UPDATE instructor SET foto = ? WHERE ID_instructor = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $nombreArchivo);
            $stmt->bindValue(2, $inst_id);
            $stmt->execute();
        }
        
        
        public function update_instructor($inst_id, $inst_nom, $inst_apep, $inst_apem, $inst_correo, $inst_telf) {
            $conectar = parent::conexion();
            parent::set_names();
        
            $sql = "UPDATE instructor 
                    SET nom_instructor = ?, 
                        ape_paterno = ?, 
                        ape_materno = ?, 
                        correo = ?, 
                        telefono = ? 
                    WHERE ID_instructor = ?";
        
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $inst_nom);
            $stmt->bindValue(2, $inst_apep);
            $stmt->bindValue(3, $inst_apem);
            $stmt->bindValue(4, $inst_correo);
            $stmt->bindValue(5, $inst_telf);
            $stmt->bindValue(6, $inst_id);
        
            if (!$stmt->execute()) {
                error_log("Error en update_instructor: " . implode(" | ", $stmt->errorInfo()));
                return false;
            }
            
            return true;
        }
        

        public function obtener_foto($inst_id) {
            try {
                $conectar = parent::conexion();
                parent::set_names();
                
                $sql = "SELECT foto FROM instructor WHERE ID_instructor = ?";
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $inst_id, PDO::PARAM_INT);
                $stmt->execute();
                
                $fila = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve una fila
                
                return !empty($fila["foto"]) ? $fila["foto"] : null; // Retorna el nombre del archivo o null
            } catch (Exception $e) {
                error_log("Error al obtener la foto del instructor: " . $e->getMessage());
                return null;
            }
        }
        
        
        
        

        public function delete_instructor($inst_id) {
            $conectar = parent::conexion();
            parent::set_names();
            
            $sql = "UPDATE instructor SET estado = 'Inactivo' WHERE ID_instructor = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $inst_id, PDO::PARAM_INT);
            $stmt->execute();
        
            return $stmt->rowCount(); // Retorna número de filas afectadas
        }
        
        

        public function get_instructor() {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM instructor WHERE estado = 'Activo'";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        public function get_instructor_id($inst_id) {
                $conectar= parent::conexion();
                parent::set_names();
                $sql = "SELECT * FROM instructor WHERE estado = 'Activo' AND ID_instructor = ?";
                $sql=$conectar->prepare($sql);
                $sql->bindValue(1, $inst_id);
                $sql->execute();
                return $resultado=$sql->fetchAll();
        }
        
        public function get_last_10_instructores() {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT 
                        ID_instructor,
                        nom_instructor,
                        ape_paterno,
                        ape_materno,
                        correo,
                        telefono,
                        foto
                    FROM instructor
                    WHERE estado = 'Activo'
                    ORDER BY ID_instructor DESC
                    LIMIT 10";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        public function contarUltimosInstructoresActivos() {
            $conectar = parent::conexion();
            parent::set_names();
        
            $sql = "SELECT ID_instructor 
                    FROM instructor 
                    WHERE estado = 'Activo' 
                    ORDER BY ID_instructor DESC 
                    LIMIT 10";
        
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            return count($resultados); // Máximo 10
        }
        
       public function correo_existe($correo) {
            $conectar = parent::conexion();
            $sql = "SELECT COUNT(*) FROM instructor WHERE correo = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$correo]);
            return $stmt->fetchColumn() > 0;
        }

        public function correo_existe_en_otros($correo, $id) {
            $conectar = parent::conexion();
            $sql = "SELECT COUNT(*) FROM instructor WHERE correo = ? AND ID_instructor != ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$correo, $id]);
            return $stmt->fetchColumn() > 0;
        }
        
    }
?>