<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Administrador.php';

$usuario = new Usuario();
$administrador = new Administrador();
$ruta_base = Conectar::ruta();

$user_id = isset($_SESSION["ID_administrador"]) 
    ? $_SESSION["ID_administrador"] 
    : (isset($_SESSION["ID_gerente"]) ? $_SESSION["ID_gerente"] : 0);

/*TODO: Opcion de solicitud de controller */
switch ($_GET["op"]) {

    /*TODO: MicroServicio para poder mostrar el listado de cursos de un usuario con certificado */
    case "listar_cursos":
        $datos = $usuario->get_cursos_x_usuario($_POST["ID_usuario"]);
        $data = array();

        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["nom_curso"]; // Nombre del curso
            $sub_array[] = $row["fecha_inicio"]; // Fecha de inicio
            $sub_array[] = $row["fecha_fin"]; // Fecha de finalización
            $sub_array[] = $row["nom_instructor"] . " " . $row["ape_paterno"]; // Nombre completo del instructor
            $sub_array[] = '<button type="button" onClick="certificado(' . $row["ID_certificado"] . ');"  
                                 id="' . $row["ID_certificado"] . '" 
                                 class="btn btn-outline-primary btn-icon">
                                    <div><i class="fa fa-id-card-o"></i></div>
                                </button>';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break;

    /*TODO: MicroServicio para poder mostrar el listado de cursos de un usuario con certificado */
    case "listar_cursos_top10":
        header('Content-Type: application/json');
    
        $datos = $usuario->get_cursos_x_usuario_top10($_POST["usu_id"]);
        $data = [];
    
        foreach ($datos as $row) {
            $id_cert = $row["ID_certificado"];
            $nombre_archivo = $row["temario"];
            $data[] = [
                $id_cert,
                $row["nom_curso"],
                $row["fecha_emision"],
                $row["fecha_vencimiento"],
                'Peruano',
              '
<div class="btn-group" role="group">
    <button type="button" onClick="certificado(' . $id_cert . ')" class="btn-certificado btn-icono">
        <i class="fa-solid fa-certificate"></i> Certificado
    </button>
    <button type="button" onClick="descargarDocumento(\'' . $nombre_archivo . '\')" class="btn-diapositiva btn-icono">
        <i class="fa-solid fa-file-powerpoint"></i> Diapositiva
    </button>
</div>

<style>
.btn-icono {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    color: white;
    cursor: pointer;
    transition: background 0.3s ease;
    white-space: nowrap;
}

.btn-certificado {
    background-color: #17a2b8;
}

.btn-certificado:hover {
    background-color: #138496;
}

.btn-diapositiva {
    background-color: #6f42c1;
}

.btn-diapositiva:hover {
    background-color: #59359e;
}

.btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 10px;
}
</style>
'

            ];
        }
    
        echo json_encode([
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ]);
        exit;
    
    
    
    

    /*TODO: Microservicio para mostar informacion del certificado con el curd_id */
    case "mostrar_curso_detalle":
        $datos = $usuario->get_curso_x_id_detalle($_POST["ID_certificado"]);
    
        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["id_certificado"] = $row["id_certificado"];
                $output["id_curso"] = $row["id_curso"];
                $output["nombre_curso"] = $row["nombre_curso"];
                $output["fecha_inicio_curso"] = $row["fecha_inicio_curso"];
                $output["fecha_fin_curso"] = $row["fecha_fin_curso"];
                $output["foto_curso"] = $row["foto_curso"];
                $output["fondo_certificado"] = $row["fondo_certificado"];
                $output["id_usuario"] = $row["id_usuario"];
                $output["nombre_usuario"] = $row["nombre_usuario"];
                $output["apellido_paterno_usuario"] = $row["apellido_paterno_usuario"];
                $output["apellido_materno_usuario"] = $row["apellido_materno_usuario"];
                $output["id_instructor"] = $row["id_instructor"];
                $output["nombre_instructor"] = $row["nombre_instructor"];
                $output["apellido_paterno_instructor"] = $row["apellido_paterno_instructor"];
                $output["apellido_materno_instructor"] = $row["apellido_materno_instructor"];
                $output["nombre_categoria"] = $row["nombre_categoria"];
                $output["DNI"] = $row["DNI"];
                $output["Nota"] = $row["Nota"];
                $output["Horas"] = $row["Horas"];

            }
    
            echo json_encode($output);
        }
        break;
    
    case "total":
        $datos = $usuario->get_total_cursos_x_usuario($_POST["ID_usuario"]);

        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["total"] = $row["total"];
            }
            echo json_encode($output);
        }
        break;

    /*TODO: Mostrar informacion segun DNI del usuario registrado */
    case "consulta_dni":
        $datos = $usuario->get_usuario_x_dni($_POST["usu_dni"]);

        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["ID_usuario"] = $row["ID_usuario"];
                $output["foto"] = $row["foto"];
                $output["nom_usuario"] = $row["nom_usuario"];
                $output["ape_paterno"] = $row["ape_paterno"];
                $output["ape_materno"] = $row["ape_materno"];
                $output["correo"] = $row["correo"];
                $output["telefono"] = $row["telefono"];
                $output["dni"] = $row["dni"];
            }
            echo json_encode($output);
        }
        break;

    /*TODO: Actualizar datos de perfil */
    case "update_perfil":
        // Procesar archivo subido (fotografía)
        $fotoNombre = "";
        if (isset($_FILES["usu_foto"]) && $_FILES["usu_foto"]["error"] == 0) {
            // Define la carpeta destino (ajusta la ruta según tu proyecto)
            $destino = "../public/uploads/fotos/";
            if (!is_dir($destino)) {
                mkdir($destino, 0777, true);
            }
            // Extraer extensión y generar nombre único
            $ext = pathinfo($_FILES["usu_foto"]["name"], PATHINFO_EXTENSION);
            $fotoNombre = uniqid("foto_") . "." . $ext;
            $rutaDestino = $destino . $fotoNombre;

            if (!move_uploaded_file($_FILES["usu_foto"]["tmp_name"], $rutaDestino)) {
                echo json_encode(["error" => "Error al subir la fotografía."]);
                exit();
            }
        }

        // Llamar al método update, pasando $fotoNombre (vacío si no se subió archivo)
        $resultado = $usuario->update_usuario_perfil(
            $_POST["ID_administrador"],
            $_POST["nom_admin"],
            $_POST["ape_paterno"],
            $_POST["ape_materno"],
            $_POST["password"],
            $_POST["sexo"],
            $_POST["telefono"],
            $_POST["correo"],
            $_POST["dni"],
            $fotoNombre
        );
        echo json_encode(["success" => true, "rows" => $resultado]);
        break;


    /*TODO: Guardar y editar cuando se tenga el ID */

    case "guardaryeditar":
    try {
        $usu_id = $_POST["usu_id"] ?? null;
        $correo = $_POST["correo_usuario"];
        $dni = $_POST["dni_usuario"];

        // Si no hay ID, se registra un nuevo usuario
        if (empty($usu_id)) {
            // Verificar si el correo ya existe
            if ($usuario->correo_existe($correo)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "El correo ya está registrado."
                ]);
                exit;
            }

            // Verificar si el DNI ya existe
            if ($usuario->dni_existe($dni)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "El DNI ya está registrado."
                ]);
                exit;
            }

            $usu_id = $usuario->insert_usuario(
                $_POST["nombre_usuario"],
                $_POST["ape_paterno_usuario"],
                $_POST["ape_materno_usuario"],
                $correo,
                $_POST["telefono_usuario"],
                $_POST["sexo_usuario"],
                $dni
            );

            $mensaje = "Usuario registrado correctamente";

        } else {
            // Verificar si el correo ya existe en otro usuario
            if ($usuario->correo_existe_en_otros($correo, $usu_id)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "El correo ya está registrado en otro usuario."
                ]);
                exit;
            }

            // Verificar si el DNI ya existe en otro usuario
            if ($usuario->dni_existe_en_otros($dni, $usu_id)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "El DNI ya está registrado en otro usuario."
                ]);
                exit;
            }

            $actualizado = $usuario->update_usuario(
                $usu_id,
                $_POST["nombre_usuario"],
                $_POST["ape_paterno_usuario"],
                $_POST["ape_materno_usuario"],
                $correo,
                $_POST["telefono_usuario"],
                $_POST["sexo_usuario"],
                $dni
            );

            $mensaje = $actualizado ? "Usuario actualizado correctamente" : "No se realizaron cambios";
        }

        // Procesar imagen de perfil si se ha subido una nueva
        if (!empty($_FILES["foto"]["name"])) {
            $extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
            $nombreArchivo = $usu_id . "_usuario." . $extension;
            $rutaDestino = "../public/img/img_usuario/" . $nombreArchivo;

            // Eliminar imagen anterior si existe
            $imagenActual = $usuario->obtener_foto($usu_id);
            if ($imagenActual) {
                $rutaImagenActual = "../public/img/img_usuario/" . $imagenActual;
                if (file_exists($rutaImagenActual)) {
                    unlink($rutaImagenActual);
                }
            }

            if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestino)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Error al subir la imagen."
                ]);
                exit;
            } else {
                $usuario->update_foto($usu_id, $nombreArchivo);
            }
        }

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





    /*TODO: Eliminar segun ID */
    case "eliminar":
        if (!empty($_POST["usu_id"])) {
            $resultado = $usuario->delete_usuario($_POST["usu_id"]);
            echo json_encode(["success" => $resultado > 0]);
        } else {
            echo json_encode(["success" => false]);
        }
        exit;
        
    /*TODO:  Listar toda la informacion segun formato de datatable */
    case "listar":
        $datos = $usuario->get_usuario();
        $data = array();

        foreach ($datos as $row) {
            $sub_array = array();
            $foto = !empty($row["foto"]) ? $row["foto"] : "default.png";
            $sub_array[] = '
                <img 
                    src="' . $ruta_base . 'public/img/img_usuario/' . $foto . '?v=' . time() . '" 
                    class="img-thumbnail rounded-circle" 
                    style="width: 25px; height: 25px; object-fit: cover; cursor: pointer;" 
                    onclick="verImagen(' . $row["ID_usuario"] . ');"
                >
            ';

            $sub_array[] = $row["nom_usuario"];
            $sub_array[] = $row["ape_paterno"];
            $sub_array[] = $row["ape_materno"];
            $sub_array[] = $row["correo"];
            $sub_array[] = $row["telefono"];
            $sub_array[] = "Usuario";
            $sub_array[] = '<div class="action-buttons">
                                <button type="button" class="icon-button icon-button--edit" 
                                        onClick="editar(' . $row['ID_usuario'] . ');" 
                                        id="edit-' . $row['ID_usuario'] . '" 
                                        aria-label="Editar usuario">
                                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                </button>
                                <button type="button" class="icon-button icon-button--delete" 
                                        onClick="eliminar(' . $row['ID_usuario'] . ');" 
                                        id="delete-' . $row['ID_usuario'] . '" 
                                        aria-label="Eliminar usuario">
                                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                </button>
                            </div>
                        ';

            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        if (ob_get_length()) ob_clean();
        echo json_encode($results);
        break;
 
      
        case "eliminar_certificado":
            if (!empty($_POST["certificado_id"])) {
                $resultado = $usuario->delete_certificado($_POST["certificado_id"]);

                echo json_encode(["success" => $resultado > 0, "error" => $resultado == 0 ? "No se encontró el certificado o no se actualizó." : null]);
            } else {
                echo json_encode(["success" => false, "error" => "ID del certificado no proporcionado"]);
            }
            exit;
        
        
        


       case "mostrar_foto":
        $datos = $usuario->get_usuario_id($_POST["usu_id"]);
        if (is_array($datos)==true and count($datos)>0) {
            foreach($datos as $row){
                $output["foto"] = $row["foto"];
                $output["usu_nom"] = $row["nom_usuario"];

                // Agregar mensaje si no hay foto
                if (empty($row["foto"])) {
                    $output["mensaje"] = "El usuario no cuenta con una foto.";
                }
            }
            echo json_encode($output);
        }
        break;

        
        
        /*TODO: Creando Json segun el ID */
      case "mostrar":
    $datos = $usuario->get_usuario_id($_POST["usu_id"]);
    if (is_array($datos) == true and count($datos) <> 0) {
        foreach($datos as $row){
            $output["ID_usuario"] = $row["ID_usuario"];  // <-- esta línea
            $output["nom_usuario"] = $row["nom_usuario"]; 
            $output["ape_paterno"] = $row["ape_paterno"]; 
            $output["ape_materno"] = $row["ape_materno"];
            $output["sexo"] = $row["sexo"];
            $output["dni"] = $row["dni"]; 
            $output["telefono"] = $row["telefono"]; 
            $output["correo"] = $row["correo"];
            $output["foto"] = $row["foto"]; 
        }
        echo json_encode($output);
    }
    break;

        
    /*TODO: Listar todos los usuarios pertenecientes a un curso */
    case "listar_cursos_usuario":
    $cur_id = isset($_POST["cur_id"]) && !empty($_POST["cur_id"]) ? $_POST["cur_id"] : null;
    $datos = $usuario->get_cursos_usuario($cur_id); // Llama a nueva función mejorada
    $data = array();

    foreach ($datos as $row) {
        $sub_array = array();
        $sub_array[] = $row["nom_curso"];
        $sub_array[] = $row["nom_usuario"] . " " . $row["ape_paterno"] . " " . $row["ape_materno"];
        $sub_array[] = $row["fecha_emision"];
        $sub_array[] = $row["fecha_vencimiento"];
        $sub_array[] = $row["nom_instructor"] . " " . $row["inst_apep"];

        $sub_array[] = '<div class="btn-group">
            <button type="button" class="icon-button icon-button--info" 
                    onClick="certificado(' . $row["curd_id"] . ');" 
                    id="cert-' . $row["curd_id"] . '" 
                    aria-label="Generar certificado">
                <span class="material-symbols-outlined" aria-hidden="true">preview</span>
            </button>';

        $sub_array[] = '
        <button type="button" class="icon-button icon-button--danger" 
                    onClick="editar_certificado(' . $row["curd_id"] . ');" 
                    id="del-' . $row["curd_id"] . '" 
                    aria-label="Editar certificado">
                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
        </button>
        
        <button type="button" class="icon-button icon-button--danger" 
                    onClick="eliminar_certificado(' . $row["curd_id"] . ');" 
                    id="del-' . $row["curd_id"] . '" 
                    aria-label="Eliminar certificado">
                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
            </button>';

        $data[] = $sub_array;
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    );
    echo json_encode($results);
    break;



        case "usuarios_activos":
            $datos = $usuario->get_usuarios_activos();
            $data = array();
            foreach ($datos as $row) {
                $sub_array = array();
                $foto = !empty($row["foto"]) ? $row["foto"] : "default.png";
                $sub_array["foto"] = '<img src="'.Conectar::ruta().'public/img/img_AdGe/'.$foto.'" class="img-thumbnail rounded-circle" 
                                     style="width: 25px; height: 25px; object-fit: cover; cursor: pointer;" >';      
                $sub_array["nom_admin"] = $row["nom_admin"];
                $sub_array["ape_paterno"] = $row["ape_paterno"];
                $sub_array["ape_materno"] = $row["ape_materno"];
                $sub_array["correo"] = $row["correo"];
                $sub_array["telefono"] = $row["telefono"];
                $data[] = $sub_array;
            }
            echo json_encode($data);
            break;

    case "listar_detalle_usuario":
        $datos=$usuario->get_usuario_modal($_POST["cur_id"]);
        $data= Array();
        foreach($datos as $row){
            $sub_array = array();
            $foto = !empty($row["foto"]) ? $row["foto"] : "default.png";
            $sub_array[] = "<input type='checkbox' name='detallecheck[]' value='". $row["ID_usuario"] ."'>";
            $sub_array[] = '<img src="'.Conectar::ruta().'public/img/img_usuario/'.$foto.'" style="width: 25px; height: 25px; object-fit: cover;" class="rounded-circle"> '.$row["nom_usuario"];
            $sub_array[] = $row["ape_paterno"];
            $sub_array[] = $row["ape_materno"];
            $sub_array[] = $row["correo"];
            $data[] = $sub_array;
        }
        $results = array(
            "sEcho"=>1,
            "iTotalRecords"=>count($data),
            "iTotalDisplayRecords"=>count($data),
            "aaData"=>$data);
        echo json_encode($results);
        break;
        
        case "listar_admin":
    $datos = $usuario->get_administradores_con_permisos();
    $data = array();

    foreach ($datos as $row) {
        $sub_array = array();
        $foto = !empty($row["foto"]) ? $row["foto"] : "default.png";

        $sub_array[] = '
            <img 
                src="' . $ruta_base . 'public/img/img_AdGe/' . $foto . '?v=' . time() . '"
                class="img-thumbnail rounded-circle" 
                style="width: 25px; height: 25px; object-fit: cover; cursor: pointer;" 
                onclick="verImagen(' . $row["ID_administrador"] . ');"
            >
        ';
        $sub_array[] = $row["nom_admin"];
        $sub_array[] = $row["ape_paterno"] . " " . $row["ape_materno"];
        $sub_array[] = $row["correo"];
        $sub_array[] = $row["telefono"];
        $sub_array[] = $row["sexo"];

        $tipo_permiso = ($row["tipo_permiso"] == '1') ? 'Editor' : 'Lector';
        $sub_array[] = $tipo_permiso;
        $sub_array[] = $row["estado"];

        // Botones según estado
        $botones = '
                <button type="button" 
                class="icon-button icon-button--edit" 
                onClick="editar_admin(' . $row["ID_administrador"] . ');" 
                id="edit-' . $row["ID_administrador"] . '" 
                aria-label="Editar administrador">
            <span class="material-symbols-outlined" aria-hidden="true">edit</span>
        </button>
        ';

       if ($row["estado"] === "Activo") {
        $botones .= '
            <button type="button" 
                    class="icon-button icon-button--delete"
                    style="color: green;"
                    onClick="eliminar_admin(' . $row["ID_administrador"] . ');" 
                    id="delete-' . $row["ID_administrador"] . '" 
                    aria-label="Desactivar administrador">
                <span class="material-symbols-outlined" aria-hidden="true">toggle_on</span>
            </button>
        ';
        } else {
            $botones .= '
                <button type="button" 
                        class="icon-button icon-button--delete" 
                        onClick="restablecer_admin(' . $row["ID_administrador"] . ');" 
                        id="reset-' . $row["ID_administrador"] . '" 
                        aria-label="Activar administrador">
                    <span class="material-symbols-outlined" aria-hidden="true">toggle_off</span>
                </button>
            ';
        }


        $sub_array[] = '<div class="btn-group">' . $botones . '</div>';

        $data[] = $sub_array;
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    );

    if (ob_get_length()) ob_clean();
    echo json_encode($results);
    break;


    
        case "mostrar_admin":
            header('Content-Type: application/json');
            $ID_administrador = $_POST["ID_administrador"] ?? 0;
            $resultado = $usuario->mostrar_admin($ID_administrador);
            echo json_encode($resultado);
            break;
    

case "insert_update_admin":
    $admin_id = $_POST["ID_administrador"] ?? null;
    $estado = "Activo";

    // Validación de campos requeridos
    $campos_requeridos = ['nom_administrador', 'ape_paterno', 'ape_materno', 'sexo', 'correo', 'telefono', 'permiso'];
    foreach ($campos_requeridos as $campo) {
        if (empty($_POST[$campo])) {
            echo json_encode(["status" => "error", "message" => "El campo '$campo' es requerido"]);
            exit;
        }
    }

    // Validación de contraseña para nuevo registro
    if (empty($admin_id) && empty($_POST["password"])) {
        echo json_encode(["status" => "error", "message" => "La contraseña es requerida para nuevos administradores."]);
        exit;
    }

    $mensaje_final = "";
    $admin_id_generado = null;

    if (empty($admin_id)) {
        // Validar correo único
        if ($administrador->existe_correo($_POST["correo"])) {
            echo json_encode(["status" => "error", "message" => "El correo ya está registrado."]);
            exit;
        }

        // Insertar nuevo administrador
       $admin_id_generado = $administrador->insert_admin(
            $_POST["nom_administrador"],
            $_POST["ape_paterno"],
            $_POST["ape_materno"],
            $_POST["sexo"],
            $_POST["correo"],
            $_POST["telefono"],
            password_hash($_POST["password"], PASSWORD_DEFAULT) // ← CORRECTO
        );


        // Insertar permiso
        $administrador->insertar_o_actualizar_permiso($admin_id_generado, $_POST["permiso"]);
        $mensaje_final = "Administrador registrado correctamente";

    } else {
        // === EDICIÓN ===
        $datos_actuales = $administrador->get_administrador_id($admin_id);
        if (!is_array($datos_actuales) || count($datos_actuales) == 0) {
            echo json_encode(["status" => "error", "message" => "Administrador no encontrado."]);
            exit;
        }

        $datos_actuales = $datos_actuales[0];

        // Validar cambio de correo
        if ($_POST["correo"] !== $datos_actuales["correo"]) {
            if ($administrador->existe_correo($_POST["correo"])) {
                echo json_encode(["status" => "error", "message" => "El nuevo correo ya está registrado."]);
                exit;
            }
        }

        // Manejo de cambio de contraseña
        $password_actual = $_POST["password_actual"] ?? '';
        $password_nueva = $_POST["password_nueva"] ?? '';
        $password_nueva_repetir = $_POST["password_nueva_repetir"] ?? '';
        $password_a_usar = $datos_actuales["password"]; // Por defecto, se mantiene la misma si no se cambia

        // Si el usuario quiere cambiar su contraseña
        if (!empty($password_actual) || !empty($password_nueva) || !empty($password_nueva_repetir)) {
            if (empty($password_actual) || empty($password_nueva) || empty($password_nueva_repetir)) {
                echo json_encode(["status" => "error", "message" => "Todos los campos de cambio de contraseña son obligatorios."]);
                exit;
            }

            // Validar que la contraseña actual sea correcta
            if (!password_verify($password_actual, $datos_actuales["password"])) {
                echo json_encode(["status" => "error", "message" => "La contraseña actual no es correcta."]);
                exit;
            }

            // Validar que la nueva y la repetida coincidan
            if ($password_nueva !== $password_nueva_repetir) {
                echo json_encode(["status" => "error", "message" => "Las contraseñas nuevas no coinciden."]);
                exit;
            }

            // Hashear la nueva contraseña
            $password_a_usar = password_hash($password_nueva, PASSWORD_DEFAULT);
        }


        // Actualizar datos del administrador
        $administrador->update_admin(
            $admin_id,
            $_POST["nom_administrador"],
            $_POST["ape_paterno"],
            $_POST["ape_materno"],
            $_POST["sexo"],
            $_POST["correo"],
            $_POST["telefono"],
            $password_a_usar
        );

        // Actualizar permisos
        $administrador->insertar_o_actualizar_permiso($admin_id, $_POST["permiso"]);
        $admin_id_generado = $admin_id;
        $mensaje_final = "Administrador actualizado correctamente";
    }

    // === GUARDADO DE IMAGEN (común para ambos casos) ===
    if (!empty($_FILES["foto"]["name"])) {
        $extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $nombreArchivo = $admin_id_generado . "_Admin." . $extension;
        $rutaDestino = "../public/img/img_AdGe/" . $nombreArchivo;

        // Eliminar la imagen anterior si existe
        $imagenActual = $administrador->obtener_foto($admin_id_generado);
        if ($imagenActual) {
            $rutaImagenActual = "../public/img/img_AdGe/" . $imagenActual;
            if (file_exists($rutaImagenActual)) {
                unlink($rutaImagenActual);
            }
        }

        // Subir nueva imagen
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestino)) {
            $administrador->update_foto($admin_id_generado, $nombreArchivo);
        }
    }
     

    echo json_encode(["status" => "success", "message" => $mensaje_final]);
    exit;




   case "mostrar_admin_id":
    $datos = $administrador->get_administrador_id($_POST["ID_administrador"]);
    if (is_array($datos) == true && count($datos) > 0) {
        foreach ($datos as $row) {
            $output["ID_administrador"]     = $row["ID_administrador"];    
            $output["nom_admin"]    = $row["nom_admin"]; 
            $output["ape_paterno"]   = $row["ape_paterno"]; 
            $output["ape_materno"]   = $row["ape_materno"]; 
            $output["telefono"]   = $row["telefono"]; 
            $output["correo"] = $row["correo"];
            $output["sexo"]        = $row["sexo"];
            $output["password"]    = $row["password"];
            $output["tipo_permiso"] = $row["tipo_permiso"];
            $output["foto"]        = $row["foto"]; 
        }
        echo json_encode($output);
    }
    break;
    
    case "restablecer_admin":
        if (!empty($_POST["ID_administrador"])) {
            $resultado = $administrador->restablecer_administrador($_POST["ID_administrador"]);
            echo json_encode(["success" => $resultado > 0]);
        } else {
            echo json_encode(["success" => false]);
        }
        break;

    // Puedes agregar más casos como listar, eliminar, obtener datos por ID, etc.

    default:
        echo json_encode(["status" => "error", "message" => "Operación no válida"]);
        break;


    
    /*************************************************************************************************************************************************** */
            
    case "mostrar_perfil_admin":
    $datos = $usuario->get_administrador_id($_POST["ID_administrador"]);
    echo json_encode($datos); 
    break;

    case "editar_perfil_admin":
    $ID_administrador = $_POST["id_admin"] ?? null;

    if ($ID_administrador) {
        // Actualizar los datos del administrador en la base de datos
        $actualizado = $usuario->update_administrador_id(
            $ID_administrador,
            trim($_POST["DatosPerfil_nom"] ?? ''),
            trim($_POST["DatosPerfil_apep"] ?? ''),
            trim($_POST["DatosPerfil_apem"] ?? ''),
            $_POST["DatosPerfil_sexo"] ?? '',
            trim($_POST["DatosPerfil_correo"] ?? ''),
            trim($_POST["DatosPerfil_telf"] ?? '')
        );

        $mensaje = $actualizado ? "Perfil actualizado correctamente" : "No se realizaron cambios";

        // Procesar la imagen si se ha subido una nueva
        $nombreArchivo = null;
        if (!empty($_FILES["foto_adminperfil"]["name"])) {
            $extension = strtolower(pathinfo($_FILES["foto_adminperfil"]["name"], PATHINFO_EXTENSION));
            $nombreArchivo = $ID_administrador . "_Admin." . $extension;
            $rutaDestino = "../public/img/img_AdGe/" . $nombreArchivo;

            // Obtener y eliminar imagen actual
            $imagenActual = $usuario->obtener_foto_admin($ID_administrador);
            if ($imagenActual) {
                $rutaImagenActual = "../public/img/img_AdGe/" . $imagenActual;
                if (file_exists($rutaImagenActual)) {
                    unlink($rutaImagenActual);
                }
            }

            // Subir nueva imagen y actualizar en DB
            if (move_uploaded_file($_FILES["foto_adminperfil"]["tmp_name"], $rutaDestino)) {
                $usuario->update_foto_admin($ID_administrador, $nombreArchivo);
            }
        }
          // === ACTUALIZAR CONTRASEÑA SI APLICA ===
        $password_actual = $_POST["password_actual"] ?? '';
        $nueva_password = $_POST["nueva_password"] ?? '';
        $repite_password = $_POST["repite_password"] ?? '';

        if (!empty($password_actual) || !empty($nueva_password) || !empty($repite_password)) {
            // Validar que todos los campos estén llenos
            if (empty($password_actual) || empty($nueva_password) || empty($repite_password)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Para cambiar la contraseña, debes completar los tres campos."
                ]);
                exit;
            }

            // Validar que las nuevas contraseñas coincidan
            if ($nueva_password !== $repite_password) {
                echo json_encode([
                    "success" => false,
                    "message" => "La nueva contraseña y su repetición no coinciden."
                ]);
                exit;
            }

            // Verificar contraseña actual
            if (!$usuario->verificarPassword($ID_administrador, $password_actual)){
                echo json_encode([
                    "success" => false,
                    "message" => "La contraseña actual es incorrecta."
                ]);
                exit;
            }

            // Cambiar la contraseña
            $usuario->cambiarPassword($ID_administrador, $nueva_password);
        }

        // ✅ ACTUALIZAR DATOS EN LA SESIÓN
        if ($actualizado) {
            $_SESSION["nom_admin"]     = $_POST["DatosPerfil_nom"];
            $_SESSION["ape_paterno"]   = $_POST["DatosPerfil_apep"];
            $_SESSION["ape_materno"]   = $_POST["DatosPerfil_apem"];
            $_SESSION["sexo"]          = $_POST["DatosPerfil_sexo"];
            $_SESSION["correo"]        = $_POST["DatosPerfil_correo"];
            $_SESSION["telefono"]      = $_POST["DatosPerfil_telf"];

            if (!empty($nombreArchivo)) {
                $_SESSION["foto"] = $nombreArchivo;
            }
        }

        echo json_encode([
            "success" => true,
            "message" => $mensaje
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "ID de administrador no recibido"
        ]);
    }
    break;

      case "delete_admin":
        if (!empty($_POST["ID_administrador"])) {
            $resultado = $administrador->delete_admin($_POST["ID_administrador"]);
            echo json_encode(["success" => $resultado > 0]);
        } else {
            echo json_encode(["success" => false]);
        }
        exit;

  case "restablecer_admin":
    if (!empty($_POST["ID_administrador"])) {
        $resultado = $administrador->restablecer_administrador($_POST["ID_administrador"]);
        echo json_encode(["success" => $resultado > 0]);
    } else {
        echo json_encode(["success" => false]);
    }
    exit;

   case "notificaciones":
    $data = $administrador->listar_logs(10); // Últimos 10 logs para notificaciones
    echo json_encode($data);
    break;

