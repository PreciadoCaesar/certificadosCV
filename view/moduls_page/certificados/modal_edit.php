<?php
require_once("../../config/conexion.php");
if (!isset($_SESSION["ID_administrador"]) && !isset($_SESSION["ID_gerente"])) {
  header("Location:" . Conectar::ruta() . "view/404/");
  exit();
}
?>
    <div class="modal fade" id="modal_edit" tabindex="-1" aria-labelledby="certificadoPopupLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="header-section">
                    <h5 class="md-headline-small" id="lbl_titulocertificado">Editar Certificado</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_certificado" method="POST" enctype="multipart/form-data">
                           <input type="hidden" name="curd_id" id="curd_id"/>
                        <div class="tab-content" id="profileTabContent">
                            <!-- Datos Personales -->
                            <div class="tab-pane fade show active"  role="tabpanel">
                                <div class="form-layout">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Usuarios: <span class="tx-danger">*</span></label>
                                            <div class="select-wrapper"> 
                                            <select class="select-input md-label-medium" name="SelectUsu" id="SelectUsu">
                                            </select><br>
                                        </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="md-label-large">Curso: <span class="tx-danger">*</span></label>
                                            <div class="select-wrapper"> 
                                            <select class="select-input md-label-medium" name="SelectCurso" id="SelectCurso">
                                            </select><br>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Fecha de Emisión: <span class="tx-danger">*</span></label>
                                            <input type="date" id="fecha_emision" name="fecha_emision" class="form-control md-label-medium" required><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Fecha de Vencimiento: <span class="tx-danger">*</span></label>
                                            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control md-label-medium" required><br>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">Nota del Certificado: <span class="tx-danger">*</span></label>
                                            <input type="number" class="form-control md-label-medium" id="nota_curso" name="nota_curso" placeholder="Ej: 16" min="0" max="20" step="1" required>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Botones del Modal -->
                        <div class="modal-footer">
                            <button type="reset" class="button button--outlined" data-bs-dismiss="modal" id="btncancelar-certificado">Cancelar</button>
                            <button type="submit" class="button button--primary" id="btnactualizar-certificado">Guardar Cambios</button>
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