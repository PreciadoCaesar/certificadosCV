$(document).ready(function() {

// 1. Tabla de Cursos
// Inicialización de la tabla
let tablaCursos = $('#tabla_cursos').DataTable({
  dom: 'Bfrtip',
  buttons: [
    {
      extend: 'copyHtml5',
      className: 'd-none',
      title: 'Los 10 últimos cursos añadidos',
      messageTop: 'Listado de los 10 cursos más recientes',
      exportOptions: {
        columns: [1, 2, 3, 4]
      }
    },
    {
      extend: 'excelHtml5',
      className: 'd-none',
      title: 'Los 10 últimos cursos añadidos',
      messageTop: 'Listado de los 10 cursos más recientes',
      exportOptions: {
        columns: [1, 2, 3, 4]
      }
    },
    {
      extend: 'csvHtml5',
      className: 'd-none',
      title: 'Los 10 últimos cursos añadidos',
      messageTop: 'Listado de los 10 cursos más recientes',
      exportOptions: {
        columns: [1, 2, 3, 4]
      }
    },
    {
      extend: 'pdfHtml5',
      className: 'd-none',
      title: 'Los 10 últimos cursos añadidos',
      messageTop: 'Listado de los 10 cursos más recientes',
      pageSize: 'A4',
      exportOptions: {
        columns: [1, 2, 3, 4]
      }
    }
  ],
  ajax: {
    url: BASE_URL + 'controller/curso.php?op=get_last_10',
    type: "GET",
    dataSrc: ""
  },
  columns: [
    { data: "foto" },
    { data: "cur_nom" },
    { data: "cur_fechini" },
    { data: "cur_fechfin" },
    { data: "inst_nom" },
    { data: "certificado" }
  ],
  language: {
    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
  }
});

// Botones externos enlazados correctamente
tablaCursos.on('init.dt', function () {
  $('#btnCopiar-inicursos').on('click', function () {
    tablaCursos.buttons().button(0).trigger();
  });
  $('#btnExcel-inicursos').on('click', function () {
    tablaCursos.buttons().button(1).trigger();
  });
  $('#btnCSV-inicursos').on('click', function () {
    tablaCursos.buttons().button(2).trigger();
  });
  $('#btnPDF-inicursos').on('click', function () {
    tablaCursos.buttons().button(3).trigger();
  });
});

// Búsqueda externa correctamente enlazada
$('#search-user-input-inicursos').on('keyup', function () {
  tablaCursos.search(this.value).draw();
});





  
  // 2. Tabla de Instructores
  let tablaInstructores = $('#tabla_instructores').DataTable({
    dom: "<'row'<'col text-center'B>>frtip",
   buttons: [
    {
      extend: 'copyHtml5',
      className: 'd-none',
      title: 'Los 10 últimos instructores añadidos',
      messageTop: 'Listado de los 10 instructores más recientes',
      exportOptions: {
        columns: [1, 2, 3, 4, 5]
      }
    },
    {
      extend: 'excelHtml5',
      className: 'd-none',
      title: 'Los 10 últimos instructores añadidos',
      messageTop: 'Listado de los 10 instructores más recientes',
      exportOptions: {
        columns: [1, 2, 3, 4, 5]
      }
    },
    {
      extend: 'csvHtml5',
      className: 'd-none',
      title: 'Los 10 últimos instructores añadidos',
      messageTop: 'Listado de los 10 instructores más recientes',
      exportOptions: {
        columns: [1, 2, 3, 4, 5]
      }
    },
    {
      extend: 'pdfHtml5',
      className: 'd-none',
      title: 'Los 10 últimos instructores añadidos',
      messageTop: 'Listado de los 10 instructores más recientes',
      pageSize: 'A4',
      exportOptions: {
        columns: [1, 2, 3, 4, 5]
      }
    }
  ],
    ajax: {
      url: BASE_URL + 'controller/instructor.php?op=nuevos_instructores',
      type: "GET",
      dataSrc: ""
    },
    columns: [
      { data: "foto" },
      { data: "nom_instructor" },
      { data: "ape_paterno" },
      { data: "ape_materno" },
      { data: "correo" },
      { data: "telefono" }
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
    }
  });

      tablaInstructores.on('init.dt', function () {
  $('#btnCopiar-iniinst').on('click', function () {
    tablaInstructores.buttons().button(0).trigger();
  });
  $('#btnExcel-iniinst').on('click', function () {
    tablaInstructores.buttons().button(1).trigger();
  });
  $('#btnCSV-iniinst').on('click', function () {
    tablaInstructores.buttons().button(2).trigger();
  });
  $('#btnPDF-iniinst').on('click', function () {
    tablaInstructores.buttons().button(3).trigger();
  });

  $('#search-user-input-iniinst').on('keyup', function () {
    tablaInstructores.search(this.value).draw();
  });
});



  // 3. Tabla de Usuarios Activos
  let tablaUsuarios = $('#tabla_usuarios').DataTable({
    dom: "<'row'<'col text-center'B>>frtip",
   buttons: [
    {
      extend: 'copyHtml5',
      className: 'd-none',
      title: 'Los 10 últimos administradores añadidos activos',
      messageTop: 'Listado de los 10 administradores más recientes y activos',
      exportOptions: {
        columns: [1, 2, 3, 4, 5] // omitimos la columna 0 (foto)
      }
    },
    {
      extend: 'excelHtml5',
      className: 'd-none',
      title: 'Los 10 últimos administradores añadidos activos',
      messageTop: 'Listado de los 10 administradores más recientes y activos',
      exportOptions: {
        columns: [1, 2, 3, 4, 5]
      }
    },
    {
      extend: 'csvHtml5',
      className: 'd-none',
      title: 'Los 10 últimos administradores añadidos activos',
      messageTop: 'Listado de los 10 administradores más recientes y activos',
      exportOptions: {
        columns: [1, 2, 3, 4, 5]
      }
    },
    {
      extend: 'pdfHtml5',
      className: 'd-none',
      title: 'Los 10 últimos administradores añadidos activos',
      messageTop: 'Listado de los 10 administradores más recientes y activos',
      pageSize: 'A4',
      exportOptions: {
        columns: [1, 2, 3, 4, 5]
      }
    }
  ],
    lengthChange: false,
    searching: true,
    info: false,
    paging: true,
    responsive: true,
    ajax: {
      url: BASE_URL + 'controller/usuario.php?op=usuarios_activos',
      type: "GET",
      dataSrc: ""
    },
    columns: [
      { data: "foto" },
      { data: "nom_admin" },
      { data: "ape_paterno" },
      { data: "ape_materno" },
      { data: "correo" },
      { data: "telefono" }
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
    }
  });

// Activar botones y búsqueda al cargar DataTable
tablaUsuarios.on('init.dt', function () {
  $('#btnCopiar-iniadmin').on('click', function () {
    tablaUsuarios.buttons().button(0).trigger();
  });
  $('#btnExcel-iniadmin').on('click', function () {
    tablaUsuarios.buttons().button(1).trigger();
  });
  $('#btnCSV-iniadmin').on('click', function () {
    tablaUsuarios.buttons().button(2).trigger();
  });
  $('#btnPDF-iniadmin').on('click', function () {
    tablaUsuarios.buttons().button(3).trigger();
  });

  $('#search-user-input-iniadmin').on('keyup', function () {
    tablaUsuarios.search(this.value).draw();
  });
});
});

// Funciones para mostrar/ocultar secciones
function mostrarCursos() {
  document.getElementById("cursos").style.display = "block";
  document.getElementById("instructores").style.display = "none";
  document.getElementById("usuarios").style.display = "none";
}

function mostrarInstructores() {
  document.getElementById("cursos").style.display = "none";
  document.getElementById("instructores").style.display = "block";
  document.getElementById("usuarios").style.display = "none";
}

function mostrarUsuarios() {
  document.getElementById("cursos").style.display = "none";
  document.getElementById("instructores").style.display = "none";
  document.getElementById("usuarios").style.display = "block";
}

$(document).ready(function () {
    $.post(BASE_URL + 'controller/curso.php?op=contadoresDashboard', function (data) {
        let res = JSON.parse(data);
        $("#cantidad-cursos").text(res.cursos + " cursos");
        $("#cantidad-instructores").text(res.instructores + " instructores");
        $("#cantidad-usuarios").text(res.usuarios + " usuarios");
    });
});


function CertificadoModelo(ID_curso){
  console.log(ID_curso);
  window.open('CertificadoModelo/index.php?ID_curso='+ ID_curso +'','_blank');
}

function descargarDocumento(temario) {
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

