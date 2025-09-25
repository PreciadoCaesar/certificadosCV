<div class="page-wrapper">
    <div class="page-header-content">
        <div class="page-header-content__left">
            <h1 class="page-header-content__title">
                <span class="material-symbols-outlined page-header-content__icon"
                    aria-hidden="true">note_alt</span> Emisión de Certificados
            </h1>
        </div>
    </div>

    <main class="main-content" role="main">
        <section class="section-header">
            <div class="section-header__text">
                <h2 class="section-header__title">Certificados</h2> <p class="section-header__description">Aquí podrá gestionar la lista completa de certificados emitidos</p> </div>
         <div id="mensaje-certificados" class="mensaje-burbuja-mejorada">
        <span class="material-symbols-outlined">
        info
        </span>
            Seleccione un curso para habilitar la visualización de certificados y acceder a las funcionalidades del módulo.
        </span>
        </div>
        </section>

        <nav class="controls-bar" aria-label="Controles de tabla de certificados">
            <div class="controls-bar__filters-end">
             <div class="select-wrapper">
                    <select class="select-input" id="cur_id" name="cur_id" aria-label="Seleccionar Curso">
                    </select>
            </div>   
            </div>
            <div class="controls-bar__actions-column" role="toolbar" aria-label="Herramientas de exportación" id="navbar-certificado">
                <nav class="controls-bar-button" aria-label="Controles de tabla de usuarios">
                <div class="text-input-wrapper">
                    <span class="material-symbols-outlined text-input-wrapper__icon" aria-hidden="true">search</span>
                    <input type="text" class="text-input" placeholder="Buscar Certificado" id="search-table-principal"> </div>
                <button id="btnCopiar" class="button button--outlined button--icon-leading" aria-label="Copiar datos">
                    <span class="material-symbols-outlined" aria-hidden="true">content_copy</span> Copiar
                </button>
                <button id="btnExcel" class="button button--outlined button--icon-leading" aria-label="Exportar a Excel">
                    <span class="material-symbols-outlined" aria-hidden="true">table_chart</span> Excel
                </button>
                <button id="btnCSV" class="button button--outlined button--icon-leading" aria-label="Exportar a CSV">
                    <span class="material-symbols-outlined" aria-hidden="true">description</span> CSV
                </button>
                <button id="btnPDF" class="button button--outlined button--icon-leading" aria-label="Exportar a PDF">
                    <span class="material-symbols-outlined" aria-hidden="true">picture_as_pdf</span> PDF
                </button>
                <?php if (
                                    (isset($_SESSION["rol"]) && $_SESSION["rol"] === "gerente") ||
                                    (isset($_SESSION["rol"], $_SESSION["tipo_permiso"]) && $_SESSION["rol"] === "admin" && $_SESSION["tipo_permiso"] == 1)
                                ): ?>
                <button class="button button--primary button--icon-leading" id="add_button" onclick="nuevo()">
                    <span class="material-symbols-outlined" aria-hidden="true">add</span>
                    Agregar Certificado
                </button>
                <?php endif; ?>
                </nav>
            </div>
        </nav>

        <section class="data-table-section" aria-labelledby="table-caption" id="data-table-section">
            <div class="table-container">
                <table id="detalle_data" class="data-table" role="grid" aria-describedby="table-caption">
                    <caption id="table-caption" class="visually-hidden">Tabla de certificados emitidos</caption>
                    <thead class="data-table__header">
                        <tr>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Curso</span> </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Usuario</span> </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Fecha de Emisión</span> </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Fecha de Vencimiento</span> </th>
                            <th class="data-table__th data-table__th--sortable" role="columnheader" aria-sort="none">
                                <span class="data-table__th-text">Instructor</span> </th>
                            <?php if (
                                    (isset($_SESSION["rol"]) && $_SESSION["rol"] === "gerente") ||
                                    (isset($_SESSION["rol"], $_SESSION["tipo_permiso"]) && $_SESSION["rol"] === "admin" && $_SESSION["tipo_permiso"] == 1)
                                ): ?>
                            <th scope="col" class="data-table__th">Ver Certificado</th>
                            <th scope="col" class="data-table__th">Opciones</th>    
                            <?php endif; ?>
         
                        </tr>
                    </thead>
                    <tbody class="data-table__body" id="certificate-table-body">
                    </tbody>
                </table>
            </div>
        </section>

        <section class="pagination-controls"  id="pagination-controls">
            <div class="pagination-controls__info">
                Visualizando registros del <span id="pagination-start">1</span> al <span id="pagination-end">8</span> de un total de <span id="pagination-total">8</span> registros
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("cur_id");
  const mensaje = document.getElementById("mensaje-certificados");
  const icono = mensaje.querySelector(".icono-info");

  select.addEventListener("mouseenter", function () {
    mensaje.style.backgroundColor = "#e0f8e9";  // verde suave
    mensaje.style.borderColor = "#34a853";       // borde verde
    mensaje.style.color = "#1a7f41";
    mensaje.style.boxShadow = "0 6px 12px rgba(0, 128, 0, 0.2)";
    icono.style.color = "#1a7f41";               // ícono verde también
  });

  select.addEventListener("mouseleave", function () {
    mensaje.style.backgroundColor = "#e6f4ff";   // color original
    mensaje.style.borderColor = "#91caff";
    mensaje.style.color = "#0a66c2";
    mensaje.style.boxShadow = "0 2px 6px rgba(0, 0, 0, 0.08)";
    icono.style.color = "#0a66c2";               // ícono azul original
  });
});

</script>
<style>
/* Estilo normal de la burbuja */
.mensaje-burbuja-mejorada {
  background-color: #e6f4ff;
  border: 1px solid #91caff;
  color: #0a66c2;
  padding: 8px 10px;
  border-radius: 12px;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  max-width: 600px;
  min-width: 250px;
  margin: 15px auto;
  animation: fadeIn 0.4s ease-out;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.mensaje-burbuja-mejorada:hover {
  transform: translateY(20px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
}
</style>