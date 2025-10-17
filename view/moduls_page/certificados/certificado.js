var admin_id = $('#admin_idx').val();

// Si init no hace nada, puedes eliminarlo o dejarlo vacío
function init(){}

let table;

$(document).ready(function () {
    combo_curso(); // Llenar combo

    // Inicializar tabla al cargar
    table = $('#detalle_data').DataTable({
        processing: true,
        serverSide: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'copyButton' },
            { extend: 'excel', className: 'excelButton' },
            { extend: 'csv', className: 'csvButton' },
            { extend: 'pdf', className: 'pdfButton' }
        ],
        ajax: {
            url: BASE_URL + 'controller/usuario.php?op=listar_cursos_usuario',
            type: 'POST',
            data: function (d) {
                const cursoID = $('#cur_id').val();
                if (cursoID) {
                    d.cur_id = cursoID;
                }
            },
            dataType: 'json',
            xhrFields: { withCredentials: true },
            dataSrc: function (json) { return json.aaData; }
        },
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            oPaginate: { sFirst: "Primero", sLast: "Último", sNext: "Siguiente", sPrevious: "Anterior" }
        }
    });

    // Al cambiar el combo de curso
    $('#cur_id').change(function () {
        table.ajax.reload();
    });

    // Botones externos
    $('#btnCopiar').on('click', function () { table.button('.copyButton').trigger(); });
    $('#btnExcel').on('click', function () { table.button('.excelButton').trigger(); });
    $('#btnCSV').on('click', function () { table.button('.csvButton').trigger(); });
    $('#btnPDF').on('click', function () { table.button('.pdfButton').trigger(); });

    // Búsqueda personalizada
    $('#search-table-principal').on('keyup change', function () {
        if ($.fn.DataTable.isDataTable('#detalle_data')) table.search(this.value).draw();
    });

    // Paginación personalizada
    table.on('draw', function () { updateCustomPagination(); });

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

    $('<button>')
      .addClass('pagination-controls__arrow pagination-controls__arrow--prev material-symbols-outlined')
      .text('chevron_left')
      .prop('disabled', currentPage === 0)
      .on('click', () => {
        if (currentPage > 0) table.page(currentPage - 1).draw('page');
      })
      .appendTo(container);

    const maxVisible = 5;
    let startPage = Math.max(0, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + maxVisible);
    if (endPage - startPage < maxVisible) {
      startPage = Math.max(0, endPage - maxVisible);
    }

    if (startPage > 0) {
      $('<button>')
        .addClass('pagination-controls__page-num')
        .text('1')
        .on('click', () => table.page(0).draw('page'))
        .appendTo(container);
      if (startPage > 1) {
        $('<span>').addClass('pagination-controls__ellipsis').text('...').appendTo(container);
      }
    }

    for (let i = startPage; i < endPage; i++) {
      $('<button>')
        .addClass('pagination-controls__page-num')
        .toggleClass('pagination-controls__page-num--active', i === currentPage)
        .text(i + 1)
        .on('click', () => table.page(i).draw('page'))
        .appendTo(container);
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        $('<span>').addClass('pagination-controls__ellipsis').text('...').appendTo(container);
      }
      $('<button>')
        .addClass('pagination-controls__page-num')
        .text(totalPages)
        .on('click', () => table.page(totalPages - 1).draw('page'))
        .appendTo(container);
    }

    $('<button>')
      .addClass('pagination-controls__arrow pagination-controls__arrow--next material-symbols-outlined')
      .text('chevron_right')
      .prop('disabled', currentPage >= totalPages - 1)
      .on('click', () => {
        if (currentPage < totalPages - 1) table.page(currentPage + 1).draw('page');
      })
      .appendTo(container);
  }
  
});



function eliminar_certificado(certificado_id) {
    Swal.fire({
        title: "Eliminar!",
        text: "¿Desea eliminar el certificado?",
        icon: "warning",
        confirmButtonText: "Sí",
        showCancelButton: true,
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + 'controller/usuario.php?op=eliminar_certificado',
                type: "POST",
                data: { certificado_id: certificado_id },
                dataType: "json",
                xhrFields: { withCredentials: true }
            }).done(function (data) {
                if (data.success) {
                    $('#detalle_data').DataTable().ajax.reload();
                    Swal.fire('Eliminado!', 'El certificado se eliminó correctamente.', 'success');
                } else {
                    Swal.fire('Error!', data.error || 'No se pudo eliminar el certificado.', 'error');
                }
            }).fail(function () {
                Swal.fire('Error!', 'Hubo un problema con la solicitud.', 'error');
            });
        }
    });
}

