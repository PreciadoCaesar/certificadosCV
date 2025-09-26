<?php
class Usuario extends Conectar {
    /* Función para el login de acceso del usuario */
    public function login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $conectar = parent::conexion();
    parent::set_names();

    if (isset($_POST["enviar"])) {
        $correo = $_POST["correo"];
        $pass   = $_POST["password"];

        if (empty($correo) || empty($pass)) {
            header("Location:" . Conectar::ruta() . "index.php?m=2");
            exit();
        } else {
            // -- LOGIN ADMINISTRADOR --
            $sql_admin = "SELECT a.*, p.tipo_permiso FROM administrador a JOIN permiso p ON a.ID_administrador = p.ID_administrador 
                        WHERE a.correo = ? AND a.estado = 'Activo'";
            $stmt_admin = $conectar->prepare($sql_admin);
            $stmt_admin->bindValue(1, $correo);
            $stmt_admin->execute();
            $resultado_admin = $stmt_admin->fetch();


            if ($resultado_admin && password_verify($pass, $resultado_admin["password"])) {
            // Login correcto → guardar variables de sesión
            $_SESSION["ID_administrador"] = $resultado_admin["ID_administrador"];
            $_SESSION["nom_admin"]        = $resultado_admin["nom_admin"];
            $_SESSION["ape_paterno"]      = $resultado_admin["ape_paterno"];
            $_SESSION["ape_materno"]      = $resultado_admin["ape_materno"];
            $_SESSION["correo"]           = $resultado_admin["correo"];
            $_SESSION["rol"]              = 'admin';
            $_SESSION["telefono"]         = $resultado_admin["telefono"];
            $_SESSION["sexo"]             = $resultado_admin["sexo"];
            $_SESSION["foto"]             = $resultado_admin["foto"];
            $_SESSION["tipo_permiso"]     = $resultado_admin["tipo_permiso"];

            // Registrar sesión
            $stmt_log = $conectar->prepare("INSERT INTO sesiones (ID_administrador, ID_gerente, login_time) VALUES (?, NULL, NOW())");
            $stmt_log->bindValue(1, $resultado_admin["ID_administrador"]);
            $stmt_log->execute();

            header("Location:" . Conectar::ruta() . "AdminMntInicio/");
            exit;
            }

            // -- LOGIN GERENTE --
            $sql_gerente = "SELECT * FROM gerente WHERE correo = ? AND password = ?";
            $stmt_gerente = $conectar->prepare($sql_gerente);
            $stmt_gerente->bindValue(1, $correo);
            $stmt_gerente->bindValue(2, $pass);
            $stmt_gerente->execute();
            $resultado_gerente = $stmt_gerente->fetch();

            if (is_array($resultado_gerente) && count($resultado_gerente) > 0) {
                $_SESSION["ID_gerente"]      = $resultado_gerente["ID_gerente"];
                $_SESSION["nom_admin"]       = $resultado_gerente["nom_admin"];
                $_SESSION["ape_paterno"]     = $resultado_gerente["ape_paterno"];
                $_SESSION["ape_materno"]     = $resultado_gerente["ape_materno"];
                $_SESSION["correo"]          = $resultado_gerente["correo"];
                $_SESSION["telefono"]        = $resultado_gerente["telefono"];
                $_SESSION["sexo"]            = $resultado_gerente["sexo"];
                $_SESSION["rol"]             = 'gerente';
                $_SESSION["foto"]            = $resultado_gerente["foto"];

                // Insertar en la tabla sesiones
                $stmt_log = $conectar->prepare("INSERT INTO sesiones (ID_administrador, ID_gerente, login_time) VALUES (NULL, ?, NOW())");
                $stmt_log->bindValue(1, $resultado_gerente["ID_gerente"]);
                $stmt_log->execute();

                header("Location:" . Conectar::ruta() . "AdminMntInicio/");
                exit();
            } else {
                header("Location:" . Conectar::ruta() . "index.php?m=1");
                exit();
            }
        }
    }
}

        

    /*TODO: Mostrar todos los cursos en los cuales esta inscrito un usuario */
    public function get_cursos_x_usuario($ID_administrador)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Ajusta el campo c.estado='Activo' si quieres filtrar solo activos
        $sql = "SELECT 
                    c.ID_certificado          AS curd_id,
                    cr.ID_curso               AS cur_id,
                    cr.nom_curso,
                    cr.fecha_inicio           AS cur_fechini,
                    cr.fecha_fin              AS cur_fechfin,
                    a.ID_administrador,
                    a.nom_admin,
                    a.ape_paterno,
                    a.ape_materno,
                    i.ID_instructor,
                    i.nom_instructor,
                    i.ape_paterno            AS inst_apep,
                    i.ape_materno            AS inst_apem
                FROM certificado c
                    INNER JOIN curso cr ON c.ID_curso = cr.ID_curso
                    INNER JOIN administrador a ON c.ID_usuario = a.ID_administrador
                    INNER JOIN instructor i ON cr.ID_instructor = i.ID_instructor
                WHERE c.ID_usuario = ?
                AND c.estado = 'Activo'";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ID_administrador, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /*TODO: Mostrar todos los cursos en los cuales esta inscrito un usuario */
    public function get_cursos_x_usuario_top10($ID_usuario)
{
    $conectar = parent::conexion();
    // parent::set_names(); // Coméntalo si no es indispensable

    $sql = "SELECT 
                c.ID_certificado,
                cr.nom_curso,
                DATE_FORMAT(c.fecha_emision, '%d-%m-%Y') AS fecha_emision,
                DATE_FORMAT(c.fecha_vencimiento, '%d-%m-%Y') AS fecha_vencimiento,
                cr.temario
            FROM certificado c
            INNER JOIN curso cr ON c.ID_curso = cr.ID_curso
            WHERE c.ID_usuario = ?
              AND c.estado = 'Activo'
            ORDER BY c.ID_certificado DESC
            LIMIT 10";

    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $ID_usuario, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public function get_cursos_usuario($cur_id = null) {
    $conectar = parent::conexion();
    parent::set_names();

    // No cerramos el SQL con ';' porque se puede agregar más condiciones
    $sql = "SELECT 
                c.ID_certificado AS curd_id,
                cr.ID_curso AS cur_id,
                cr.nom_curso,
                c.fecha_emision,
                c.fecha_vencimiento,
                u.ID_usuario,
                u.nom_usuario,
                u.ape_paterno,
                u.ape_materno,
                i.ID_instructor,
                i.nom_instructor,
                i.ape_paterno AS inst_apep,
                i.ape_materno AS inst_apem
            FROM certificado c
            INNER JOIN curso cr ON c.ID_curso = cr.ID_curso
            INNER JOIN usuario u ON c.ID_usuario = u.ID_usuario
            INNER JOIN instructor i ON cr.ID_instructor = i.ID_instructor
            WHERE c.estado = 'Activo'
              AND cr.estado = 'Activo'
              AND u.estado = 'Activo'";

    if (!is_null($cur_id)) {
        $sql .= " AND cr.ID_curso = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $cur_id, PDO::PARAM_INT);
    } else {
        $stmt = $conectar->prepare($sql);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    


    /*TODO: Mostrar todos los datos de un curso por su id de detalle */
    public function get_curso_x_id_detalle($curd_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
    
        $sql = "SELECT 
                cert.ID_certificado AS id_certificado,
                cert.nota AS Nota,
                curso.ID_curso AS id_curso, 
                curso.nom_curso AS nombre_curso, 
                curso.fecha_inicio AS fecha_inicio_curso, 
                curso.fecha_fin AS fecha_fin_curso, 
                curso.foto AS foto_curso, 
                curso.horas AS Horas, 
                curso.ruta_certificado AS fondo_certificado,
                us.ID_usuario AS id_usuario, 
                us.nom_usuario AS nombre_usuario, 
                us.ape_paterno AS apellido_paterno_usuario, 
                us.ape_materno AS apellido_materno_usuario,
                us.dni AS DNI,
                inst.ID_instructor AS id_instructor, 
                inst.nom_instructor AS nombre_instructor, 
                inst.ape_paterno AS apellido_paterno_instructor, 
                inst.ape_materno AS apellido_materno_instructor,
                cat.nombre AS nombre_categoria
            FROM certificado cert 
            INNER JOIN curso curso ON cert.ID_curso = curso.ID_curso 
            INNER JOIN categoria cat ON curso.ID_categoria = cat.ID_categoria
            INNER JOIN instructor inst ON curso.ID_instructor = inst.ID_instructor 
            INNER JOIN usuario us ON cert.ID_usuario = us.ID_usuario 
            WHERE cert.ID_certificado = ?";
    
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $curd_id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    

    /*TODO: Cantidad de Cursos por Usuario */
    public function get_total_cursos_x_usuario($ID_administrador)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT COUNT(*) as total 
                FROM certificado
                WHERE ID_usuario = ?
                AND estado = 'Activo'";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ID_administrador, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    /*TODO: Mostrar los datos del usuario segun el ID */
    public function get_administrador_x_id($ID_administrador)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * 
                FROM administrador
                WHERE ID_administrador = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ID_administrador, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /*TODO: Mostrar los datos del usuario segun el DNI */
    public function get_usuario_x_dni($dni)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Asumiendo que creaste la columna "dni" en "administrador"
        $sql = "SELECT * 
                FROM usuario
                WHERE dni = ?
                AND estado = 'Activo'";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $dni);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    /*TODO: Actualizar la informacion del perfil del usuario segun ID */
    public function update_usuario_perfil(
        $ID_administrador,
        $nom_admin,
        $ape_paterno,
        $ape_materno,
        $password,
        $sexo,
        $telefono,
        $correo,
        $dni,
        $foto
    ) {
        $conectar = parent::conexion();
        parent::set_names();

        // Obtener el registro actual
        $currentData = $this->get_administrador_x_id($ID_administrador);
        if (is_array($currentData) && count($currentData) > 0) {
            $current = $currentData[0];
        } else {
            error_log("No se encontró el registro para ID_administrador: $ID_administrador");
            return 0;
        }

        // Conservar valores si se envían vacíos
        $nom_admin   = (trim($nom_admin)   === "") ? $current["nom_admin"]   : $nom_admin;
        $ape_paterno = (trim($ape_paterno) === "") ? $current["ape_paterno"] : $ape_paterno;
        $ape_materno = (trim($ape_materno) === "") ? $current["ape_materno"] : $ape_materno;
        $password    = (trim($password)    === "") ? $current["password"]    : $password;
        $sexo        = (trim($sexo)        === "") ? $current["sexo"]        : $sexo;
        $telefono    = (trim($telefono)    === "") ? $current["telefono"]    : $telefono;
        $correo      = (trim($correo)      === "") ? $current["correo"]      : $correo;
        $dni         = (trim($dni)         === "") ? $current["dni"]         : $dni;
        // Si no se subió una nueva foto, se conserva la actual
        $foto        = (trim($foto)        === "") ? $current["foto"]        : $foto;

        // Validar el valor de 'sexo'
        $valores_sexo = ["Masculino", "Femenino"];
        if (!in_array($sexo, $valores_sexo)) {
            $sexo = $current["sexo"];
        }

        $sql = "UPDATE administrador
                    SET
                        nom_admin   = ?,
                        ape_paterno = ?,
                        ape_materno = ?,
                        password    = ?,
                        sexo        = ?,
                        telefono    = ?,
                        correo      = ?,
                        dni         = ?,
                        foto        = ?
                    WHERE
                        ID_administrador = ?";

        try {
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $nom_admin);
            $stmt->bindValue(2, $ape_paterno);
            $stmt->bindValue(3, $ape_materno);
            $stmt->bindValue(4, $password);
            $stmt->bindValue(5, $sexo);
            $stmt->bindValue(6, $telefono);
            $stmt->bindValue(7, $correo);
            $stmt->bindValue(8, $dni);
            $stmt->bindValue(9, $foto);
            $stmt->bindValue(10, $ID_administrador);

            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error en update_usuario_perfil: " . $e->getMessage());
            echo json_encode(["error" => $e->getMessage()]);
            exit();
        }
    }

    /*TODO: Funcion para insertar usuario sin imagen*/
    public function insert_usuario($usu_nom, $usu_apep, $usu_apem, $usu_correo, $usu_telefono, $usu_sexo, $usu_dni) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO usuario (nom_usuario,ape_paterno,ape_materno,correo,telefono,sexo,dni,rol,foto,estado) VALUES (?,?,?,?,?,?,?,'Usuario',NULL,'Activo')";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usu_nom);
        $stmt->bindValue(2, $usu_apep);
        $stmt->bindValue(3, $usu_apem);
        $stmt->bindValue(4, $usu_correo);
        $stmt->bindValue(5, $usu_telefono);
        $stmt->bindValue(6, $usu_sexo);
        $stmt->bindValue(7, $usu_dni);
        $stmt->execute();
        // Devolver el ID insertado
        return $conectar->lastInsertId();
    }

    public function update_foto($usu_id, $nombreArchivo)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE usuario SET foto = ? WHERE ID_usuario = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $nombreArchivo);
        $stmt->bindValue(2, $usu_id);
        $stmt->execute();
    }

    /*TODO: Funcion para actualizar usuario */
    public function update_usuario($usu_id, $usu_nom, $usu_apep, $usu_apem, $usu_correo, $usu_telefono, $usu_sexo, $usu_dni)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuario
                SET nom_usuario = ?,
                    ape_paterno = ?,
                    ape_materno = ?,
                    correo = ?,
                    telefono = ?,
                    sexo = ?,
                    dni = ?
                WHERE ID_usuario = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usu_nom);
        $stmt->bindValue(2, $usu_apep);
        $stmt->bindValue(3, $usu_apem);
        $stmt->bindValue(4, $usu_correo);
        $stmt->bindValue(5, $usu_telefono);
        $stmt->bindValue(6, $usu_sexo);
        $stmt->bindValue(7, $usu_dni);
        $stmt->bindValue(8, $usu_id);

        if (!$stmt->execute()) {
            error_log("Error en update_usuario: " . implode(" | ", $stmt->errorInfo()));
            return false;
        }
        
        return true;
    }

    public function obtener_foto($usu_id) {
        try {
            $conectar = parent::conexion();
            parent::set_names();
            
            $sql = "SELECT foto FROM usuario WHERE ID_usuario = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $fila = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve una fila
            
            return !empty($fila["foto"]) ? $fila["foto"] : null; // Retorna el nombre del archivo o null
        } catch (Exception $e) {
            error_log("Error al obtener la foto del usuario: " . $e->getMessage());
            return null;
        }
    }

    /*TODO: Eliminar cambiar de estado a la categoria */
    public function delete_usuario($usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuario
                SET
                    estado = 'Inactivo'
                WHERE
                    ID_usuario = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /*TODO: Listar todas las categorias */
    public function get_usuario()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuario WHERE estado = 'Activo'";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /*TODO: Listar todas las categorias */
    public function get_usuario_id($usu_id)
    {
        $conectar= parent::conexion();
                parent::set_names();
                $sql = "SELECT * FROM usuario WHERE estado = 'Activo' AND ID_usuario = ?";
                $sql=$conectar->prepare($sql);
                $sql->bindValue(1, $usu_id);
                $sql->execute();
                return $resultado=$sql->fetchAll();
    }

    public function get_usuarios_activos() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                    ID_administrador,
                    nom_admin,
                    ape_paterno,
                    ape_materno,
                    correo,
                    telefono,
                    foto
                FROM administrador
                WHERE estado = 'Activo'
                ORDER BY ID_administrador DESC
                LIMIT 10";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //*************************************************** VISTA ADMINISTRADORES - codigo funcional ***************************************************************************/
        public function get_administradores_con_permisos() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                    a.ID_administrador,
                    a.nom_admin,
                    a.ape_paterno,
                    a.ape_materno,
                    a.telefono,
                    a.correo,
                    a.sexo,
                    a.estado,
                    a.foto,
                    p.tipo_permiso
                FROM administrador a
                LEFT JOIN permiso p ON a.ID_administrador = p.ID_administrador";
        try {
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en get_administradores_con_permisos: " . $e->getMessage());
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }

    
    
    
    public function mostrar_admin($ID_administrador) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT a.*, p.perm_lectura, p.perm_escritura 
                FROM administrador a
                LEFT JOIN permiso p ON a.ID_administrador = p.ID_administrador
                WHERE a.ID_administrador = ?";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ID_administrador);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function guardar_editar_admin() {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            $id_actual = $_POST['ID_administrador'] ?? 0;
    
            // Obtener datos actuales si es edición
            $currentData = [];
            if (!empty($id_actual)) {
                $currentData = $this->mostrar_admin($id_actual);
            }
    
            // Armar datos dinámicamente (solo campos modificados)
            $datos = [
                'ID_administrador' => $id_actual,
                'nom_admin'   => $_POST['nom_admin'] ?? $currentData['nom_admin'] ?? '',
                'ape_paterno' => $_POST['ape_paterno'] ?? $currentData['ape_paterno'] ?? '',
                'ape_materno' => $_POST['ape_materno'] ?? $currentData['ape_materno'] ?? '',
                'dni'         => $_POST['dni'] ?? $currentData['dni'] ?? '',
                'sexo'        => $_POST['sexo'] ?? $currentData['sexo'] ?? 'Masculino',
                'telefono'    => $_POST['telefono'] ?? $currentData['telefono'] ?? '',
                'correo'      => $_POST['correo'] ?? $currentData['correo'] ?? '',
                'estado'      => $_POST['estado'] ?? $currentData['estado'] ?? 'Activo',
                'foto'        => $this->procesar_imagen($_POST['foto_actual'] ?? ($currentData['foto'] ?? ''))
            ];
    
            // Validar DNI si es nuevo o modificado
            if (empty($id_actual) || $datos['dni'] !== $currentData['dni']) {
                if ($this->validarCampoUnico('dni', $datos['dni'], $id_actual)) {
                    throw new Exception("El DNI ya está registrado");
                }
            }
    
            // Validar correo si es nuevo o modificado
            if (empty($id_actual) || $datos['correo'] !== $currentData['correo']) {
                if ($this->validarCampoUnico('correo', $datos['correo'], $id_actual)) {
                    throw new Exception("El correo ya está registrado");
                }
            }
    
            // Manejar contraseña
            if (!empty($_POST['password']) && $_POST['password'] !== "*****") {
                $datos['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } elseif (!empty($currentData['password'])) {
                $datos['password'] = $currentData['password'];
            }
    
            // Insertar o actualizar
            if (empty($id_actual)) {
                unset($datos['ID_administrador']);
                $this->insertar_admin($conectar, $datos);
                $mensaje = "Administrador creado correctamente";
            } else {
                $this->actualizar_admin($conectar, $datos);
                $this->actualizar_permisos(
                    $conectar, 
                    $id_actual,
                    $_POST['perm_lectura'] ?? $currentData['perm_lectura'] ?? 0,
                    $_POST['perm_escritura'] ?? $currentData['perm_escritura'] ?? 0
                );
                $mensaje = "Administrador actualizado correctamente";
            }
    
            $conectar->commit();
            return ["status" => "success", "message" => $mensaje];
            
        } catch (Exception $e) {
            $conectar->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    private function actualizar_admin($conectar, $datos) {
        $updates = [];
        foreach ($datos as $campo => $valor) {
            if ($campo !== 'ID_administrador') { 
                $updates[] = "$campo = :$campo";
            }
        }
        
        $sql = "UPDATE administrador SET " . implode(', ', $updates) . " 
                WHERE ID_administrador = :ID_administrador";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute($datos);
    }
    
    private function actualizar_permisos($conectar, $id_admin, $perm_lectura, $perm_escritura) {
        $sql = "UPDATE permiso SET 
                perm_lectura = ?,
                perm_escritura = ?
                WHERE ID_administrador = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$perm_lectura, $perm_escritura, $id_admin]);
    }
    
    
    
    private function procesar_imagen($foto_actual) {
        if (!empty($_FILES['foto']['name'])) {
            $this->eliminar_imagen_anterior($foto_actual);
            return $this->subir_imagen();
        }
        return $foto_actual ?: "default.png";
    }
    
    private function eliminar_imagen_anterior($nombre_archivo) {
        if ($nombre_archivo && $nombre_archivo !== "default.png") {
            $ruta = "../../public/img/img_usuario/" . $nombre_archivo;
            if (file_exists($ruta)) @unlink($ruta);
        }
    }
    
    private function insertar_admin($conectar, $datos) {
        $sql = "INSERT INTO administrador (" . implode(',', array_keys($datos)) . ") 
                VALUES (:" . implode(', :', array_keys($datos)) . ")";
        $stmt = $conectar->prepare($sql);
        $stmt->execute($datos);
        
        $id_nuevo = $conectar->lastInsertId();
        $this->insertar_permisos($conectar, $id_nuevo);
    }
    
    
    private function insertar_permisos($conectar, $id_admin) {
        $sql = "INSERT INTO permiso (ID_administrador, perm_lectura, perm_escritura)
                VALUES (?, ?, ?)";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([
            $id_admin,
            $_POST['perm_lectura'] ?? 0,
            $_POST['perm_escritura'] ?? 0
        ]);
    }
    
    private function subir_imagen() {
        $carpeta = "../../public/img/img_usuario/";
        $nombre_archivo = "foto_" . uniqid() . "." . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $carpeta . $nombre_archivo)) {
            return $nombre_archivo;
        }
        return "default.png";
    }
    


    
    private function validarCampoUnico($campo, $valor, $id_excluir = 0) {
        $conectar = parent::conexion();
        $sql = "SELECT COUNT(*) FROM administrador WHERE $campo = ? AND ID_administrador != ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$valor, $id_excluir]);
        return $stmt->fetchColumn() > 0;
    }

    public function get_usuario_modal($cur_id){
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuario WHERE estado = 'Activo' AND ID_usuario NOT IN ( SELECT ID_usuario FROM certificado WHERE ID_curso = ? AND estado = 'Activo' );";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cur_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    
    public function delete_certificado($certificado_id){
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE certificado
                SET
                    estado = 'Inactivo'
                WHERE
                    ID_certificado = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $certificado_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function contarUsuariosActivos() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) as total FROM administrador WHERE estado = 'Activo'";
        $query = $conectar->prepare($sql);
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function get_administrador_id($ID_administrador){
    $conectar = parent::conexion();
    parent::set_names();
    $sql = "SELECT * FROM administrador WHERE estado = 'Activo' AND ID_administrador = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $ID_administrador);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obtener_foto_admin($ID_administrador) {
    try {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT foto FROM administrador WHERE ID_administrador = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ID_administrador, PDO::PARAM_INT);
        $stmt->execute();
        
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($fila["foto"]) ? $fila["foto"] : null;
    } catch (Exception $e) {
        error_log("Error al obtener la foto del usuario: " . $e->getMessage());
        return null;
    }
}
public function update_foto_admin($ID_administrador, $nombreArchivo) {
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "UPDATE administrador SET foto = ? WHERE ID_administrador = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $nombreArchivo);
    $stmt->bindValue(2, $ID_administrador);
    $stmt->execute();
}

