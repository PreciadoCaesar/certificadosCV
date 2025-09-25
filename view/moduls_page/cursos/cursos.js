var admin_id = $('#admin_idx').val();

$(document).ready(function () {
    const table = $('#cursos_data').DataTable({
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
],
        ajax: {
            url: BASE_URL + 'controller/curso.php?op=listar',
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
// ✅ Llamada a init()
    init();

});
// ==============================
// GUARDAR / EDITAR
// ==============================
function init(){
    $("#form_curso").on("submit", function(e){
        guardaryeditar(e);
    });
}

function guardaryeditar(e) {
    e.preventDefault();

    const formData = new FormData($("#form_curso")[0]);

    console.group("📦 Formulario Enviado");
    for (let [key, value] of formData.entries()) {
        console.log(`${key}:`, value);
    }
    console.groupEnd();

    $.ajax({
        url: BASE_URL + 'controller/curso.php?op=guardaryeditar',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        xhrFields: { withCredentials: true },
        beforeSend: function () {
            console.log("⏳ Enviando datos al servidor...");
        },
        success: function (response) {
            console.log("✅ Respuesta del servidor:", response);
            if (response.status === 'success') {
                $('#cursos_data').DataTable().ajax.reload(null, false);
                $('#modal_curso').modal('hide');
                $('#form_curso')[0].reset();
                borrarPreview();
                Swal.fire({
                    icon: 'success',
                    title: '¡Correcto!',
                    text: response.message
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: response.message
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ Error AJAX");
            console.log("Status:", status);
            console.log("Error:", error);
            console.log("Respuesta:", xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'Error en el servidor',
                html: `
                    <strong>Status:</strong> ${status}<br>
                    <strong>Error:</strong> ${error}<br>
                    <pre style="text-align:left; white-space:pre-wrap;">${xhr.responseText || "Sin respuesta del servidor"}</pre>
                `,
                width: '60%'
            });
        }
    });
}


function editar(cur_id) {
    // Llamamos primero a los combos
    $.when(combo_categoria(), combo_instructor()).done(function () {
        // Luego de que se cargan ambos combos, obtenemos los datos del curso
        $.ajax({
            url: BASE_URL + 'controller/curso.php?op=mostrar',
            type: "POST",
            data: { cur_id: cur_id },
            dataType: "json",
            xhrFields: { withCredentials: true }
        }).done(function (data) {
            $('#cur_id').val(data.ID_curso);
            $('#SelectCategoria').val(data.ID_categoria).trigger('change');
            $('#nom_curso').val(data.nom_curso);
            $('#inicio_curso').val(data.fecha_inicio);
            $('#final_curso').val(data.fecha_fin);
            $('#SelectInstructor').val(data.ID_instructor).trigger('change');
            $('#horas_curso').val(data.horas);

            if (data.foto) {
                let imgUrl = BASE_URL + 'public/img/img_curso/' + data.foto + '?v=' + new Date().getTime();
                $("#imagenPreview").attr("src", imgUrl).show();
                $("#btnBorrarImagen").show();
            } else {
                $("#imagenPreview, #btnBorrarImagen").hide();
            }


            $('#lbl_titulocurso').html('Editar Registro');
            $('#modal_curso').modal('show');
        }).fail(function () {
            Swal.fire('Error!', 'No se pudo cargar datos del curso', 'error');
        });
    });
}


function eliminar(cur_id){
    Swal.fire({
    title: "¡Eliminar!",
    html: `
        ¿Desea eliminar el registro?
        <div style="margin-top: 10px; padding: 10px; background-color: #f8d7da; color: #842029; border-radius: 4px;">
            <strong>Advertencia:</strong> Si eliminas el curso, también se eliminará su certificación y con ello las personas que estaban con la certificación.
        </div>
    `,
    icon: "warning",
    confirmButtonText: "Sí",
    showCancelButton: true,
    cancelButtonText: "No",
}).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + 'controller/curso.php?op=eliminar',
                type: "POST",
                data: { cur_id: cur_id },
                dataType: "json",
                xhrFields: { withCredentials: true }
            }).done(function(){
                $('#cursos_data').DataTable().ajax.reload();
                Swal.fire('Correcto!', 'Se eliminó correctamente', 'success');
            }).fail(function(){
                Swal.fire('Error!', 'No se pudo eliminar el curso', 'error');
            });
        }
    });
}

function nuevo() {
    $('#lbl_titulocurso').html('Nuevo Registro');
    $('#form_curso')[0].reset();
    borrarPreview();

    // Esperar a que ambos combos terminen de cargarse
    $.when(combo_categoria(), combo_instructor()).done(function () {
        // Seleccionar vacío por defecto
        $('#SelectCategoria').val('').trigger('change');
        $('#SelectInstructor').val('').trigger('change');

        // Mostrar el modal una vez que todo esté listo
        $('#modal_curso').modal('show');
    });
}



