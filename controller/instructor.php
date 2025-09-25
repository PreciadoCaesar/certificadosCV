<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Instructor.php'; 

$instructor = new Instructor();
$ruta_base = Conectar::ruta();

$user_id = isset($_SESSION["ID_administrador"]) 
    ? $_SESSION["ID_administrador"] 
    : (isset($_SESSION["ID_gerente"]) ? $_SESSION["ID_gerente"] : 0);
    
switch ($_GET["op"]) {
    
   case "guardaryeditar":
    $inst_id = $_POST["inst_id"] ?? null;
    $correo = trim($_POST["inst_correo"]);

    // Validación de correo obligatorio
    if (empty($correo)) {
        echo json_encode(["status" => "error", "message" => "El correo es obligatorio."]);
        exit;
    }

    // Si es inserción
    if (empty($inst_id)) {
        // Verificar si el correo ya existe
        if ($instructor->correo_existe($correo)) {
            echo json_encode(["status" => "error", "message" => "El correo ya está registrado."]);
            exit;
        }

        $inst_id = $instructor->insert_instructor(
            $_POST["inst_nom"], 
            $_POST["inst_apep"], 
            $_POST["inst_apem"], 
            $correo, 
            $_POST["inst_telf"]
        );

        $mensaje = "Instructor registrado correctamente";

    } else {
        // Validar si el correo ya existe en otro instructor
        if ($instructor->correo_existe_en_otros($correo, $inst_id)) {
            echo json_encode(["status" => "error", "message" => "El correo ya está registrado en otro instructor."]);
            exit;
        }

        $actualizado = $instructor->update_instructor(
            $inst_id, 
            $_POST["inst_nom"], 
            $_POST["inst_apep"], 
            $_POST["inst_apem"], 
            $correo, 
            $_POST["inst_telf"]
        );

        $mensaje = $actualizado ? "Instructor actualizado correctamente" : "No se realizaron cambios";
    }

    // Procesar imagen (si se subió)
    if (!empty($_FILES["foto"]["name"])) {
        $extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $nombreArchivo = $inst_id . "_instructor." . $extension;
        $rutaDestino = "../public/img/img_instructor/" . $nombreArchivo;

        // Eliminar la imagen anterior si existe
        $imagenActual = $instructor->obtener_foto($inst_id);
        if ($imagenActual) {
            $rutaImagenActual = "../public/img/img_instructor/" . $imagenActual;
            if (file_exists($rutaImagenActual)) {
                unlink($rutaImagenActual);
            }
        }

        // Subir nueva imagen
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestino)) {
            $instructor->update_foto($inst_id, $nombreArchivo);
        }
    }

    echo json_encode(["status" => "success", "message" => $mensaje]);
    break;


        /*TODO: Eliminar segun ID */
        case "eliminar":
            if (!empty($_POST["ID_instructor"])) {
                $resultado = $instructor->delete_instructor($_POST["ID_instructor"]);
                echo json_encode(["success" => $resultado > 0]);
            } else {
                echo json_encode(["success" => false]);
            }
            exit;
        
        
        /*TODO:  Listar toda la informacion segun formato de datatable */
        case "listar":
            $datos = $instructor->get_instructor();
            $data = array();
        
            foreach ($datos as $row) {
                $sub_array = array();
                $foto = !empty($row["foto"]) ? $row["foto"] : "default.png";
                $timestamp = time(); // Genera un número único basado en el tiempo actual
                $sub_array[] = '
                    <img 
                        src="' . $ruta_base . 'public/img/img_instructor/' . $foto . '?t=' . $timestamp . '" 
                       class="img-thumbnail rounded-circle" 
                        style="width: 25px; height: 25px; object-fit: cover; cursor: pointer;"
                        onclick="verImagenInstructor(' . $row["ID_instructor"] . ');"
                    >
                ';
                $sub_array[] = $row["nom_instructor"];
                $sub_array[] = $row["ape_paterno"];
                $sub_array[] = $row["ape_materno"];
                $sub_array[] = $row["correo"];
                $sub_array[] = $row["telefono"];
        
                $sub_array[] = '
                                <div class="action-buttons">
                                    <button type="button" class="icon-button icon-button--edit" 
                                            onClick="editar(' . $row["ID_instructor"] . ');" 
                                            id="edit-' . $row["ID_instructor"] . '" 
                                            aria-label="Editar instructor">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                    </button>
                                    <button type="button" class="icon-button icon-button--delete" 
                                            onClick="eliminar(' . $row["ID_instructor"] . ');" 
                                            id="delete-' . $row["ID_instructor"] . '" 
                                            aria-label="Eliminar instructor">
                                        <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                    </button>
                                </div>
                            ';
        
                $data[] = $sub_array;
            }

            $results = array(
                "sEcho"=>1,
                "iTotalRecords"=>count($data),
                "iTotalDisplayRecords"=>count($data),
                "aaData"=>$data);
            echo json_encode($results);
            break;
            
        
        
        
        /*TODO:  Listar toda la informacion segun formato de datatable */
        case "combo":
        $datos = $instructor->get_instructor();
        if (is_array($datos) && count($datos) > 0) {
            $html = "<option value='' selected disabled>Seleccione un instructor</option>"; // opción vacía al inicio
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['ID_instructor'] . "'>" . $row['nom_instructor'] . "</option>";
            }
            echo $html;
        }
        break;

        case"mostrar_foto":
            $datos = $instructor->get_instructor_id($_POST["inst_id"]);
            if (is_array($datos)==true and count($datos)>0) {
                foreach($datos as $row){
                    $output["foto"] = $row["foto"];
                    $output["inst_nom"] = $row["nom_instructor"];
                }
                echo json_encode($output);
            }
            break;
        
        
        /*TODO: Creando Json segun el ID */
        case "mostrar":
            $datos = $instructor->get_instructor_id($_POST["inst_id"]);
            if (is_array($datos)==true and count($datos)<>0) {
                foreach($datos as $row){
                $output["inst_id"] = $row["ID_instructor"];    
                $output["inst_nom"] = $row["nom_instructor"]; 
                $output["inst_apep"] = $row["ape_paterno"]; 
                $output["inst_apem"] = $row["ape_materno"]; 
                $output["inst_telf"] = $row["telefono"]; 
                $output["inst_correo"] = $row["correo"];
                $output["foto"] = $row["foto"]; 
                }
                echo json_encode($output);
            } break;
        

            case "nuevos_instructores":
                // Obtenemos los últimos 10 instructores activos
                $datos = $instructor->get_last_10_instructores();
                $data = array();
                foreach ($datos as $row) {
                    $sub_array = array();
                    // Si no hay foto, usamos default
                    $foto = !empty($row["foto"]) ? $row["foto"] : "default.png";
                    // Puedes concatenar la imagen con el nombre o separarlos
                    $sub_array["foto"] = '<img src="'.Conectar::ruta().'public/img/img_instructor/'.$foto.'" class="img-thumbnail rounded-circle" 
                                     style="width: 25px; height: 25px; object-fit: cover; cursor: pointer;">';
                    $sub_array["nom_instructor"] = $row["nom_instructor"];
                    $sub_array["ape_paterno"] = $row["ape_paterno"];
                    $sub_array["ape_materno"] = $row["ape_materno"];
                    $sub_array["correo"] = $row["correo"];
                    $sub_array["telefono"] = $row["telefono"];
                    $data[] = $sub_array;
                }
                echo json_encode($data);
                break;
            
    }



?>
