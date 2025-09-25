<?php
require_once("../../config/conexion.php");

if (!isset($_SESSION["ID_gerente"])) {
  header("Location: " . Conectar::ruta() . "view/404/");
  exit();
}
?>
    <div class="modal fade" id="modal_fotoAdmin" tabindex="-1" aria-labelledby="fotoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="md-headline-small" id="fotoLabel">Fotografía del Administrador</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <span aria-hidden="true">&times;</span>
                     </button>
                </div>
                <div class="modal-body text-center">
                    <img id="fotoAdmin" src="" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_administrador" tabindex="-1" aria-labelledby="administradorPopupLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="header-section">
                    <h5 class="md-headline-small" id="lbltituloadministrador">Editar administrador</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                <div class="modal-body">
                    <form id="form_administrador" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="ID_administrador" name="ID_administrador" value="">
                        <ul class="nav nav-tabs mb-3" id="profileTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="datos-administrador-tab" data-bs-toggle="tab" href="#datos-administrador" role="tab">Datos Personales <span class="tx-danger">*</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="inf-administrador-tab" data-bs-toggle="tab" href="#inf-administrador" role="tab">Información de administrador <span class="tx-danger">*</span></a>
                            </li>
                        </ul>
                        <div class="tab-content" id="profileTabContent">
                            <!-- Datos Personales -->
                            <div class="tab-pane fade show active" id="datos-administrador" role="tabpanel">
                                <div class="form-layout">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Nombres: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="nom_administrador" name="nom_administrador" placeholder="Ingresar nombres"><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Apellido Paterno: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="ape_paterno" name="ape_paterno" placeholder="Ingresar apellido paterno"><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Sexo: <span class="tx-danger">*</span></label>
                                            <select class="md-label-large select-input" style="width:100%" name="sexo" id="sexo" required>
                                                <option value="" disabled selected>Seleccione un sexo</option>
                                                <option value="Masculino">Masculino</option>
                                                <option value="Femenino">Femenino</option>
                                            </select><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Apellido Materno: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="ape_materno" name="ape_materno" placeholder="Ingresar apellido materno"><br>
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

                            <!-- Información de administrador -->
                            <div class="tab-pane fade" id="inf-administrador" role="tabpanel">
                                <div class="form-layout">
                                    <div class="row g-3">
                                         <div class="col-12 col-md-6">
                                            <label class="md-label-large">Correo Electrónico: <span class="tx-danger">*</span></label>
                                            <input type="email" class="form-control md-label-medium" id="correo" name="correo" placeholder="Ingrese su correo" required>
                                        <br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Teléfono: <span class="tx-danger">*</span></label>
                                            <input type="number" class="form-control md-label-medium" id="telefono" name="telefono" placeholder="Ingrese su teléfono" required>
                                        <br>
                                        </div>
                                        <!-- Campo para contraseña (registro) -->
                                        <div class="col-12 col-md-6 mb-3" id="divPasswordRegistro">
                                        <label for="password" class="md-label-large">Password: <span class="tx-danger">*</span></label>
                                        <input
                                            type="password"
                                            class="form-control md-label-medium"
                                            id="password"
                                            name="password"
                                            placeholder="Ingrese su password"
                                        />
                                        </div>

                                        <!-- Botón para mostrar campos de cambio de contraseña (edición) -->
                                        <div class="col-12 col-md-6 mb-3" id="divBtnCambiarPassword" style="display: none;">
                                        <button type="button" id="btnCambiarPassword" class="button button--primary">
                                            Cambiar contraseña
                                        </button>
                                        </div>

                                        <!-- Campos para cambio de contraseña (solo en edición) -->
                                        

                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Permiso de Administrador: <span class="tx-danger">*</span></label>
                                            <select class="md-label-large select-input" style="width:100%" name="permiso" id="permiso" required>
                                                <option value="" disabled selected>Seleccione un tipo de permiso</option>
                                                <option value="1">Editor</option>
                                                <option value="2">Lector</option>
                                            </select><br>
                                        </div>

                                        <div class="col-12 col-md-6" id="divCambiarPassword" style="display: none;">
                                        <label class="md-label-large" style="background-color: #f8d7da; padding: 5px; color: #721c24;">Si deseas cambiar la contraseña, debes rellenar correctamente los 3 campos</label>
                                        <br><br>
                                        <div class="mb-2">
                                            <input
                                            type="password"
                                            id="password_actual"
                                            name="password_actual"
                                            placeholder="Contraseña actual"
                                            class="form-control md-label-medium"
                                            />
                                        </div>
                                        <div class="mb-2">
                                            <input
                                            type="password"
                                            id="password_nueva"
                                            name="password_nueva"
                                            placeholder="Contraseña nueva"
                                            class="form-control md-label-medium"
                                            />
                                        </div>
                                        <div class="mb-2">
                                            <input
                                            type="password"
                                            id="password_nueva_repetir"
                                            name="password_nueva_repetir"
                                            placeholder="Repetir contraseña nueva"
                                            class="form-control md-label-medium"
                                            />
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones del Modal -->
                        <div class="modal-footer" >
                            <button type="reset" class="button button--outlined" data-bs-dismiss="modal" id="btncancelar-administrador">Cancelar</button>
                            <button type="button" class="button button--primary" id="btnsiguiente-administrador">Siguiente</button>
                            <button type="button" class="button button--outlined d-none" id="btnatras-administrador">Atrás</button>
                            <button type="submit" class="button button--primary d-none" id="btnactualizar-administrador">Guardar Cambios</button>
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
            $("#modal_administrador").modal("hide");
        
            // Mostrar modal al hacer clic en botón registrar
            $("#btnRegistraradministrador").click(function () {
                $("#modal_administrador").modal("show");
            });
        
            // Botón Siguiente: Ir a pestaña "Información del Administrador"
            $("#btnsiguiente-administrador").click(function () {
                $('#inf-administrador-tab').tab('show');
            });
        
            // Botón Atrás: Volver a pestaña "Datos del Administrador"
            $("#btnatras-administrador").click(function () {
                $('#datos-administrador-tab').tab('show');
            });
        
            // Sincronizar botones según la pestaña activa
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr("href");
        
                if (target === "#datos-administrador") {
                    $("#btncancelar-administrador, #btnsiguiente-administrador").removeClass("d-none");
                    $("#btnatras-administrador, #btnactualizar-administrador").addClass("d-none");
                } else if (target === "#inf-administrador") {
                    $("#btncancelar-administrador, #btnsiguiente-administrador").addClass("d-none");
                    $("#btnatras-administrador, #btnactualizar-administrador").removeClass("d-none");
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