public function update_administrador_id(
    $ID_administrador,
    $nombre,
    $apellido_paterno,
    $apellido_materno,
    $sexo,
    $correo,
    $telefono
) {
    try {
        // Conexión a la base de datos y configuración
        $conectar = parent::conexion();
        parent::set_names();

        // Consulta SQL corregida (se eliminó la coma antes del WHERE)
        $sql = "UPDATE administrador SET 
                    nom_admin = ?, 
                    ape_paterno = ?, 
                    ape_materno = ?, 
                    sexo = ?, 
                    correo = ?, 
                    telefono = ? 
                WHERE ID_administrador = ?";

        // Preparar la consulta
        $stmt = $conectar->prepare($sql);

        // Asignar valores a los parámetros
        $stmt->bindValue(1, $nombre);
        $stmt->bindValue(2, $apellido_paterno);
        $stmt->bindValue(3, $apellido_materno);
        $stmt->bindValue(4, $sexo);
        $stmt->bindValue(5, $correo);
        $stmt->bindValue(6, $telefono);
        $stmt->bindValue(7, $ID_administrador);

        // Ejecutar y retornar resultado
        return $stmt->execute();
    } catch (Exception $e) {
        // Registrar el error en el log del servidor
        error_log("Error al actualizar datos del administrador: " . $e->getMessage());
        return false;
    }
}