function combo_categoria() {
    return $.ajax({
        url: BASE_URL + 'controller/categoria.php?op=combo',
        type: "POST",
        dataType: "html",
        xhrFields: { withCredentials: true },
        success: function(data) {
            $("#SelectCategoria").html(data);
        }
    });
}

function combo_instructor() {
    return $.ajax({
        url: BASE_URL + 'controller/instructor.php?op=combo',
        type: "POST",
        dataType: "html",
        xhrFields: { withCredentials: true },
        success: function(data) {
            $("#SelectInstructor").html(data);
        }
    });
}


function verImagenCurso(curso_id) {
    $.post(BASE_URL + 'controller/curso.php?op=mostrar_foto', { curso_id: curso_id }, function (data) {
        if (data.foto && data.foto.trim() !== "") {
            let imgPath = BASE_URL + "public/img/img_curso/" + data.foto;

            $.get(imgPath)
                .done(function () {
                    $("#fotoModal_curso").attr("src", imgPath);
                    $("#modal_foto_curso").modal("show");
                    $("#fotoLabel").text(data.curso_nom || "Curso");
                })
                .fail(function () {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje || 'La imagen del curso no se encuentra en el servidor.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                });
        } else {
            Swal.fire({
                title: 'Aviso',
                text: data.mensaje || 'El curso no tiene una imagen registrada.',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        }
    }, "json");
}


// Certificado
function guardarCertificado() {
    let formData = new FormData(document.getElementById('formSubirCertificado'));
    $.ajax({
        url: BASE_URL + 'controller/curso.php?op=guardar_certificado',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        xhrFields: { withCredentials: true },
        success: function() {
            Swal.fire('Éxito', 'Certificado guardado correctamente', 'success').then(() => {
                $('#modalSubirCertificado').modal('hide');
                // Recargar tabla
                $('#cursos_data').DataTable().ajax.reload(null, false); // false = no reinicia la paginación
            });
        },
        error: function() {
            Swal.fire('Error', 'Hubo un problema al guardar el certificado', 'error');
        }
    });
}


// Documento
function guardarDocumento() {
  const form = document.getElementById('formSubirDocumento');
  const formData = new FormData(form);
  const idCurso = formData.get('id_curso');

  if (!idCurso) {
    Swal.fire('Error', 'No se especificó el curso.', 'error');
    return;
  }

  $.ajax({
    url: BASE_URL + 'controller/curso.php?op=guardar_documento',
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    xhrFields: { withCredentials: true },
    success: function(response) {
      let res = {};
      try {
        res = JSON.parse(response);
      } catch {
        Swal.fire('Error', 'Respuesta no válida del servidor', 'error');
        return;
      }

      if (res.status === 'success') {
        Swal.fire('Éxito', res.message, 'success').then(() => {
          const modalElemento = document.getElementById('modalSubirDocumento');
          const modalInstance = bootstrap.Modal.getInstance(modalElemento);
          if (modalInstance) modalInstance.hide();

          // Recargar la tabla
          $('#cursos_data').DataTable().ajax.reload(null, false); // Mantiene la página actual
        });
      } else {
        Swal.fire('Error', res.message || 'Error desconocido', 'error');
      }
    },
    error: function() {
      Swal.fire('Error', 'Hubo un problema al guardar el documento', 'error');
    }
  });
}


function descargarDocumento_curso(temario) {
    if (!temario || temario.trim() === "") {
        Swal.fire({
            title: "Sin Temario",
            text: "Este curso no tiene temario disponible.",
            icon: "warning",
            confirmButtonText: "Aceptar"
        });
        return;
    }

    // Validar si el archivo existe
    $.post(BASE_URL + 'controller/curso.php?op=verificarDocumento', { archivo: temario }, function (response) {
        if (response.existe) {
            Swal.fire({
                title: "Ver Temario",
                text: "¿Desea abrir el temario en una nueva pestaña?",
                icon: "info",
                showCancelButton: true,
                confirmButtonText: "Sí",
                cancelButtonText: "No"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(BASE_URL + 'controller/curso.php?op=descargarDocumento&archivo=' + encodeURIComponent(temario), '_blank');

                    Swal.fire({
                        title: 'Abierto',
                        text: 'El temario se ha abierto en una nueva pestaña.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        } else {
            Swal.fire({
                title: "Archivo no encontrado",
                text: "El temario solicitado no está disponible.",
                icon: "error",
                confirmButtonText: "Aceptar"
            });
        }
    }, "json");
}













