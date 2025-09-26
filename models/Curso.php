<?php
    class Curso extends Conectar{

            public function insert_curso($id_categoria, $nom_curso, $fecha_inicio, $fecha_fin, $id_instructor, $horas) {
            $conectar = parent::conexion();    
            parent::set_names();                

            $sql = "INSERT INTO curso (nom_curso, fecha_inicio, fecha_fin, ID_categoria, ID_instructor, estado, ruta_certificado, horas)
                    VALUES (?, ?, ?, ?, ?, 'Activo', '', ?)";  // ← aquí se usa el ? para horas

            $stmt = $conectar->prepare($sql);
            $stmt->execute([$nom_curso, $fecha_inicio, $fecha_fin, $id_categoria, $id_instructor, $horas]);

            return $conectar->lastInsertId(); // Retorna el ID insertado
        }



    // Actualizar curso existente
         public function update_curso($id_curso, $id_categoria, $nom_curso, $fecha_inicio, $fecha_fin, $id_instructor, $horas) {
            $conectar = parent::conexion();
            parent::set_names();

            $sql = "UPDATE curso 
                    SET nom_curso = ?, 
                        fecha_inicio = ?, 
                        fecha_fin = ?, 
                        ID_categoria = ?, 
                        ID_instructor = ?,
                        horas = ?
                    WHERE ID_curso = ?";
            
            $stmt = $conectar->prepare($sql);
            $stmt->execute([
                $nom_curso,
                $fecha_inicio,
                $fecha_fin,
                $id_categoria,
                $id_instructor,
                $horas,
                $id_curso
            ]);
        }



                // Obtener nombre de archivo de la foto del curso
                    public function obtener_foto($id_curso) {
                        $conectar = parent::conexion();
                        parent::set_names();

                        $sql = "SELECT foto FROM curso WHERE ID_curso = ?";
                        $stmt = $conectar->prepare($sql);
                        $stmt->execute([$id_curso]);

                        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                        return $resultado ? $resultado['foto'] : null;
                    }


                    public function update_foto($id_curso, $nombre_archivo) {
                        $conectar = parent::conexion();
                        parent::set_names();

                        $sql = "UPDATE curso SET foto = ? WHERE ID_curso = ?";
                        $stmt = $conectar->prepare($sql);
                        $stmt->execute([$nombre_archivo, $id_curso]);
                    }


        public function delete_curso($cur_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="UPDATE tm_curso
                SET
                    est = 0
                WHERE
                    cur_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $cur_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function get_curso(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT
                        curso.ID_curso,
                        IFNULL(curso.foto, 'default.png') AS curso_foto, 
                        categoria.ID_categoria,
                        categoria.nombre AS categoria_nombre,
                        curso.nom_curso,
                        curso.fecha_inicio,
                        curso.fecha_fin,
                        curso.ID_instructor,
                        IFNULL(instructor.foto, 'default.png') AS instructor_foto, 
                        instructor.nom_instructor,
                        instructor.ape_paterno,
                        instructor.ape_materno 
                    FROM curso
                    INNER JOIN categoria ON curso.ID_categoria = categoria.ID_categoria
                    INNER JOIN instructor ON curso.ID_instructor = instructor.ID_instructor
                    WHERE curso.estado = 'Activo'"; 
        
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna el resultado en un array asociativo
        }
                
            public function get_curso_id($cur_id) {
            $conectar = parent::conexion();
            parent::set_names();

            $sql = "SELECT * FROM curso WHERE estado = 'Activo' AND ID_curso = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $cur_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
           public function get_curso_edit() {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM curso WHERE estado = 'Activo'"; 
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        }

        

        
        public function delete_curso_usuario($cur_id) {
            $conectar = parent::conexion();
            parent::set_names();
        
            $sql = "UPDATE curso SET estado = 'Inactivo' WHERE ID_curso = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $cur_id);
            
            $sql->execute();
            
            return $sql->rowCount(); // Retorna el número de filas afectadas
        }
        

        /*TODO: Insert Curso por Usuario */
        public function insert_curso_usuario($cur_id, $usu_id) {
            $conectar = parent::conexion();
            parent::set_names();
        
            // 1. Obtener la fecha de fin del curso
            $sql_fecha = "SELECT fecha_fin FROM curso WHERE ID_curso = ?";
            $sql_fecha = $conectar->prepare($sql_fecha);
            $sql_fecha->bindValue(1, $cur_id);
            $sql_fecha->execute();
            $fecha_curso = $sql_fecha->fetch(PDO::FETCH_ASSOC);
        
            if (!$fecha_curso) {
                return ['error' => 'Curso no encontrado'];
            }
        
            $fecha_emision = $fecha_curso['fecha_fin'];
        
            // 2. Calcular la fecha de vencimiento (3 meses después)
            $fecha_vencimiento = date('Y-m-d', strtotime($fecha_emision . ' +3 months'));
        
            // 3. Insertar el certificado con fecha_emision y fecha_vencimiento
            $sql = "INSERT INTO certificado (ID_usuario, ID_curso, fecha_emision, fecha_vencimiento, estado) 
                    VALUES (?, ?, ?, ?, 'Activo')";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->bindValue(2, $cur_id);
            $sql->bindValue(3, $fecha_emision);
            $sql->bindValue(4, $fecha_vencimiento);
            $sql->execute();
        
            // 4. Obtener el ID del certificado recién insertado
            $sql1 = "SELECT LAST_INSERT_ID() AS ID_certificado";
            $sql1 = $conectar->prepare($sql1);
            $sql1->execute();
            return $sql1->fetch(PDO::FETCH_ASSOC);
        }
        

        public function update_imagen_curso($cur_id,$cur_img){
            $conectar= parent::conexion();
            parent::set_names();

            require_once("Curso.php");
            $curx = new Curso();
            $cur_img = '';
            if ($_FILES["cur_img"]["name"]!=''){
                $cur_img = $curx->upload_file();
            }

            $sql="UPDATE tm_curso
                SET
                    cur_img = ?
                WHERE
                    cur_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $cur_img);
            $sql->bindValue(2, $cur_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function upload_file(){
            if(isset($_FILES["cur_img"])){
                $extension = explode('.', $_FILES['cur_img']['name']);
                $new_name = rand() . '.' . $extension[1];
                $destination = '../public/' . $new_name;
                move_uploaded_file($_FILES['cur_img']['tmp_name'], $destination);
                return "../../public/".$new_name;
            }
        }

        /* Listar los 10 cursos destacados de la vista home */
        
        public function get_last_10_cursos() {
            $conectar = parent::conexion();
            parent::set_names();
            // Usamos los nombres reales de la BD: "curso" y "instructor"
            $sql = "SELECT 
                        c.ID_curso,
                        c.nom_curso,
                        c.foto,
                        c.temario,
                        c.fecha_inicio,
                        c.fecha_fin,
                        i.nom_instructor,
                        i.foto AS foto_instructor
                    FROM curso c
                    INNER JOIN instructor i ON c.ID_instructor = i.ID_instructor
                    WHERE c.estado = 'Activo'
                    ORDER BY c.ID_curso DESC
                    LIMIT 10";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }


        
        public function guardarCertificado($id_curso, $nombre_archivo) {
            $conectar = parent::conexion();
            parent::set_names();
        
            $sql = "UPDATE curso SET ruta_certificado = ? WHERE ID_curso = ?";
            $stmt = $conectar->prepare($sql);
            return $stmt->execute([$nombre_archivo, $id_curso]);
        }


        public function guardarDocumento($id_curso, $nombre_documento) {
            if (empty($id_curso) || empty($nombre_documento)) {
                return false;
            }

            try {
                $conexion = parent::conexion();
                parent::set_names();

                $sql = "UPDATE curso SET temario = ? WHERE ID_curso = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$nombre_documento, $id_curso]);
                
                return $stmt->rowCount() > 0; // true si actualizó alguna fila
            } catch (PDOException $e) {
                // Log error si tienes un sistema de logs
                return false;
            }
        }

        
        public function contarCursos() {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) as total 
            FROM (
                SELECT ID_curso 
                FROM curso 
                WHERE estado = 'Activo' 
                ORDER BY ID_curso DESC 
                LIMIT 10
            ) AS ultimos";
            $query = $conectar->prepare($sql); // corregido
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];
       
        }
        
        public function MostrarCurso($ID_curso) {
            $conectar = parent::conexion();
            parent::set_names();
        
            $sql = "SELECT 
                        curso.nom_curso,
                        curso.ID_curso,
                        curso.temario,
                        curso.fecha_inicio,
                        curso.fecha_fin,
                        instructor.nom_instructor AS nombre_instructor
                    FROM 
                        curso
                    INNER JOIN 
                        instructor ON curso.ID_instructor = instructor.ID_instructor
                        
                    WHERE 
                        curso.ID_curso = ?";
        
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$ID_curso]); // <- aquí se pasa el parámetro correctamente
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function obtenerDocumento($id_curso) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT temario FROM curso WHERE ID_curso = :id_curso LIMIT 1";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso, PDO::PARAM_INT);
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $row['temario'];  // Devuelve el nombre del archivo
            }
            return false; // No hay documento registrado
        }

        public function VerificarDocumento($id_curso) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT temario FROM curso WHERE id_curso = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$id_curso]);

            if ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $fila['temario'];
            } else {
                return null;
            }
        }

        public function existeTemario($id_curso) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT temario FROM curso WHERE ID_curso = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$id_curso]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row['temario'])) {
                $ruta = '../public/temarios/' . $row['temario'];
                return file_exists($ruta);
            }

            return false;
        }

    public function guardar_url_certificado($id_certificado, $url_certificado) {
    $conectar = Conectar::conexion();
    $sql = "UPDATE certificado SET url_certificado = ? WHERE ID_certificado = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $url_certificado);
    $stmt->bindValue(2, $id_certificado);
    $stmt->execute();
}



        
}

       
        

?>