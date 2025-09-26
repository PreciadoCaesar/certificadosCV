<div class="page-wrapper">
    <div class="page-header-content">
                <div class="page-header-content__left">
                    <h1 class="page-header-content__title">
                        <span class="material-symbols-outlined page-header-content__icon"
                            aria-hidden="true">person</span>
                    Gestión de Administradores                    
                    </h1>
                </div>
            </div>

    <main class="main-content" role="main">
        <section class="section-header">
            <div class="section-header__text">
                <h2 class="section-header__title">Administradores</h2>
                <p class="section-header__description">Aquí podrá gestionar la lista completa de administradores</p>
            </div>
            <button class="button button--primary button--icon-leading" id="add_button" onclick="nuevo()">
                <span class="material-symbols-outlined" aria-hidden="true">add</span>
                Registrar Administrador
            </button>
        </section>

        <nav class="controls-bar" aria-label="Controles de tabla de usuarios">
            <div class="controls-bar__actions" role="toolbar" aria-label="Herramientas de exportación">
               <button id="btnCopiar-admin" class="button button--outlined button--icon-leading" aria-label="Copiar datos">
                    <span class="material-symbols-outlined" aria-hidden="true">content_copy</span> Copiar
                </button>
                <button id="btnExcel-admin" class="button button--outlined button--icon-leading" aria-label="Exportar a Excel">
                    <span class="material-symbols-outlined" aria-hidden="true">table_chart</span> Excel
                </button>
                <button id="btnCSV-admin" class="button button--outlined button--icon-leading" aria-label="Exportar a CSV">
                    <span class="material-symbols-outlined" aria-hidden="true">description</span> CSV
                </button>
                <button id="btnPDF-admin" class="button button--outlined button--icon-leading" aria-label="Exportar a PDF">
                    <span class="material-symbols-outlined" aria-hidden="true">picture_as_pdf</span> PDF
                </button>
            </div>
            <div class="controls-bar__filters">
                <div class="text-input-wrapper">
                    <span class="material-symbols-outlined text-input-wrapper__icon" aria-hidden="true">search</span>
                    <input type="text" class="text-input" placeholder="Buscar Usuario" id="search-user-input-admin">
                </div>
            </div>
        </nav>

        <section class="data-table-section" aria-labelledby="table-caption">
            <div class="table-container">
                <table id="administrador_data" class="data-table" role="grid" aria-describedby="table-caption">
                    <caption id="table-caption" class="visually-hidden">Tabla de usuarios registrados</caption>
                    <thead class="data-table__header">
                        <tr>
                            <th scope="col" class="data-table__th">Foto</th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Nombres</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Apellidos</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Correo</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Telefono</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Sexo</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Permiso</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Estado</span>
                            </th>
                            <th scope="col" class="data-table__th">Opciones</th>
                        </tr>
                    </thead>
                    <tbody class="data-table__body" id="user-table-body">
                        </tbody>
                </table>
            </div>
        </section>

        <section class="pagination-controls">
            <div class="pagination-controls__info">
                Visualizando registros del <span id="pagination-start-admin">1</span> al <span id="pagination-end-admin">8</span> de un total de <span id="pagination-total-admin">8</span> registros
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
        <br>
    </main>
</div>