function combo_curso() {
  $.ajax({
    url: BASE_URL + 'controller/curso.php?op=combo',
    type: "POST",
    dataType: "html",
    xhrFields: { withCredentials: true },
    success: function (data) {
      $('#cur_id').html(data).trigger('change');
    }
  });
}


function certificado(curd_id){
    window.open(BASE_URL + 'view/Certificado/index.php?curd_id=' + curd_id, '_blank');
}

function nuevo() {
    if (!$('#cur_id').val()) {
        Swal.fire('Error!', 'Seleccionar Curso', 'error');
    } else {
        let cur_id = $('#cur_id').val();
        listar_usuario(cur_id); // Este cur_id se pasa correctamente
        $('#modalmantenimiento').modal('show');
    }
}


let usuarioTable;

function listar_usuario(cur_id) {
    if ($.fn.DataTable.isDataTable('#usuario_data')) {
        $('#usuario_data').DataTable().clear().destroy();
    }

    usuarioTable = $('#usuario_data').DataTable({
        processing: true,
        serverSide: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copyHtml5', titleAttr: 'Copiar', className: 'dt-button-copy' },
            { extend: 'excelHtml5', titleAttr: 'Excel', className: 'dt-button-excel' },
            { extend: 'csvHtml5', titleAttr: 'CSV', className: 'dt-button-csv' }
        ],
        ajax: {
            url: BASE_URL + 'controller/usuario.php?op=listar_detalle_usuario',
            type: 'POST',
            data: function (d) {
                return { cur_id: cur_id };
            },
            dataType: 'json',
            xhrFields: { withCredentials: true },
            dataSrc: 'aaData',
            cache: false
        },
        responsive: true,
        pageLength: 5,
        order: [[0, 'desc']],
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        }
    });

    // Esperar a que se inicialicen los botones
    usuarioTable.on('init', function () {
        // Botones personalizados
        $('#btnCopiar-modal').on('click', function () {
            $('.dt-button-copy').click();
        });

        $('#btnExcel-modal').on('click', function () {
            $('.dt-button-excel').click();
        });

        $('#btnCSV-modal').on('click', function () {
            $('.dt-button-csv').click();
        });

        // Buscador personalizado
        $('#search-user-input').on('input', function () {
            usuarioTable.search(this.value).draw();
        });
    });
    // Actualizar paginación personalizada
function updateCustomPagination() {
    const pageInfo = usuarioTable.page.info();
    const currentPage = pageInfo.page;
    const totalPages = pageInfo.pages;
    const start = pageInfo.start + 1;
    const end = pageInfo.end;
    const total = pageInfo.recordsDisplay;

    $('#pagination-start-modal').text(start);
    $('#pagination-end-modal').text(end);
    $('#pagination-total-modal').text(total);

    const container = $('.pagination-controls__pages');
    container.empty();

    // Botón anterior
    const prevBtn = $('<button>')
        .addClass('pagination-controls__arrow pagination-controls__arrow--prev material-symbols-outlined')
        .text('chevron_left')
        .prop('disabled', currentPage === 0)
        .on('click', () => {
            if (currentPage > 0) usuarioTable.page(currentPage - 1).draw('page');
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
            .on('click', () => usuarioTable.page(0).draw('page'));
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
            .on('click', () => usuarioTable.page(i).draw('page'));
        container.append(pageBtn);
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            container.append('<span class="pagination-controls__ellipsis">...</span>');
        }
        const lastBtn = $('<button>')
            .addClass('pagination-controls__page-num')
            .text(totalPages)
            .on('click', () => usuarioTable.page(totalPages - 1).draw('page'));
        container.append(lastBtn);
    }

    // Botón siguiente
    const nextBtn = $('<button>')
        .addClass('pagination-controls__arrow pagination-controls__arrow--next material-symbols-outlined')
        .text('chevron_right')
        .prop('disabled', currentPage >= totalPages - 1)
        .on('click', () => {
            if (currentPage < totalPages - 1) usuarioTable.page(currentPage + 1).draw('page');
        });
    container.append(nextBtn);
}

// Evento cuando se redibuja la tabla
usuarioTable.on('draw', function () {
    updateCustomPagination();
});

}