case "historial":
    $datos = $administrador->get_historial(); // Asegúrate de que este método usa el JOIN con nom_admin
    $data = array();

    foreach ($datos as $row) {
        $sub_array = array();

        $sub_array[] = $row["ID_logs"];
        $sub_array[] = !empty($row["nom_admin"]) ? $row["nom_admin"] : "Gerente";
        $sub_array[] = $row["tabla_afectada"];
        $sub_array[] = $row["registro"];
        $sub_array[] = $row["accion"];
        $sub_array[] = !empty($row["old_data"]) ? $row["old_data"] 
        : '<span style="color: gray;">No existen datos anteriores</span>';
        $sub_array[] = $row["new_data"];
        $sub_array[] = $row["fecha"];

        $data[] = $sub_array;
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    );

    if (ob_get_length()) ob_clean();
    echo json_encode($results);
    break;

case "mostrar_certificado":     
    $datos = $usuario->get_certificado_id($_POST["curd_id"]);     

    if (is_array($datos) && count($datos) > 0) {
        foreach ($datos as $row) {
            $output["ID_certificado"] = $row["ID_certificado"];
            $output["ID_curso"] = $row["ID_curso"];
            $output["ID_usuario"] = $row["ID_usuario"];
            $output["fecha_emision"] = $row["fecha_emision"];
            $output["fecha_vencimiento"] = $row["fecha_vencimiento"];
            $output["nota"] = $row["nota"];

            
        }
        echo json_encode($output);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Certificado no encontrado"]);
    }
    break;

     case "combo":
        $datos = $usuario->get_usuario();
        if (is_array($datos) && count($datos) > 0) {
            $html = "<option value='' disabled selected>Seleccione un curso</option>";
            foreach ($datos as $row) {
               $html .= "<option value='" . $row['ID_usuario'] . "'>" . $row['nom_usuario'] . " " . $row['ape_paterno'] . "</option>";
            }
            echo $html;
        }
        break;

    
}
?>