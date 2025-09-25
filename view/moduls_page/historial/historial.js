$(document).ready(function () {

    const table = $('#log_data').DataTable({
        aProcessing: true,
        aServerSide: true,
        dom: 'Bfrtip',
       buttons: [
    {
        extend: 'copy',
        className: 'copyButton',
        exportOptions: {
            columns: function (idx, data, node) {
                return idx !== 1; // Excluir la segunda columna
            }
        }
    },
    {
        extend: 'excel',
        className: 'excelButton',
        exportOptions: {
            columns: function (idx, data, node) {
                return idx !== 1;
            }
        }
    },
    {
        extend: 'csv',
        className: 'csvButton',
        exportOptions: {
            columns: function (idx, data, node) {
                return idx !== 1;
            }
        }
    },
    {
        extend: 'pdf',
        className: 'pdfButton',
        exportOptions: {
            columns: function (idx, data, node) {
                return idx !== 1;
            }
        }
    }
]
,
        ajax: {
            url: BASE_URL + 'controller/usuario.php?op=historial',
            type: 'GET',
            dataType: 'json',
            xhrFields: { withCredentials: true }
        },
        destroy: true,
        responsive: true,
        info: true,
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            sProcessing:   "Procesando...",
            sLengthMenu:   "Mostrar _MENU_ registros",
            sZeroRecords:  "No se encontraron resultados",
            sEmptyTable:   "Ningún dato disponible en esta tabla",
            sInfo:         "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty:    "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch:       "Buscar:",
            oPaginate: {
                sFirst:    "Primero",
                sLast:     "Último",
                sNext:     "Siguiente",
                sPrevious: "Anterior"
            },
            oAria: {
                sSortAscending:  ": Activar para ordenar la columna ascendente",
                sSortDescending: ": Activar para ordenar la columna descendente"
            }
        }
    });

    // Botones personalizados
    $('#btnCopiar').on('click', function () {
        table.button('.copyButton').trigger();
    });

    $('#btnExcel').on('click', function () {
        table.button('.excelButton').trigger();
    });

    $('#btnCSV').on('click', function () {
        table.button('.csvButton').trigger();
    });

    $('#btnPDF').on('click', function () {
        table.button('.pdfButton').trigger();
    });

    // Búsqueda personalizada
    $('#search-user-input').on('keyup change', function () {
        table.search(this.value).draw();
    });

    // Actualizar paginación personalizada
    function updateCustomPagination() {
        const pageInfo = table.page.info();
        const currentPage = pageInfo.page;
        const totalPages = pageInfo.pages;
        const start = pageInfo.start + 1;
        const end = pageInfo.end;
        const total = pageInfo.recordsDisplay;

        $('#pagination-start').text(start);
        $('#pagination-end').text(end);
        $('#pagination-total').text(total);

        const container = $('.pagination-controls__pages');
        container.empty();

        // Botón anterior
        const prevBtn = $('<button>')
            .addClass('pagination-controls__arrow pagination-controls__arrow--prev material-symbols-outlined')
            .text('chevron_left')
            .prop('disabled', currentPage === 0)
            .on('click', () => {
                if (currentPage > 0) table.page(currentPage - 1).draw('page');
            });
        container.append(prevBtn);

        // Rango de páginas
        const maxVisible = 5;
        let startPage = Math.max(0, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisible);
        if (endPage - startPage < maxVisible) {
            startPage = Math.max(0, endPage - maxVisible);
        }

        if (startPage > 0) {
            const firstBtn = $('<button>')
                .addClass('pagination-controls__page-num')
                .text('1')
                .on('click', () => table.page(0).draw('page'));
            container.append(firstBtn);
            if (startPage > 1) {
                container.append('<span class="pagination-controls__ellipsis">...</span>');
            }
        }

        for (let i = startPage; i < endPage; i++) {
            const pageBtn = $('<button>')
                .addClass('pagination-controls__page-num')
                .toggleClass('pagination-controls__page-num--active', i === currentPage)
                .text(i + 1)
                .on('click', () => table.page(i).draw('page'));
            container.append(pageBtn);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                container.append('<span class="pagination-controls__ellipsis">...</span>');
            }
            const lastBtn = $('<button>')
                .addClass('pagination-controls__page-num')
                .text(totalPages)
                .on('click', () => table.page(totalPages - 1).draw('page'));
            container.append(lastBtn);
        }

        // Botón siguiente
        const nextBtn = $('<button>')
            .addClass('pagination-controls__arrow pagination-controls__arrow--next material-symbols-outlined')
            .text('chevron_right')
            .prop('disabled', currentPage >= totalPages - 1)
            .on('click', () => {
                if (currentPage < totalPages - 1) table.page(currentPage + 1).draw('page');
            });
        container.append(nextBtn);
    }

    // Evento cuando se redibuja la tabla
    table.on('draw', function () {
        updateCustomPagination();
    });
});



