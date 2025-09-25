<div id="modalmantenimiento" class="modal modal--fade" role="dialog" aria-modal="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Encabezado -->
            <div class="header-section">
            <h6 id="lbltitulo" class="md-headline-small">
            Registrar Categoría
            </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Formulario Mantenimiento -->
                <form method="post" id="categoria_form">
                    <div class="modal-body">
                        <input type="hidden" name="cat_id" id="ID_categoria"/>
                        <div class="tab-content" id="profileTabContent">
                            <!-- Datos Personales -->
                            <div class="tab-pane fade show active"  role="tabpanel">
                                <div class="form-layout">
                                    <div class="row g-3">
                                        <div class="form-xd">
                                            <label class="form-label">Nombres: <span class="tx-danger">*</span></label>
                                            <input type="text" class="form-control md-label-medium" id="nombre" name="nombre" placeholder="Ingresar la categoría"><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Pie de página -->
                    <div class="modal-footer">
                            <button type="reset" class="button button--outlined"  data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="button button--primary">Guardar Cambios</button>
                        </div>
                </form>
        </div>
    </div>
</div>

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
