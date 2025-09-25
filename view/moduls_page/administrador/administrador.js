var admin_id = $('#admin_idx').val();

function init() {
    $("#form_administrador").on("submit", function (e) {
        guardaryeditar(e);
    });
}

function guardaryeditar(e) {
    e.preventDefault();

    var formData = new FormData($("#form_administrador")[0]);

    $.ajax({
        url: BASE_URL + 'controller/usuario.php?op=insert_update_admin',
        type: "POST",
        data: formData,
        contentType: false, // necesario para enviar archivos
        processData: false,
        xhrFields: { withCredentials: true }, // si usas sesiones
        dataType: 'json',

        success: function (response) {
            console.log("✅ Respuesta JSON del servidor:", response);

            if (response.status === 'success') {
                // Todo correcto: cerramos modal, limpiamos formulario y recargamos la tabla
                $('#administrador_data').DataTable().ajax.reload(null, false);
                $('#modal_administrador').modal('hide');
                $('#form_administrador')[0].reset();
                borrarPreview(); // función para limpiar imagen

                Swal.fire({
                    title: 'Correcto!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                // Error del servidor (validación, etc.): no cerrar el modal
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }
        },

        error: function (xhr, status, error) {
            console.error("❌ Error AJAX:");
            console.error("Status:", status);
            console.error("Error:", error);
            console.error("Respuesta del servidor (texto crudo):", xhr.responseText);

            Swal.fire({
                title: 'Error!',
                text: 'Hubo un problema con la solicitud. Revisa la consola para más detalles.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}




$(document).ready(function () {
    const table = $('#administrador_data').DataTable({  // <- Aquí defines la variable
        aProcessing: true,
        aServerSide: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'copyButton',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: 'excel',
                className: 'excelButton',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: 'csv',
                className: 'csvButton',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: 'pdf',
                className: 'pdfButton',
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            }
        ],
        ajax: {
            url: BASE_URL + 'controller/usuario.php?op=listar_admin',
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

    // Botones personalizados
    $('#btnCopiar-admin').on('click', function () {
        table.button('.copyButton').trigger();
    });

    $('#btnExcel-admin').on('click', function () {
        table.button('.excelButton').trigger();
    });

    $('#btnCSV-admin').on('click', function () {
        table.button('.csvButton').trigger();
    });

    $('#btnPDF-admin').on('click', function () {
        table.button('.pdfButton').trigger();
    });

    // Búsqueda personalizada
    $('#search-user-input-admin').on('keyup change', function () {
        table.search(this.value).draw();
    });
        function updateCustomPagination() {
        const pageInfo = table.page.info();
        const currentPage = pageInfo.page;
        const totalPages = pageInfo.pages;
        const start = pageInfo.start + 1;
        const end = pageInfo.end;
        const total = pageInfo.recordsDisplay;

        $('#pagination-start-admin').text(start);
        $('#pagination-end-admin').text(end);
        $('#pagination-total-admin').text(total);

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


function eliminar_admin(ID_administrador) {
    Swal.fire({
        title: "Eliminar!",
        text: "¿Desea inactivar al administrador?",
        icon: "warning",
        confirmButtonText: "Sí",
        showCancelButton: true,
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(BASE_URL + 'controller/usuario.php?op=delete_admin', 
                { ID_administrador: ID_administrador }, 
                function (response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $('#administrador_data').DataTable().ajax.reload();
                        Swal.fire({
                            title: 'Correcto!',
                            text: 'Se inactivo correctamente',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'No se pudo eliminar el administrador.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                }
            ).fail(function () {
                Swal.fire({
                    title: 'Error!',
                    text: 'Hubo un problema en el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    });
}

function restablecer_admin(ID_administrador) {
    Swal.fire({
        title: "Restablecer Administrador!",
        text: "¿Desea activar al administrador?",
        icon: "warning",
        confirmButtonText: "Sí",
        showCancelButton: true,
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(BASE_URL + 'controller/usuario.php?op=restablecer_admin', 
                { ID_administrador: ID_administrador }, 
                function (response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $('#administrador_data').DataTable().ajax.reload();
                        Swal.fire({
                            title: 'Correcto!',
                            text: 'Se activo correctamente',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'No se pudo activar el administrador.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                }
            ).fail(function () {
                Swal.fire({
                    title: 'Error!',
                    text: 'Hubo un problema en el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    });
}
function editar_admin(ID_administrador) {
    $.post(BASE_URL + 'controller/usuario.php?op=mostrar_admin_id', { ID_administrador: ID_administrador }, function (data) {
        if (data.error) {
            alert(data.error);
            return;
        }

        // Rellenar campos del formulario
        $("#ID_administrador").val(data.ID_administrador);
        $('#nom_administrador').val(data.nom_admin);
        $('#ape_paterno').val(data.ape_paterno);
        $('#ape_materno').val(data.ape_materno);
        $('#sexo').val(data.sexo || '').trigger("change");
        $('#permiso').val(data.tipo_permiso || '').trigger("change");
        $('#telefono').val(data.telefono);
        $('#correo').val(data.correo);

        // Imagen
        if (data.foto && data.foto !== "") {
            let rutaImagen = BASE_URL + "public/img/img_AdGe/" + data.foto;
            $("#imagenPreview").attr("src", rutaImagen).show();
            $("#btnBorrarImagen").show();
        } else {
            $("#imagenPreview").hide();
            $("#btnBorrarImagen").hide();
        }

        // Mostrar título y modal
        $('#lbltituloadministrador').html('Editar Registro');
        $('#modal_administrador').modal('show');

        // === CONTROL DE CONTRASEÑA ===

        // Ocultar el campo de password (solo se usa en registro)
        $("#divPasswordRegistro").hide();

        // Mostrar el botón "Cambiar contraseña"
        $("#divBtnCambiarPassword").show();
        $("#btnCambiarPassword").show();

        // Ocultar los campos de cambio de contraseña (inicialmente)
        $("#divCambiarPassword").hide();

        // Limpiar los campos de cambio de contraseña
        $("#password_actual").val('');
        $("#password_nueva").val('');
        $("#password_nueva_repetir").val('');
        
    }, "json").fail(function () {
        alert("Error al cargar los datos del administrador.");
    });
}



function nuevo() {
    $('#ID_administrador').val('');
    $('#lbltituloadministrador').html('Nuevo Registro');
    $('#form_administrador')[0].reset();
    $('#modal_administrador').modal('show');
    $("#divBtnCambiarPassword").hide();
    $("#divCambiarPassword").hide();
    $("#divPasswordRegistro").show();
}
// Al hacer clic en "Cambiar contraseña"
$("#btnCambiarPassword").on("click", function () {
  // Mostrar u ocultar los campos
  $("#divCambiarPassword").slideToggle("fast");
});


init();

