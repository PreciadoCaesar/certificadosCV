<?php
    require_once __DIR__ . '/../config/conexion.php';
    require_once __DIR__ . '/../models/Curso.php';
    require_once __DIR__ . '/../models/Usuario.php';
    require_once __DIR__ . '/../models/Instructor.php';


    $curso = new Curso();
    $usuario = new Usuario();
    $instructor = new Instructor();
    $ruta_base = Conectar::ruta();

    $admin_id = isset($_SESSION["ID_administrador"]) 
    ? $_SESSION["ID_administrador"] 
    : (isset($_SESSION["ID_gerente"]) ? $_SESSION["ID_gerente"] : 0);
    
    /* TODO: Opción de solicitud del controller */
    switch ($_GET["op"]) {
        
  case "guardaryeditar":
    $cur_id = $_POST["cur_id"] ?? null;
    $fecha_inicio = $_POST["inicio_curso"];
    $fecha_final = $_POST["final_curso"];

    // Validación de fechas
    if (strtotime($fecha_inicio) > strtotime($fecha_final)) {
        echo json_encode(["status" => "error", "message" => "La fecha de inicio no puede ser mayor que la fecha de finalización."]);
        exit;
    }

    if (empty($cur_id)) {
        // Registrar nuevo curso
        $cur_id = $curso->insert_curso($_POST["SelectCategoria"], $_POST["nom_curso"], $_POST["inicio_curso"], $_POST["final_curso"], $_POST["SelectInstructor"],$_POST["horas_curso"]);
        $mensaje = "Curso registrado correctamente";
    } else {
        // Editar curso existente
        $curso->update_curso($cur_id, $_POST["SelectCategoria"], $_POST["nom_curso"], $_POST["inicio_curso"], $_POST["final_curso"], $_POST["SelectInstructor"],$_POST["horas_curso"]);
        $mensaje = "Curso actualizado correctamente";
    }

    // Subida de imagen
    if (!empty($_FILES["foto"]["name"])) {
        $extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $nombreArchivo = $cur_id . "_curso." . $extension;
        $rutaDestino = "../public/img/img_curso/" . $nombreArchivo;

        // Eliminar la imagen anterior si existe
        $imagenActual = $curso->obtener_foto($cur_id);
        if ($imagenActual) {
            $rutaImagenActual = "../public/img/img_curso/" . $imagenActual;
            if (file_exists($rutaImagenActual)) {
                unlink($rutaImagenActual);
            }
        }

        // Subir nueva imagen
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestino)) {
            $curso->update_foto($cur_id, $nombreArchivo); // Aquí se usa el objeto correcto
        }
    }

    echo json_encode(["status" => "success", "message" => $mensaje]);
    exit;

    

        /* TODO: Obtener curso por ID */
        case "mostrar":
            if (!empty($_POST["cur_id"])) {
                $datos = $curso->get_curso_id($_POST["cur_id"]);
                echo json_encode($datos[0] ?? ["error" => "No se encontraron datos"], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["error" => "ID de curso no recibido"]);
            }
            exit;
        

        /* TODO: Eliminar curso por ID */
        case "eliminar":
            if (isset($_POST["cur_id"])) {
                $resultado = $curso->delete_curso_usuario($_POST["cur_id"]);
                
                if ($resultado > 0) {
                    echo json_encode(["status" => "success", "message" => "Curso eliminado"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se encontró el curso"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "ID no recibido"]);
            }
            break;
        

        /* TODO: Listar toda la información en formato DataTable */
        case "listar":
            $datos = $curso->get_curso();
            $data = array();

            foreach ($datos as $row) {

                // Obtener la ruta de la imagen del curso y del instructor
                $foto = !empty($row["curso_foto"]) ? $row["curso_foto"] : "default.png";
                $foto_instructor = !empty($row["instructor_foto"]) ? $row["instructor_foto"] : "default.png";
                $timestamp = time(); 
                
                // Verificar si el curso tiene un temario asociado
                $temarioExiste = $curso->existeTemario($row["ID_curso"]);
                $temarioIcono = $temarioExiste
                ? '<span class="material-symbols-outlined" style="color: green;" title="Temario disponible">visibility</span>'
                : '<span class="material-symbols-outlined" title="Sin temario">assignment_add</span>';

                $fondoExiste = existeFondoCertificado($row["ID_curso"]);
                $fondoIcono = $fondoExiste
                ? '<span class="material-symbols-outlined" style="color: green;" title="Temario disponible">wallpaper_slideshow</span>'
                : '<span class="material-symbols-outlined"  title="Sin temario">wallpaper_slideshow</span>';


                
                $data[] = array(
                '
                    <img 
                        src="' . $ruta_base . 'public/img/img_curso/' . $foto . '?t=' . $timestamp . '" 
                        class="img-thumbnail rounded-circle" 
                        style="width: 25px; height: 25px; object-fit: cover; cursor: pointer;"
                        onclick="verImagenCurso(' . $row["ID_curso"] . ')"
                    >
                ',
                $row["categoria_nombre"],
                $row["nom_curso"],
                $row["fecha_inicio"],
                $row["fecha_fin"],
                '<img src="' . $ruta_base . 'public/img/img_instructor/' . $foto_instructor . '?t=' . $timestamp . '" 
                    class="img-thumbnail rounded-circle" 
                    style="width:25px; height:25px; object-fit:cover;" 
                    title="' . htmlspecialchars($row["nom_instructor"] . ' ' . $row["ape_paterno"] . ' ' . $row["ape_materno"]) . '"> ' 
                . htmlspecialchars($row["nom_instructor"] . ' ' . $row["ape_paterno"] . ' ' . $row["ape_materno"]),

                '<div class="action-buttons">
                                <button type="button" class="icon-button icon-button--edit" 
                                        onClick="editar(' . $row["ID_curso"] . ');" 
                                        id="edit-' . $row["ID_curso"] . '" 
                                        aria-label="Editar curso">
                                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                </button>
                                <button type="button" class="icon-button icon-button--delete" 
                                        onClick="eliminar(' . $row["ID_curso"] . ');" 
                                        id="delete-' . $row["ID_curso"] . '" 
                                        aria-label="Eliminar curso">
                                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                </button>
                            </div>',
                            
                            '<button type="button" class="icon-button icon-button--delete" 
                                onClick="abrirModalCertificado(' . $row["ID_curso"] . ');" 
                                aria-label="Ver certificado">' . $fondoIcono . '
                            </button>',
               '<button type="button" onClick="abrirModalDocumento(' . $row["ID_curso"] . ', \'' . addslashes($row["nom_curso"]) . '\');" class="icon-button icon-button--delete">'
    . $temarioIcono .
    '</button>' 

);
            }

            echo json_encode(array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            ));
            break;

        /* TODO: Listar cursos en un combo */
        case "combo":
        $datos = $curso->get_curso();
        if (is_array($datos) && count($datos) > 0) {
            // Agrega selected a la opción deshabilitada para que sea la seleccionada por defecto
            $html = "<option value='' disabled selected>Seleccione un curso</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['ID_curso'] . "'>" . $row['nom_curso'] . "</option>";
            }
            echo $html;
        }
        break;

         case "combo_edit":
        $datos = $curso->get_curso_edit();
        if (is_array($datos) && count($datos) > 0) {
            // Agrega selected a la opción deshabilitada para que sea la seleccionada por defecto
            $html = "<option value='' disabled selected>Seleccione un curso</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['ID_curso'] . "'>" . $row['nom_curso'] . "</option>";
            }
            echo $html;
        }
        break;


        /* TODO: Eliminar curso de usuario */
        case "eliminar_curso_usuario":
            if (isset($_POST["curd_id"])) {
                $curso->delete_curso_usuario($_POST["curd_id"]);
            }
            break;

        /* TODO: Insertar detalle de curso usuario */
        case "insert_curso_usuario":
        $datos = json_decode($_POST['usu_id'], true); // ✅ decodificar JSON correctamente
        $data = Array();

        foreach($datos as $row){
            $sub_array = array();
            $idx = $curso->insert_curso_usuario($_POST["cur_id"], $row);
            $sub_array[] = $idx;
            $data[] = $sub_array;
        }

        echo json_encode($data);
        break;


        /* TODO: Generar código QR */
        case "generar_qr":
            require 'phpqrcode/qrlib.php';
            if (isset($_POST["curd_id"])) {
                QRcode::png(conectar::ruta()."view/Certificado/index.php?curd_id=".$_POST["curd_id"], "../public/qr/".$_POST["curd_id"].".png", 'L', 32, 5);
            }
            break;

        /* TODO: Actualizar imagen del curso */
        case "update_imagen_curso":
            if (isset($_POST["curx_idx"]) && isset($_POST["cur_img"])) {
                $curso->update_imagen_curso($_POST["curx_idx"], $_POST["cur_img"]);
            }
            break;

        /* TODO: Obtener los 10 últimos cursos */
      case "get_last_10":
        $datos = $curso->get_last_10_cursos();

        $response = array();
        foreach ($datos as $row) {
            $id_curso = $row["ID_curso"];
            $nombre_curso = $row["nom_curso"];

            $temarioExiste = $curso->existeTemario($id_curso);
                $temarioIcono = $temarioExiste
                ? '<span class="material-symbols-outlined" style="color: green;" title="Temario disponible">visibility</span>'
                : '<span class="material-symbols-outlined">visibility_off</span>';

            $nombre_archivo = $row["temario"];
            $foto = !empty($row["foto"]) ? $row["foto"] : "default.png";
            $sub_array = array();
            $sub_array["foto"] = '<img src="' . Conectar::ruta() . 'public/img/img_curso/' . $foto . '" class="img-thumbnail rounded-circle" 
                                     style="width: 25px; height: 25px; object-fit: cover;">';
            $sub_array["cur_nom"] = $row["nom_curso"];
            $sub_array["cur_fechini"] = $row["fecha_inicio"];
            $sub_array["cur_fechfin"] = $row["fecha_fin"];
            $sub_array["inst_nom"] = '<img src="' . Conectar::ruta() . 'public/img/img_instructor/' . $row["foto_instructor"] . '" class="img-thumbnail rounded-circle" 
                                     style="width: 25px; height: 25px; object-fit: cover;">&nbsp;&nbsp;' . $row["nom_instructor"];

            $sub_array["certificado"] = '<button onClick="descargarDocumento(\'' . $nombre_archivo . '\')" type="button" class="icon-button icon-button--delete">' . $temarioIcono . '</button>';

            $response[] = $sub_array;
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        break;


            case "mostrar_foto":
                $datos = $curso->get_curso_id($_POST["curso_id"]);
                if (!empty($datos)) {
                    foreach ($datos as $row) {
                        $output["foto"] = $row["foto"] ?? "";
                        $output["curso_nom"] = $row["nom_curso"];
                    }
                    echo json_encode($output);
                }
                break;
            
                case 'guardar_certificado':
                    if (isset($_FILES['certificado']) && $_FILES['certificado']['error'] == 0) {
                        $curso = new Curso();
                
                        $id_curso = $_POST['id_curso'];
                        $certificado = $_FILES['certificado'];
                
                        // Obtener la extensión del archivo original
                        $extension = pathinfo($certificado['name'], PATHINFO_EXTENSION);
                        $extension = strtolower($extension);
                
                        // Nombre nuevo: ID_curso_curso_certificado.jpg/png
                        $nombre_archivo = $id_curso . '_curso_certificado.' . $extension;
                        $directorio = '../public/img/certificado/';
                        $ruta_archivo = $directorio . $nombre_archivo;
                
                        // Eliminar imagen anterior si existe con cualquier extensión
                        foreach (['jpg', 'jpeg', 'png'] as $ext) {
                            $archivo_existente = $directorio . $id_curso . '_curso_certificado.' . $ext;
                            if (file_exists($archivo_existente)) {
                                unlink($archivo_existente);
                            }
                        }
                
                        // Mover archivo nuevo
                        if (move_uploaded_file($certificado['tmp_name'], $ruta_archivo)) {
                            $resultado = $curso->guardarCertificado($id_curso, $nombre_archivo);
                
                            echo json_encode(
                                $resultado
                                    ? ["status" => "success", "message" => "Certificado guardado correctamente"]
                                    : ["status" => "error", "message" => "Error al guardar en BD"]
                            );
                        } else {
                            echo json_encode(["status" => "error", "message" => "Error al mover el archivo"]);
                        }
                    } else {
                        echo json_encode(["status" => "error", "message" => "Archivo no válido o no enviado"]);
                    }
                    break;


                   case 'guardar_documento':
                    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === 0) {
                        $id_curso = $_POST['id_curso'];
                        $documento = $_FILES['documento'];

                        // Extensión del archivo original
                        $extension = strtolower(pathinfo($documento['name'], PATHINFO_EXTENSION));
                        $ext_permitidas = ['pdf', 'ppt', 'pptx'];

                        if (!in_array($extension, $ext_permitidas)) {
                            echo json_encode(["status" => "error", "message" => "Extensión de archivo no permitida"]);
                            exit;
                        }

                        // Crear nombre de archivo único y seguro
                        $nombre_documento = 'temario_' . uniqid('', true) . '.' . $extension;

                        $directorio = '../public/temarios/';
                        $ruta_archivo = $directorio . $nombre_documento;

                        // Eliminar versiones anteriores del archivo asociadas al curso
                        $patron = $directorio . $id_curso . '_*' . '_temario.*';
                        foreach (glob($patron) as $archivo_existente) {
                            if (is_file($archivo_existente)) {
                                unlink($archivo_existente);
                            }
                        }

                        // Subir nuevo documento
                        if (move_uploaded_file($documento['tmp_name'], $ruta_archivo)) {
                            $resultado = $curso->guardarDocumento($id_curso, $nombre_documento);

                            if ($resultado) {
                                echo json_encode([
                                    "status" => "success",
                                    "message" => "Documento guardado correctamente",
                                    "archivo" => $nombre_documento
                                ]);
                            } else {
                                // Si falla la BD, eliminar el archivo
                                if (file_exists($ruta_archivo)) {
                                    unlink($ruta_archivo);
                                }
                                echo json_encode([
                                    "status" => "error",
                                    "message" => "Error al guardar en base de datos"
                                ]);
                            }
                        } else {
                            echo json_encode(["status" => "error", "message" => "Error al mover el archivo"]);
                        }
                    } else {
                        echo json_encode(["status" => "error", "message" => "Archivo no válido o no enviado"]);
                    }
                    break;




                 case "descargarDocumento":
                    if (isset($_GET['archivo'])) {
                        $archivo = basename($_GET['archivo']); // Seguridad: evita rutas relativas
                        $ruta = "../public/temarios/" . $archivo;

                        if (file_exists($ruta)) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime = finfo_file($finfo, $ruta);
                            finfo_close($finfo);

                            header('Content-Type: ' . $mime);
                            header('Content-Disposition: inline; filename="' . $archivo . '"');
                            header('Content-Length: ' . filesize($ruta));
                            readfile($ruta);
                            exit;
                        } else {
                            http_response_code(404);
                            echo "Archivo no encontrado.";
                        }
                    } else {
                        http_response_code(400);
                        echo "Nombre de archivo no especificado.";
                    }
                    break;




                            case 'contadoresDashboard':
                                $data = [
                                    "cursos" => $curso->contarCursos(), 
                                    "instructores" => $instructor->contarUltimosInstructoresActivos(),
                                    "usuarios" => $usuario->contarUsuariosActivos()
                                ];
                                echo json_encode($data);
                                break;

                        case 'verificarDocumento':
                        if (isset($_POST['archivo'])) {
                            $archivo = basename($_POST['archivo']);
                            $ruta = "../public/temarios/" . $archivo;

                            if (file_exists($ruta)) {
                                echo json_encode(["existe" => true]);
                            } else {
                                echo json_encode(["existe" => false]);
                            }
                        } else {
                            echo json_encode(["error" => "Archivo no especificado"]);
                        }
                        break;



                          case 'mostrarCurso':
                                $datos = $curso->MostrarCurso($_POST["ID_curso"]);
                            
                                if (is_array($datos) && count($datos) > 0) {
                                    foreach ($datos as $row) {
                                        $output["ID_curso"] = $row["ID_curso"];
                                        $output["nom_curso"] = $row["nom_curso"];
                                        $output["ruta_certificado"] = $row["ruta_certificado"];
                                        $output["fecha_inicio"] = $row["fecha_inicio"];
                                        $output["fecha_fin"] = $row["fecha_fin"];
                                        $output["nombre_instructor"] = $row["nombre_instructor"];
                                    }
                            
                                    echo json_encode($output, JSON_UNESCAPED_UNICODE);
                                }
                                break;


                                case 'verificar_documento':
                                    $id_curso = $_POST['id_curso'];

                                    // Obtener nombre del archivo desde la BD
                                    $documento_bd = $curso->VerificarDocumento($id_curso);

                                    if ($documento_bd) {
                                        $ruta_archivo = "../public/temarios/" . $documento_bd;

                                        // Verificar si el archivo existe físicamente
                                        if (file_exists($ruta_archivo)) {
                                            echo json_encode([
                                                'status' => 'success',
                                                'archivo' => $documento_bd
                                            ]);
                                        } else {
                                            echo json_encode([
                                                'status' => 'error',
                                                'message' => 'El archivo no existe en el directorio'
                                            ]);
                                        }
                                    } else {
                                        echo json_encode([
                                            'status' => 'error',
                                            'message' => 'No se encontró archivo en la base de datos'
                                        ]);
                                    }
                                    break;

                                    case 'guardar_url_certificado':
                                    if (isset($_POST["curd_id"]) && isset($_POST["url_certificado"])) {
                                        $curso->guardar_url_certificado($_POST["curd_id"], $_POST["url_certificado"]);
                                        echo json_encode([
                                            "status" => "success",
                                            "message" => "URL del certificado guardada correctamente"
                                        ]);
                                    } else {
                                        echo json_encode([
                                            "status" => "error",
                                            "message" => "Datos incompletos para guardar la URL"
                                        ]);
                                    }
                                    break;

                                    
                
                            }       

                       function existeFondoCertificado($id_curso) {
                        $directorioRelativo = __DIR__ . '/../public/img/certificado/';

                        $extensiones = ['jpg', 'jpeg', 'png'];

                        foreach ($extensiones as $ext) {
                            $archivo = $directorioRelativo . $id_curso . '_curso_certificado.' . $ext;
                            if (file_exists($archivo)) {
                                return true;
                            }
                        }
                        return false;
                    }




?>
                        