public function verificarPassword($id_admin, $password_actual) {
    $conectar = parent::conexion();
    $sql = "SELECT password FROM administrador WHERE ID_administrador = :id";
    $stmt = $conectar->prepare($sql);
    $stmt->bindParam(':id', $id_admin, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Comparación segura con el hash almacenado
        return password_verify($password_actual, $row['password']);
    }
    return false;
}



public function cambiarPassword($id_admin, $nueva_password) {
    $conectar = parent::conexion();
    // Hashear la nueva contraseña antes de guardarla
    $hashed_password = password_hash($nueva_password, PASSWORD_DEFAULT);

    $sql = "UPDATE administrador SET password = :password WHERE ID_administrador = :id";
    $stmt = $conectar->prepare($sql);
    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id_admin, PDO::PARAM_INT);

    return $stmt->execute();
}


public function correo_existe($correo) {
    $conectar = parent::conexion();
    $sql = "SELECT COUNT(*) FROM usuario WHERE correo = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->execute([$correo]);
    return $stmt->fetchColumn() > 0;
}

// Verifica si el correo ya existe en otro usuario (excluyendo el actual)
public function correo_existe_en_otros($correo, $id_usuario) {
    $conectar = parent::conexion();
    $sql = "SELECT COUNT(*) FROM usuario WHERE correo = ? AND ID_usuario != ?";
    $stmt = $conectar->prepare($sql);
    $stmt->execute([$correo, $id_usuario]);
    return $stmt->fetchColumn() > 0;
}

// Verifica si el DNI ya existe (en cualquier usuario)
public function dni_existe($dni) {
    $conectar = parent::conexion();
    $sql = "SELECT COUNT(*) FROM usuario WHERE dni = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->execute([$dni]);
    return $stmt->fetchColumn() > 0;
}

// Verifica si el DNI ya existe en otro usuario (excluyendo el actual)
public function dni_existe_en_otros($dni, $id_usuario) {
    $conectar = parent::conexion();
    $sql = "SELECT COUNT(*) FROM usuario WHERE dni = ? AND ID_usuario != ?";
    $stmt = $conectar->prepare($sql);
    $stmt->execute([$dni, $id_usuario]);
    return $stmt->fetchColumn() > 0;
}

 public function get_certificado_id($ID_certificado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * 
                FROM certificado
                WHERE ID_certificado = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ID_certificado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}

?>