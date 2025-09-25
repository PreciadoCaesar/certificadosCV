<?php
require_once("../../config/conexion.php");

if (!isset($_SESSION["ID_administrador"]) && !isset($_SESSION["ID_gerente"])) {
  header("Location: " . Conectar::ruta() . "view/404/");
  exit();
}
?>
    <div class="modal fade" id="modal_visualizar_foto" tabindex="-1" aria-labelledby="fotoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="md-headline-small" id="fotoLabel">Fotografía del Usuario</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <span aria-hidden="true">&times;</span>
                     </button>
                </div>
                <div class="modal-body text-center">
                    <img id="fotoModal" src="" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_usuario" tabindex="-1" aria-labelledby="usuarioPopupLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="header-section">
                    <h5 class="md-headline-small" id="lbltitulousuario">Editar Usuario</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                <div class="modal-body">
                    <form id="form_usuario" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="usu_id" name="usu_id">
                        <ul class="nav nav-tabs mb-3" id="profileTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="datos-usuario-tab" data-bs-toggle="tab" href="#datos-usuario" role="tab">Datos Personales <span class="tx-danger">*</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="inf-usuario-tab" data-bs-toggle="tab" href="#inf-usuario" role="tab">Información de Usuario <span class="tx-danger">*</span></a>
                            </li>
                        </ul>
                        <div class="tab-content" id="profileTabContent">
                            <!-- Datos Personales -->
                            <div class="tab-pane fade show active" id="datos-usuario" role="tabpanel">
                                <div class="form-layout">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Nombres: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="nombre_usuario" name="nombre_usuario" placeholder="Ingresar nombres"><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Documento de Identidad: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="dni_usuario" name="dni_usuario" placeholder="Ingresar DNI"><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Apellido Paterno: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="ape_paterno_usuario" name="ape_paterno_usuario" placeholder="Ingresar apellido paterno"><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Sexo: <span class="tx-danger">*</span></label>
                                            <select class="md-label-large select-input" style="width:100%" name="sexo_usuario" id="sexo_usuario" required>
                                                <option value="" disabled selected>Seleccione un sexo</option>
                                                <option value="Masculino">Masculino</option>
                                                <option value="Femenino">Femenino</option>
                                            </select><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Apellido Materno: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="ape_materno_usuario" name="ape_materno_usuario" placeholder="Ingresar apellido materno"><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Fotografía:</label>
                                            <div class="input-group">
                                                <img id="imagenPreview" src="" class="img-profile"
                                                    style="width: 150px; height: 150px; display: none; margin-bottom: 10px;">
                                                <div>
                                                    <button type="button" class="button button--primary md-label-medium" onclick="seleccionarImagen()">Seleccionar Imagen</button>
                                                    <br><br>
                                                    <button type="button" class="button button--outlined md-label-medium" id="btnBorrarImagen" onclick="borrarPreview()" style="display: none;">Borrar Imagen</button>
                                                </div>
                                                <input type="file" id="inputFoto" name="foto" accept=".jpg, .jpeg, .png" style="display: none;" onchange="previsualizarImagen(event)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Usuario -->
                            <div class="tab-pane fade" id="inf-usuario" role="tabpanel">
                                <div class="form-layout">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Teléfono: <span class="tx-danger">*</span></label>
                                            <input type="number" class="form-control md-label-medium" id="telefono_usuario" name="telefono_usuario" placeholder="Ingrese su teléfono" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Correo Electrónico: <span class="tx-danger">*</span></label>
                                            <input type="email" class="form-control md-label-medium" id="correo_usuario" name="correo_usuario" placeholder="Ingrese su correo" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones del Modal -->
                        <div class="modal-footer" >
                            <button type="reset" class="button button--outlined" data-bs-dismiss="modal" id="btncancelar-usuario">Cancelar</button>
                            <button type="button" class="button button--primary" id="btnsiguiente-usuario">Siguiente</button>
                            <button type="button" class="button button--outlined d-none" id="btnatras-usuario">Atrás</button>
                            <button type="submit" class="button button--primary d-none" id="btnactualizar-usuario">Guardar Cambios</button>
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
         $(document).ready(function () {
        // Ocultar modal al cargar
        $("#modal_usuario").modal("hide");

        // Mostrar modal al hacer clic en botón registrar
        $("#btnRegistrarUsuario").click(function () {
            $("#modal_usuario").modal("show");
        });

        // Botón Siguiente: Ir a pestaña "Información de Usuario"
        $("#btnsiguiente-usuario").click(function () {
            $('#inf-usuario-tab').tab('show');
        });

        // Botón Atrás: Volver a pestaña "Datos Personales"
        $("#btnatras-usuario").click(function () {
            $('#datos-usuario-tab').tab('show');
        });

        // Sincronizar botones según la pestaña activa
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr("href");

            if (target === "#datos-usuario") {
                $("#btncancelar-usuario, #btnsiguiente-usuario").removeClass("d-none");
                $("#btnatras-usuario, #btnactualizar-usuario").addClass("d-none");
            } else if (target === "#inf-usuario") {
                $("#btncancelar-usuario, #btnsiguiente-usuario").addClass("d-none");
                $("#btnatras-usuario, #btnactualizar-usuario").removeClass("d-none");
            }
        });
    });
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

        .form-control,
        .form-select {
            border-radius: 50px;
            padding: 10px 15px;
            border: 1px solid #98CDE4;
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
            background-color: transparent;
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
        }.form-label{
            color: black !important;
        }
    </style>
