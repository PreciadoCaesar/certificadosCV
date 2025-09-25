var admin_id = $('#admin_idx').val();

function init(){
    $("#form_usuario").on("submit", function(e){
        guardaryeditar(e);
    });
}


function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#form_usuario")[0]);
    $('#correo_usuario, #dni_usuario').removeClass('is-invalid');

    $.ajax({
        url: BASE_URL + 'controller/usuario.php?op=guardaryeditar',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        xhrFields: { withCredentials: true },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#usuario_data').DataTable().ajax.reload(null, false);
                $('#modal_usuario').modal('hide');
                $('#form_usuario')[0].reset();
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
                    $('#correo_usuario').addClass('is-invalid').focus();
                }
                if (response.message.toLowerCase().includes('dni')) {
                    $('#dni_usuario').addClass('is-invalid');
                }
        }},
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


//lllllll
$(document).ready(function () {
     const table = $('#usuario_data').DataTable({
        aProcessing: true,
        aServerSide: true,
        dom: 'Bfrtip',
       buttons: [
    {
        extend: 'copy',
        className: 'copyButton',
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6]
        }
    },
    {
        extend: 'excel',
        className: 'excelButton',
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6]
        }
    },
    {
        extend: 'csv',
        className: 'csvButton',
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6]
        }
    },
    {
        extend: 'pdf',
        className: 'pdfButton',
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6]
        }
    }
],
        ajax: {
            url: BASE_URL + 'controller/usuario.php?op=listar',
            type: 'POST',
            dataType: 'json',
            xhrFields: { withCredentials: true }
        },
        destroy: true,
        responsive: true,
        info: true,
        pageLength: 10,
        order: [[0, "desc"]],
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
            },
            oAria: {
                sSortAscending: ": Activar para ordenar la columna ascendente",
                sSortDescending: ": Activar para ordenar la columna descendente"
            }
        }
    });

    // Funciones para los botones personalizados
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


    // Buscar con el input personalizado
    $('#search-user-input').on('keyup change', function () {
        table.search(this.value).draw();
    });


  function updateCustomPagination() {
    const pageInfo = table.page.info();
    const currentPage = pageInfo.page;
    const totalPages = pageInfo.pages;
    const start = pageInfo.start + 1;
    const end = pageInfo.end;
    const total = pageInfo.recordsDisplay;

    // Actualizar los spans de texto
    $('#pagination-start').text(start);
    $('#pagination-end').text(end);
    $('#pagination-total').text(total);

    // Contenedor de paginación
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

    // Rango de páginas (máx 5 visibles con "...")
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

    // Botones numéricos
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


   table.on('draw', function () {
    updateCustomPagination();
});

});



function editar(usu_id) {
    $.post(BASE_URL + 'controller/usuario.php?op=mostrar', { usu_id: usu_id }, function (data) {
        $('#correo_usuario, #dni_usuario').removeClass('is-invalid');
        $("#usu_id").val(data.ID_usuario);
        $('#nombre_usuario').val(data.nom_usuario);
        $('#ape_paterno_usuario').val(data.ape_paterno);
        $('#ape_materno_usuario').val(data.ape_materno);
        $('#dni_usuario').val(data.dni);
        $('#sexo_usuario').val(data.sexo || '').trigger("change");
        $('#telefono_usuario').val(data.telefono);
        $('#correo_usuario').val(data.correo);

        if (data.foto && data.foto !== "") {
            let rutaImagen = BASE_URL + "public/img/img_usuario/" + data.foto + "?v=" + new Date().getTime();
            $("#imagenPreview").attr("src", rutaImagen).show();
            $("#btnBorrarImagen").show();
        } else {
            $("#imagenPreview").hide();
            $("#btnBorrarImagen").hide();
        }


        $('#lbltitulo').html('Editar Registro');
        $('#modal_usuario').modal('show');
    }, "json");
}


function eliminar(usu_id) {
    Swal.fire({
        title: "Eliminar!",
        text: "¿Desea eliminar el registro?",
        icon: "error",
        confirmButtonText: "Si",
        showCancelButton: true,
        cancelButtonText: "No",
    }).then((result) => {
        if (result.value) {
            $.post(BASE_URL + 'controller/usuario.php?op=eliminar', { usu_id: usu_id }, function () {
                $('#usuario_data').DataTable().ajax.reload();

                Swal.fire({
                    title: 'Correcto!',
                    text: 'Se eliminó correctamente',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            }).fail(function () {
                Swal.fire({
                    title: 'Error!',
                    text: 'Hubo un problema al eliminar',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    });
}
function verImagen(usu_id) {
    $.post(BASE_URL + 'controller/usuario.php?op=mostrar_foto', { usu_id: usu_id }, function (data) {
        if (data.foto && data.foto.trim() !== "") {
            let imgPath = BASE_URL + "public/img/img_usuario/" + data.foto;

            $.get(imgPath)
                .done(function () {
                    $("#fotoModal").attr("src", imgPath);
                    $("#modal_visualizar_foto").modal("show");
                    $("#fotoLabel").text("Foto de Perfil de " + (data.usu_nom || "Usuario"));
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
                text: data.mensaje || 'El usuario no tiene una imagen registrada.',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        }
    }, "json");
}


function nuevo() {
    $('#usu_id').val('');
    $('#lbltitulousuario').html('Nuevo Registro');
    $('#form_usuario')[0].reset();
    $('#modal_usuario').modal('show');
    $('#correo_usuario, #dni_usuario').removeClass('is-invalid');
    borrarPreview();
}

init();
