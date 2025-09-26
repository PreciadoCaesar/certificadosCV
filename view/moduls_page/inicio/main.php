 <div class="page-wrapper">
        <!-- Banner de bienvenida -->
        <div class="banner">
            <div class="banner__overlay"></div>
            <div class="banner__content">
                <h2 class="banner__greeting">Hola <?php echo $_SESSION["nom_admin"]." ".$_SESSION["ape_paterno"]; ?></h2> <!-- Placeholder para PHP -->
                <h1 class="md-display-small-bold">Bienvenido al Portal</h1>
            </div>
        </div>

        <!-- Menu de tarjetas para cambiar de sección -->
        <div class="card-grid">
            <!-- Cursos Destacados -->
            <div class="card-grid__item">
                <div class="card card--panel" onclick="mostrarCursos()">
                    <div class="card__body">
                        <div class="card__icon-box"><span class="material-symbols-outlined">school</span></div>
                        <div>
                            <h5 class="card__title">Cursos Destacados</h5>
                            <p class="card__text"><span id="cantidad-cursos">.....</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nuevos Instructores -->
            <div class="card-grid__item">
                <div class="card card--panel" onclick="mostrarInstructores()">
                    <div class="card__body">
                        <div class="card__icon-box"><span class="material-symbols-outlined">group</span></div>
                        <div>
                            <h5 class="card__title">Nuevos Instructores</h5>
                            <p class="card__text"><span id="cantidad-instructores">......</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuarios Activos -->
            <div class="card-grid__item">
                <div class="card card--panel" onclick="mostrarUsuarios()">
                    <div class="card__body">
                        <div class="card__icon-box"><span class="material-symbols-outlined">manage_accounts</span></div>
                        <div>
                            <h5 class="card__title">Administradores Activos</h5>
                            <p class="card__text"><span id="cantidad-usuarios">......</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel que agrupa las tablas -->
        <div class="table-group-panel">

        <!-- Sección: Cursos -->
            <main class="main-content" role="main" id="cursos">
                <section class="section-header">
                    <div class="section-header__text">
                        <h2 class="section-header__title">Curso</h2>
                        <p class="section-header__description">Aquí podrá encontrar los 10 últimos cursos agregados</p>
                    </div>
                </section>

                <nav class="controls-bar" aria-label="Controles de tabla de categorías">
                    <div class="controls-bar__actions" role="toolbar" aria-label="Herramientas de exportación">
                    <button id="btnCopiar-inicursos" class="button button--outlined " aria-label="Copiar datos">
                            <span class="material-symbols-outlined" aria-hidden="true">content_copy</span> Copiar
                        </button>
                        <button id="btnExcel-inicursos" class="button button--outlined " aria-label="Exportar a Excel">
                            <span class="material-symbols-outlined" aria-hidden="true">table_chart</span> Excel
                        </button>
                        <button id="btnCSV-inicursos" class="button button--outlined " aria-label="Exportar a CSV">
                            <span class="material-symbols-outlined" aria-hidden="true">description</span> CSV
                        </button>
                        <button id="btnPDF-inicursos" class="button button--outlined " aria-label="Exportar a PDF">
                            <span class="material-symbols-outlined" aria-hidden="true">picture_as_pdf</span> PDF
                        </button>
                    </div>
                    <div class="controls-bar__filters">
                        <div class="text-input-wrapper">
                            <span class="material-symbols-outlined text-input-wrapper__icon" aria-hidden="true">search</span>
                            <input type="text" class="text-input" placeholder="Buscar Curso" id="search-user-input-inicursos">
                        </div>
                    </div>
                </nav>

                <section class="data-table-section" aria-labelledby="table-caption">
                    <div class="table-container">
                        <table id="tabla_cursos" class="data-table" role="grid" aria-describedby="table-caption">
                            <caption id="table-caption" class="visually-hidden">Tabla de cursos registradas</caption>
                            <thead class="data-table__header">
                                <tr>
                                    <th scope="col" class="data-table__th">Foto</th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Nombre</span>
                                    </th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Fecha Inicio</span>
                                    </th><th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Fecha Final</span>
                                    </th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Instructor</span>
                                    </th>
                                    <th scope="col" class="data-table__th">Temario</th>
                                </tr>
                            </thead>
                            <tbody class="data-table__body" id="user-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
                <br>
                <br>
            </main>

            <!-- Sección: Instructores -->
            <main class="main-content" role="main" id="instructores" style="display: none;">
                <section class="section-header">
                    <div class="section-header__text">
                        <h2 class="section-header__title">Instructores</h2>
                        <p class="section-header__description">Aquí podrá encontrar los 10 últimos instructores agregados</p>
                    </div>
                </section>

                <nav class="controls-bar" aria-label="Controles de tabla de categorías">
                    <div class="controls-bar__actions" role="toolbar" aria-label="Herramientas de exportación">
                    <button id="btnCopiar-iniinst" class="button button--outlined" aria-label="Copiar datos">
                            <span class="material-symbols-outlined" aria-hidden="true">content_copy</span> Copiar
                        </button>
                        <button id="btnExcel-iniinst" class="button button--outlined" aria-label="Exportar a Excel">
                            <span class="material-symbols-outlined" aria-hidden="true">table_chart</span> Excel
                        </button>
                        <button id="btnCSV-iniinst" class="button button--outlined" aria-label="Exportar a CSV">
                            <span class="material-symbols-outlined" aria-hidden="true">description</span> CSV
                        </button>
                        <button id="btnPDF-iniinst" class="button button--outlined" aria-label="Exportar a PDF">
                            <span class="material-symbols-outlined" aria-hidden="true">picture_as_pdf</span> PDF
                        </button>
                    </div>
                    <div class="controls-bar__filters">
                        <div class="text-input-wrapper">
                            <span class="material-symbols-outlined text-input-wrapper__icon" aria-hidden="true">search</span>
                            <input type="text" class="text-input" placeholder="Buscar Instructor" id="search-user-input-iniinst">
                        </div>
                    </div>
                </nav>

                <section class="data-table-section" aria-labelledby="table-caption">
                    <div class="table-container">
                        <table id="tabla_instructores" class="data-table" role="grid" aria-describedby="table-caption">
                            <caption id="table-caption" class="visually-hidden">Tabla de instructores registrados</caption>
                            <thead class="data-table__header">
                                <tr>
                                    <th scope="col" class="data-table__th">Foto</th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Nombre</span>
                                    </th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Apellido Paterno</span>
                                    </th><th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Apellido Materno</span>
                                    </th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Telefono</span>
                                    </th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Correo</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="data-table__body" id="user-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
                <br>
                <br>
            </main>

            <!-- Sección: Administradores -->
            <main class="main-content" role="main" id="usuarios" style="display: none;">
                <section class="section-header">
                    <div class="section-header__text">
                        <h2 class="section-header__title">Administradores</h2>
                        <p class="section-header__description">Aquí podrá encontrar los 10 últimos administradores activos</p>
                    </div>
                </section>

                <nav class="controls-bar" aria-label="Controles de tabla de categorías">
                    <div class="controls-bar__actions" role="toolbar" aria-label="Herramientas de exportación">
                    <button id="btnCopiar-iniadmin" class="button button--outlined " aria-label="Copiar datos">
                            <span class="material-symbols-outlined" aria-hidden="true">content_copy</span> Copiar
                        </button>
                        <button id="btnExcel-iniadmin" class="button button--outlined " aria-label="Exportar a Excel">
                            <span class="material-symbols-outlined" aria-hidden="true">table_chart</span> Excel
                        </button>
                        <button id="btnCSV-iniadmin" class="button button--outlined " aria-label="Exportar a CSV">
                            <span class="material-symbols-outlined" aria-hidden="true">description</span> CSV
                        </button>
                        <button id="btnPDF-iniadmin" class="button button--outlined " aria-label="Exportar a PDF">
                            <span class="material-symbols-outlined" aria-hidden="true">picture_as_pdf</span> PDF
                        </button>
                    </div>
                    <div class="controls-bar__filters">
                        <div class="text-input-wrapper">
                            <span class="material-symbols-outlined text-input-wrapper__icon" aria-hidden="true">search</span>
                            <input type="text" class="text-input" placeholder="Buscar Administrador" id="search-user-input-iniadmin">
                        </div>
                    </div>
                </nav>

                <section class="data-table-section" aria-labelledby="table-caption">
                    <div class="table-container">
                        <table id="tabla_usuarios" class="data-table" role="grid" aria-describedby="table-caption">
                            <caption id="table-caption" class="visually-hidden">Tabla de administradores registrados</caption>
                            <thead class="data-table__header">
                                <tr>
                                    <th scope="col" class="data-table__th">Foto</th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Nombre</span>
                                    </th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Apellido Paterno</span>
                                    </th><th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Apellido Materno</span>
                                    </th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Telefono</span>
                                    </th>
                                    <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                        <span class="data-table__th-text">Correo</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="data-table__body" id="user-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
                <br>
                <br>
            </main>
        </div><!-- .table-group-panel -->
    </div><!-- .page-wrapper -->