// adminmntinstructor.js

$(document).ready(function () {
    init();

    const table = $('#instructor_data').DataTable({
        aProcessing: true,
        aServerSide: true,
        dom: 'Bfrtip',
       buttons: [
    {
        extend: 'copy',
        className: 'copyButton',
        exportOptions: {
            columns: [1, 2, 3, 4, 5]
        }
    },
    {
        extend: 'excel',
        className: 'excelButton',
        exportOptions: {
            columns: [1, 2, 3, 4, 5]
        }
    },
    {
        extend: 'csv',
        className: 'csvButton',
        exportOptions: {
            columns: [1, 2, 3, 4, 5]
        }
    },
    {
        extend: 'pdf',
        className: 'pdfButton',
        exportOptions: {
            columns: [1, 2, 3, 4, 5]
        }
    }
]
,
        ajax: {
            url: BASE_URL + 'controller/instructor.php?op=listar',
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

var admin_id = $('#admin_idx').val();

function init(){
    $("#form_instructor").on("submit", function(e){
        guardaryeditar(e);
    });
}


function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#form_instructor")[0]);
    $('#inst_correo').removeClass('is-invalid');

    $.ajax({
        url: BASE_URL + 'controller/instructor.php?op=guardaryeditar',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        xhrFields: { withCredentials: true },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Solo si es exitoso: cerrar, limpiar y recargar
                $('#instructor_data').DataTable().ajax.reload(null, false);
                $('#modal_instructor').modal('hide');
                $('#form_instructor')[0].reset();
                borrarPreview();

                Swal.fire({
                    title: '¡Correcto!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                // Mostrar mensaje de advertencia
                Swal.fire({
                    title: 'Advertencia',
                    text: response.message,
                    icon: 'warning',
                    confirmButtonText: 'Aceptar'
                });

                // Si el mensaje menciona el correo, marcar el input
                if (response.message.toLowerCase().includes('correo')) {
                    $('#inst_correo').addClass('is-invalid').focus();
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("🔴 Error AJAX:", xhr.responseText);
            Swal.fire({
                title: 'Error en el servidor',
                html: `
                    <p><strong>Status:</strong> ${status}</p>
                    <p><strong>Error:</strong> ${error}</p>
                    <pre style="text-align:left; white-space:pre-wrap;">${xhr.responseText || "Sin respuesta del servidor"}</pre>
                `,
                icon: 'error',
                confirmButtonText: 'Aceptar',
                width: '60%'
            });
        }
    });
}


// Mostrar en el formulario datos para editar
function editar(inst_id) {
    $.post(BASE_URL + 'controller/instructor.php?op=mostrar', { inst_id: inst_id }, function(data) {
        $('#inst_correo').removeClass('is-invalid');
        $('#inst_id').val(data.inst_id);
        $('#inst_nom').val(data.inst_nom);
        $('#inst_apep').val(data.inst_apep);
        $('#inst_apem').val(data.inst_apem);
        $('#inst_telf').val(data.inst_telf);
        $('#inst_correo').val(data.inst_correo);

        if (data.foto && data.foto !== "") {
            let rutaImagen = BASE_URL + "public/img/img_instructor/" + data.foto + "?v=" + new Date().getTime();
            $("#imagenPreview").attr("src", rutaImagen).show();
            $("#btnBorrarImagen").show();
        } else {
            $("#imagenPreview").hide();
            $("#btnBorrarImagen").hide();
        }

        $('#titulo_modalinstructor').html('Editar Registro');
        $('#modal_instructor').modal('show');
    }, "json").fail(function () {
        Swal.fire('Error', 'No se pudieron cargar los datos para editar.', 'error');
    });
}


// Función para eliminar un registro
function eliminar(ID_instructor) {
    Swal.fire({
        title: 'Eliminar!',
        text: '¿Desea eliminar el registro?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + 'controller/instructor.php?op=eliminar',
                type: 'POST',
                data: { ID_instructor: ID_instructor },
                dataType: 'json',
                xhrFields: { withCredentials: true }
            }).done(function(response) {
                $('#instructor_data').DataTable().ajax.reload();
                Swal.fire('Correcto!', response.message || 'Se eliminó correctamente', 'success');
            }).fail(function(){
                Swal.fire('Error!', 'No se pudo eliminar el registro', 'error');
            });
        }
    });
}

// Función para ver la imagen en un modal
function verImagenInstructor(inst_id) {
    $.post(BASE_URL + 'controller/instructor.php?op=mostrar_foto', { inst_id: inst_id }, function (data) {
        if (data.foto && data.foto.trim() !== "") {
            let imgPath = BASE_URL + "public/img/img_instructor/" + data.foto;

            $.get(imgPath)
                .done(function () {
                    $("#fotoModal").attr("src", imgPath);
                    $("#modal_visualizar_foto").modal("show");
                    $("#fotoLabel").text("Foto de Perfil de " + (data.inst_nom || "Instructor"));
                })
                .fail(function () {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje || 'La imagen no se encuentra en el servidor.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                });
        } else {
            Swal.fire({
                title: 'Aviso',
                text: data.mensaje || 'El instructor no tiene una imagen registrada.',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        }
    }, "json");
}


// Función para mostrar el formulario de nuevo registro
function nuevo(){
    $('#inst_id').val('');
     $('#titulo_modalinstructor').html('Nuevo Registro');
    $('#form_instructor')[0].reset();
    $('#modal_instructor').modal('show');
    $('#inst_correo').removeClass('is-invalid');
    borrarPreview();
}
