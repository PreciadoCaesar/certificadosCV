<?php
    require_once __DIR__ . '/../config/conexion.php';
    require_once __DIR__ . '/../models/Categoria.php';

    $categoria = new Categoria();
    $ruta_base = Conectar::ruta();

    $user_id = isset($_SESSION["ID_administrador"]) 
        ? $_SESSION["ID_administrador"] 
        : (isset($_SESSION["ID_gerente"]) ? $_SESSION["ID_gerente"] : 0);

    /*TODO: Opción de solicitud del controlador */
    switch($_GET["op"]){
       
       
        /*TODO: Guardar y editar cuando se tenga el ID */
        case "guardaryeditar":
            $cat_id = !empty($_POST["cat_id"]) ? $_POST["cat_id"] : null;
            $cat_nom = trim($_POST["nombre"]);

            if (empty($cat_nom)) {
                echo json_encode(["status" => "error", "message" => "El nombre de la categoría es obligatorio."]);
                exit;
            }

            // Verificar si el nombre ya existe en otra categoría
            if (is_null($cat_id)) {
                // Insertar nueva categoría
                if ($categoria->nombre_existe($cat_nom)) {
                    echo json_encode(["status" => "error", "message" => "El nombre de la categoría ya existe."]);
                    exit;
                }

                $resultado = $categoria->insert_categoria($cat_nom);
                $mensaje = $resultado > 0 ? "Categoría registrada correctamente." : "No se pudo registrar la categoría.";
            } else {
                // Actualizar categoría existente
                if ($categoria->nombre_existe_en_otros($cat_nom, $cat_id)) {
                    echo json_encode(["status" => "error", "message" => "El nombre de la categoría ya está en uso por otra."]);
                    exit;
                }

                $resultado = $categoria->update_categoria($cat_id, $cat_nom);
                $mensaje = $resultado > 0 ? "Categoría actualizada correctamente." : "No se realizaron cambios.";
            }

            echo json_encode([
                "status" => $resultado !== false ? "success" : "error",
                "message" => $mensaje
            ]);
            break;

        
        
        //Mostrar el nombre de la categoria cuando presionan en editar para mostrar en el modal
            case "editar":
                if (!empty($_POST["ID_categoria"])) { 
                    $resultado = $categoria->update_categoria($_POST["ID_categoria"], $_POST["nombre"]);
                    $mensaje = "Categoría actualizada correctamente.";
                    
                    echo json_encode(["status" => "success", "message" => $mensaje]);
                } else {
                    echo json_encode(["status" => "error", "message" => "ID de categoría vacío."]);
                }
                break;
            

        /*TODO: Creando JSON según el ID */
        case "mostrar":
            $datos = $categoria->get_categoria_id($_POST["cat_id"]); 
            if (is_array($datos) && count($datos) > 0) {
                $output["cat_id"] = $datos["ID_categoria"]; 
                $output["cat_nom"] = $datos["nombre"]; 
                echo json_encode($output);
            }
            break;
        

      /*TODO: Eliminar según ID */
      case "eliminar":
    if (!empty($_POST["ID_categoria"])) {
        $resultado = $categoria->delete_categoria($_POST["ID_categoria"]);
        echo json_encode([
            "success" => $resultado > 0,
            "message" => $resultado > 0 ? "Eliminado correctamente" : "No se encontró el registro"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "ID_categoria no proporcionado"
        ]);
    }
    break;

    

/*TODO: Listar toda la información en formato DataTable */
case "listar":
    $datos = $categoria->get_categoria();
    $data = [];

    foreach ($datos as $row) {
        $id_categoria = (int) $row["ID_categoria"];  // fuerza a entero

        $nombre = htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8');

        $acciones = '
            <div class="action-buttons">
                <button type="button" class="icon-button icon-button--edit" 
                        onClick="editar(' . $id_categoria . ');" 
                        id="edit-' . $id_categoria . '" 
                        aria-label="Editar categoría">
                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                </button>
                <button type="button" class="icon-button icon-button--delete" 
                        onClick="eliminar(' . $id_categoria . ');" 
                        id="delete-' . $id_categoria . '" 
                        aria-label="Eliminar categoría">
                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                </button>
            </div>
        ';


        $data[] = [$nombre, $acciones];
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



        /*TODO: Listar toda la información para el combo */
       case "combo":
        $datos = $categoria->get_categoria();
        if (is_array($datos) && count($datos) > 0) {
            $html = "<option value='' selected disabled>Seleccione una categoría</option>"; // Agregar opción inicial vacía
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['ID_categoria'] . "'>" . $row['nombre'] . "</option>";
            }
            echo $html;
        }
        break;

    }
?>
