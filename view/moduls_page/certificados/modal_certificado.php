<div id="modalmantenimiento" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bd-0">
           <div class="modal-header" style="background-color: #7FCBD9; color: black; border-radius: 15px 15px 0 0;">
            <h5 class="md-headline-small">Seleccionar Usuarios <span class="material-symbols-outlined" style="font-size: 30px;">group_add</span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
    <div class="modal-body">
        <main class="main-content" role="main">
        <nav class="controls-bar" aria-label="Controles de tabla de categorías">
            <div class="controls-bar__actions" role="toolbar" aria-label="Herramientas de exportación">
               <button id="btnCopiar-modal" class="button button--outlined button--icon-leading" aria-label="Copiar datos">
                    <span class="material-symbols-outlined" aria-hidden="true">content_copy</span> Copiar
                </button>
                <button id="btnExcel-modal" class="button button--outlined button--icon-leading" aria-label="Exportar a Excel">
                    <span class="material-symbols-outlined" aria-hidden="true">table_chart</span> Excel
                </button>
                <button id="btnCSV-modal" class="button button--outlined button--icon-leading" aria-label="Exportar a CSV">
                    <span class="material-symbols-outlined" aria-hidden="true">description</span> CSV
                </button>
            </div>
            <div class="controls-bar__filters">
                <div class="text-input-wrapper">
                    <span class="material-symbols-outlined text-input-wrapper__icon" aria-hidden="true">search</span>
                    <input type="text" class="text-input" placeholder="Buscar Categoría" id="search-user-input">
                </div>
            </div>
        </nav>

        <section class="data-table-section" aria-labelledby="table-caption">
            <div class="table-container">
                <table id="usuario_data" class="data-table" role="grid" aria-describedby="table-caption">
                    <caption id="table-caption" class="visually-hidden">Tabla de categorías registradas</caption>
                    <thead class="data-table__header">
                        <tr>
                            <th scope="col" class="data-table__th"></th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Nombre</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Apa.Paterno</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Apa.Materno</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Correo Electrónico</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="data-table__body" id="user-table-body">
                        </tbody>
                </table>
            </div>
        </section>

        <section class="pagination-controls">
            <div class="pagination-controls__info">
                Visualizando registros del <span id="pagination-start-modal">1</span> al <span id="pagination-end-modal">8</span> de un total de <span id="pagination-total-modal">8</span> registros
            </div>
            <div class="pagination-controls__pages">
                <button class="pagination-controls__arrow pagination-controls__arrow--prev material-symbols-outlined">chevron_left</button>
                <button class="pagination-controls__page-num pagination-controls__page-num--active">1</button>
                <button class="pagination-controls__page-num">2</button>
                <button class="pagination-controls__page-num">3</button>
                <span class="pagination-controls__ellipsis">...</span>
                <button class="pagination-controls__arrow pagination-controls__arrow--next material-symbols-outlined">chevron_right</button>
            </div>
        </section>
    </main>
    </div>
            <div class="modal-footer">
                <button name="action" onclick="registrardetalle()" class="button button--primary">Guardar</button>
                <button type="reset" class="button button--outlined" data-dismiss="modal" id="btncancelar-usuario">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<style>
    @media (max-width: 768px) {
        .modal-dialog {
            max-width: 95%;
            margin: auto;
        }
        .modal-content {
            width: 100%;
            height: auto;
            border-radius: 15px;
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

    .modal-content {
        border-radius: 15px;
        border: 2px solid #98CDE4;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #98CDE4;
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

    .form-layout {
        border: 1px solid #000;
        border-radius: 0px 10px 10px 10px;
        padding: 20px;
        background-color: #fff;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        background-color: var(--md-sys-color-background);
        padding: 15px;
        border: transparent;
        border-radius: 0 0 15px 15px;
    }

    .btn {
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: bold;
    }

    .btn-info {
        background-color: #98CDE4;
        border-color: #98CDE4;
        color: white;
    }

    .btn-info:hover {
        background-color: #98CDE4;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    #usuario_data th {
        color: white;
        background-color: #98CDE4;
    }
</style>

