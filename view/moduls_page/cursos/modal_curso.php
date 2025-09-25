<?php
require_once("../../config/conexion.php");
if (!isset($_SESSION["ID_administrador"]) && !isset($_SESSION["ID_gerente"])) {
  header("Location:" . Conectar::ruta() . "view/404/");
  exit();
}
?>
    <div class="modal fade" id="modal_foto_curso" tabindex="-1" aria-labelledby="fotoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #7FCBD9; color: black; border-radius: 15px 15px 0 0;">
                    <h5 class="md-headline-small" id="fotoLabel">Fotografía del Instructor</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <span aria-hidden="true">&times;</span>
                     </button>
                </div>
                <div class="modal-body text-center">
                    <img id="fotoModal_curso" src="" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_curso" tabindex="-1" aria-labelledby="cursoPopupLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="header-section">
                    <h5 class="md-headline-small" id="lbl_titulocurso">Registrar Curso</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_curso" method="POST" enctype="multipart/form-data">
                           <input type="hidden" name="cur_id" id="cur_id"/>
                        <div class="tab-content" id="profileTabContent">
                            <!-- Datos Personales -->
                            <div class="tab-pane fade show active"  role="tabpanel">
                                <div class="form-layout">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Categoria: <span class="tx-danger">*</span></label>
                                            <div class="select-wrapper"> 
                                            <select class="select-input md-label-medium" name="SelectCategoria" id="SelectCategoria">
                                            </select><br>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Fecha de Inicio: <span class="tx-danger">*</span></label>
                                            <input type="date" id="inicio_curso" name="inicio_curso" class="form-control md-label-medium" required><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Nombres: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="nom_curso" name="nom_curso" placeholder="Ingresar nombre"><br>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Fecha de Final: <span class="tx-danger">*</span></label>
                                            <input type="date" id="final_curso" name="final_curso" class="form-control md-label-medium" required><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Instructor: <span class="tx-danger">*</span></label>
                                            <div class="select-wrapper"> 
                                            <select class="select-input md-label-medium" name="SelectInstructor" id="SelectInstructor">
                                            </select><br>
                                            </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Horas: <span class="tx-danger">*</span></label>
                                            <input type="number" class="form-control md-label-medium" id="horas_curso" name="horas_curso" placeholder="Ej: 40" min="1" step="1" required>
                                        </div>


                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Fotografía:</label>
                                            <div class="input-group">
                                                <img id="imagenPreview" src="" class="img-profile"
                                                    style="width: 150px; height: 150px; display: none; margin-bottom: 10px;">
                                                <div>
                                                    <button type="button" class="button button--primary md-label-medium" onclick="seleccionarImagen()">Seleccionar Imagen</button>
                                                    <br><br>
                                                    <button type="button" class="button button--outlined md-label-medium" id="btnBorrarImagen"
                                                        onclick="borrarPreview()" style="display: none;">
                                                        Borrar Imagen
                                                    </button>
                                                </div>
                                                <input type="file" id="inputFoto" name="foto" accept=".jpg, .jpeg, .png"
                                                    style="display: none;" onchange="previsualizarImagen(event)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Botones del Modal -->
                        <div class="modal-footer">
                            <button type="reset" class="button button--outlined" data-bs-dismiss="modal" id="btncancelar-curso">Cancelar</button>
                            <button type="submit" class="button button--primary" id="btnactualizar-curso">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery y Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        //Previsualizar imagen
        function seleccionarImagen() {
            document.getElementById('inputFoto').click();
        }

        function previsualizarImagen(event) {
            var input = event.target;
            var reader = new FileReader();

            reader.onload = function() {
                var imgElement = document.getElementById('imagenPreview');
                var btnBorrar = document.getElementById('btnBorrarImagen');

                imgElement.src = reader.result;
                imgElement.style.display = 'block'; // Mostrar imagen

                ///Para mostrar el boton borrar cuando la imagen este seleccionada

                btnBorrar.style.display = 'inline-block'; // Mostrar botón borrar
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }
        /// Funcion para borrar la imagen

        function borrarPreview() {
            var imgElement = document.getElementById('imagenPreview');
            var inputFoto = document.getElementById('inputFoto');
            var btnBorrar = document.getElementById('btnBorrarImagen');

            // Resetear el input file eliminando el archivo seleccionado
            inputFoto.value = "";

            // Algunos navegadores requieren cambiar el tipo para resetear
            if (inputFoto.type === "file") {
                inputFoto.type = "";
                inputFoto.type = "file";
            }

            // Ocultar y borrar la imagen
            imgElement.src = "";
            imgElement.style.display = 'none';

            // Ocultar botón borrar
            btnBorrar.style.display = 'none';
        }
    </script>

    <style>
        /* Estilos generales del modal */

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 95%;
                margin: auto;
            }

            .modal-body {
                padding: 15px;
                margin: 0px !important;
            }

            .form-layout {
                margin: 0;
                padding: 15px;
            }

            .nav-tabs .nav-link {
                font-size: 14px;
                padding: 8px;
            }
        }

        .mb-3 {
            margin-bottom: 0px !important;
        }

        /* Estilos generales */
        .modal-content {
            border-radius: 15px;
            border: 2px solid #98CDE4;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #7FCBD9;
            color: black;
            padding: 15px;
            border-radius: 15px 15px 0 0;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .nav-tabs .nav-link {
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            background: #7FCBD9;
            color: white !important;
        }

        .nav-tabs .nav-link.active {
            background-color: #13232B;
            color: #7FCBD9 !important;
        }


        .form-layout {
            border: 1px solid #000;
            border-radius: 0px 10px 10px 10px;
            padding: 20px;
            background-color: #fff;
        }

        .btn {
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: bold;
        }

        .btn-info {
            background-color: #17A2B8;
            border-color: #17A2B8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        .img-profile {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            background-color: #EAF4FF;
            padding: 15px;
            border-radius: 0 0 15px 15px;
        }

        .modal-body {
            margin: 40px;
        }

        /* Botón de subida de imagen */
        .upload-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #7FCBD9;
            border: none;
            color: black;
            font-weight: bold;
            border-radius: 50px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .upload-btn:hover {
            background-color: #5fa6b3;
        }

        .upload-btn i {
            margin-right: 8px;
        }
    </style>