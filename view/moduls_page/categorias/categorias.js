
var usu_id = $('#usu_idx').val();

function init() {
    $("#categoria_form").on("submit", function(e) {
        guardaryeditar(e);
    });
    $("#modalmantenimiento button.close, #modalmantenimiento button[data-dismiss='modal']").on("click", cerrarModal);
}

function cerrarModal() {
    $('#modalmantenimiento').modal('hide');
}

function guardaryeditar(e) {
    e.preventDefault();
    const form = $("#categoria_form")[0];
    const inputNombre = $("#nombre");

    // Validación HTML5
    if (!form.checkValidity()) {
        if (!inputNombre.val().trim()) {
            inputNombre.addClass("is-invalid");
        }

        Swal.fire({
            title: "Campos incompletos",
            text: "Por favor, completa los campos requeridos.",
            icon: "warning",
            confirmButtonText: "Aceptar"
        });
        return;
    }

    var formData = new FormData(form);

    $.ajax({
        url: BASE_URL + 'controller/categoria.php?op=guardaryeditar',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(response) {
            // Siempre limpiar borde rojo al recibir respuesta
            inputNombre.removeClass("is-invalid");

            if (response.status === "success") {
                Swal.fire({
                    title: "¡Correcto!",
                    text: response.message,
                    icon: "success",
                    confirmButtonText: "Aceptar"
                });

                $('#categoria_data').DataTable().ajax.reload();
                cerrarModal();
                $('#categoria_form')[0].reset();
            } else {
                // Si es por nombre repetido, marcar en rojo
                if (response.message.includes("categoría ya")) {
                    inputNombre.addClass("is-invalid");
                }

                Swal.fire({
                    title: "Advertencia",
                    text: response.message,
                    icon: "warning",
                    confirmButtonText: "Aceptar"
                });
            }
        },
        error: function(jqXHR) {
            console.error("Error en AJAX:", jqXHR.responseText);
            Swal.fire({
                title: "Error de servidor",
                text: "Ocurrió un problema al procesar la solicitud.",
                icon: "error",
                confirmButtonText: "Aceptar"
            });
        }
    });
}


function editar(cat_id) {
    $.ajax({
        url: BASE_URL + 'controller/categoria.php?op=mostrar',
        type: 'POST',
        data: { cat_id: cat_id },
        dataType: 'json'
    }).done(function(data) {
        $('#nombre').removeClass("is-invalid");
        $('#ID_categoria').val(data.cat_id);
        $('#nombre').val(data.cat_nom);
        $('#lbltitulo').text('Editar Registro');
        $('#modalmantenimiento').modal('show');
    }).fail(function() {
        Swal.fire('Error', 'No se pudieron cargar los datos para editar.', 'error');
    });
}

function eliminar(cat_id) {
    Swal.fire({
        title: "Eliminar",
        text: "¿Desea eliminar el registro?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "No"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + 'controller/categoria.php?op=eliminar',
                type: 'POST',
                data: { ID_categoria: cat_id },
                dataType: 'json'
            }).done(function(response) {
                if (response.success) {
                    $('#categoria_data').DataTable().ajax.reload();
                    Swal.fire('Correcto!', response.message, 'success');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            }).fail(function() {
                Swal.fire('Error!', 'No se pudo eliminar el registro. Intente nuevamente.', 'error');
            });
        }
    });
}


function nuevo() {
    $("#categoria_form")[0].reset();
    $("#ID_categoria").val("");
    $("#lbltitulo").text("Registrar Categoría");
    $('#modalmantenimiento').modal('show');
    $('#nombre').removeClass("is-invalid");
}


$(document).ready(function () {
    init();

    const table = $('#categoria_data').DataTable({
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
            url: BASE_URL + 'controller/categoria.php?op=listar',
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

