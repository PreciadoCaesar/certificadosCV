<?php
    class Categoria extends Conectar{
        
        public function insert_categoria($cat_nom){
            try {
                $conectar = parent::conexion();
                parent::set_names();
                
                $sql = "INSERT INTO categoria (nombre, estado) VALUES (?, 'Activo')";
                $stmt = $conectar->prepare($sql);
                $stmt->bindParam(1, $cat_nom, PDO::PARAM_STR);
                
                $stmt->execute();
                return $stmt->rowCount(); // Retorna número de filas afectadas (1 si se insertó correctamente)
            } catch (Exception $e) {
                return 0; // Retorna 0 si hubo un error
            }
        }
        
        /* TODO: Función para actualizar categoría */
        public function update_categoria($cat_id, $cat_nom) {
            try {
                $conectar = parent::conexion();
                parent::set_names();
                
                $sql = "UPDATE categoria SET nombre = ? WHERE ID_categoria = ?";
                $stmt = $conectar->prepare($sql);
                $stmt->bindParam(1, $cat_nom, PDO::PARAM_STR);
                $stmt->bindParam(2, $cat_id, PDO::PARAM_INT);
                
                $stmt->execute();
        
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Categoría actualizada correctamente."];
                } else {
                    return ["status" => "warning", "message" => "No se realizaron cambios o la categoría no existe."];
                }
            } catch (Exception $e) {
                return ["status" => "error", "message" => "Error al actualizar la categoría: " . $e->getMessage()];
            }
        }
        
        
        

        /* TODO: Eliminar (cambiar de estado la categoría a 'Inactivo') */
        public function delete_categoria($cat_id){
            $conectar = parent::conexion();
            parent::set_names();

            $sql = "UPDATE categoria SET estado = 'Inactivo' WHERE ID_categoria = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $cat_id);
            $sql->execute();

            return $sql->rowCount(); // Retorna el número de filas afectadas
        }

        
        
        /* TODO: Listar todas las categorías activas */
        public function get_categoria(){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT * FROM categoria WHERE estado = 'Activo'";
            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        /* TODO: Filtrar según ID de categoría */
        public function get_categoria_id($cat_id) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM categoria WHERE ID_categoria = ?"; 
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $cat_id);
            $sql->execute();
            return $sql->fetch(PDO::FETCH_ASSOC);
        }

        public function nombre_existe($nombre) {
            $conectar = parent::conexion();
            $sql = "SELECT COUNT(*) FROM categoria WHERE nombre = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$nombre]);
            return $stmt->fetchColumn() > 0;
        }

        public function nombre_existe_en_otros($nombre, $id) {
            $conectar = parent::conexion();
            $sql = "SELECT COUNT(*) FROM categoria WHERE nombre = ? AND ID_categoria != ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$nombre, $id]);
            return $stmt->fetchColumn() > 0;
        }


        
    }
?>