function registrardetalle() {
    let table = $('#usuario_data').DataTable();
    let usu_id = [];

    // Recorremos las filas y sacamos los IDs de usuarios seleccionados
    table.rows().every(function(rowIdx) {
        let cell0 = table.cell(rowIdx, 0).node();
        if ($('input', cell0).prop("checked")) {
            usu_id.push($('input', cell0).val());
        }
    });

    if (usu_id.length === 0) {
        Swal.fire('Error!', 'Seleccionar Usuarios', 'error');
        return;
    }

    let formData = new FormData($("#form_detalle")[0]);
    formData.append('cur_id', $('#cur_id').val());
    formData.append('usu_id', JSON.stringify(usu_id));

    $.ajax({
        url: BASE_URL + 'controller/curso.php?op=insert_curso_usuario',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',  // <- jQuery esperará JSON y parseará automáticamente
        xhrFields: { withCredentials: true },
        success: function(parsed) {
            // parsed ya es un objeto JS gracias a dataType:'json'
            if (!Array.isArray(parsed)) {
                Swal.fire('Error!', 'Respuesta inesperada del servidor', 'error');
                return;
            }

            parsed.forEach(batch => {
                batch.forEach(item => {
                    $.post(
                        BASE_URL + 'controller/curso.php?op=generar_qr',
                        { curd_id: item.curd_id },
                        function() {}
                    );
                });
            });

            Swal.fire('Correcto!', 'Se guardó correctamente', 'success');

            $('#detalle_data').DataTable().ajax.reload();
            $('#usuario_data').DataTable().ajax.reload();
            $('#modalmantenimiento').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', status, error);
            console.log('Respuesta del servidor:', xhr.responseText);
            Swal.fire('Error!', 'No se pudo registrar detalle', 'error');
        }
    });
}

function editar_certificado(curd_id) {
    console.log("ID recibido para edición:", curd_id);

    $.when(combo_curso_edit(),combo_usuarios()).done(function () {
        $.ajax({
            url: BASE_URL + 'controller/usuario.php?op=mostrar_certificado',
            type: "POST",
            data: { curd_id: curd_id },
            dataType: "json",
            xhrFields: { withCredentials: true }
        })
        .done(function (data) {
            console.log("Datos recibidos:", data);

            if (data && data.ID_certificado) {
                $('#curd_id').val(data.ID_certificado);
                $('#SelectCurso').val(data.ID_curso).trigger('change');
                $('#SelectUsu').val(data.ID_usuario).trigger('change');
                $('#fecha_emision').val(data.fecha_emision);
                $('#fecha_vencimiento').val(data.fecha_vencimiento);
                $('#nota_curso').val(data.nota);
                $('#modal_edit').modal('show');
            } else {
                Swal.fire('Advertencia', 'No se encontraron datos del certificado.', 'warning');
            }
        })
        .fail(function (xhr, status, error) {
            console.error("Error en mostrar_certificado:", {
                status: status,
                error: error,
                response: xhr.responseText
            });

            Swal.fire('Error', 'No se pudo cargar datos del certificado. Revisa la consola para más detalles.', 'error');
        });
    });
}

$(document).ready(function () {
    $(document).on("submit", "#form_certificado", function (e) {
        e.preventDefault();
        actualizar_certificado();
    });
});

function actualizar_certificado() {
    const formData = new FormData($("#form_certificado")[0]);

    $.ajax({
        url: BASE_URL + 'controller/certificado.php?op=actualizar_certificado',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        xhrFields: { withCredentials: true },
        beforeSend: function () {
            $("#form_certificado button[type='submit']").prop("disabled", true);
        },
        success: function (response) {
            if (response.status === 'success') {
                $('#modal_edit').modal('hide');
                $('#form_certificado')[0].reset();
                $('#detalle_data').DataTable().ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: '¡Correcto!',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: response.message,
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error en el servidor',
                html: `
                    <strong>Status:</strong> ${status}<br>
                    <strong>Error:</strong> ${error}<br>
                    <pre style="text-align:left; white-space:pre-wrap;">${xhr.responseText || "Sin respuesta del servidor"}</pre>
                `,
                confirmButtonText: 'Aceptar',
                width: '60%'
            });
        },
        complete: function () {
            $("#form_certificado button[type='submit']").prop("disabled", false);
        }
    });
}


function combo_curso_edit() {
    return $.ajax({
        url: BASE_URL + 'controller/curso.php?op=combo',
        type: "POST",
        dataType: "html",
        xhrFields: { withCredentials: true }
    }).done(function (data) {
        $("#SelectCurso").html(data);
    }).fail(function (xhr, status, error) {
        console.error("Error cargando combo_curso:", {
            status: status,
            error: error,
            response: xhr.responseText
        });
        Swal.fire('Error', 'No se pudo cargar la lista de cursos.', 'error');
    });
}
function combo_usuarios() {
    return $.ajax({
        url: BASE_URL + 'controller/usuario.php?op=combo',
        type: "POST",
        dataType: "html",
        xhrFields: { withCredentials: true }
    }).done(function (data) {
        $("#SelectUsu").html(data);
    }).fail(function (xhr, status, error) {
        console.error("Error cargando combo_curso:", {
            status: status,
            error: error,
            response: xhr.responseText
        });
        Swal.fire('Error', 'No se pudo cargar la lista de cursos.', 'error');
    });
}





