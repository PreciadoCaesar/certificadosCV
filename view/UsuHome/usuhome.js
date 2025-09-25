$(document).ready(function () {
    $("#divpanel").hide();
    $("#perfilcard").hide();

    $("#btnconsultar").on("click", function () {
        const usu_dni = $("#documentNumber").val().trim();

        if (!usu_dni) {
            Swal.fire({
                title: 'Error!',
                text: 'DNI Vacío',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        $.post(BASE_URL + 'controller/usuario.php?op=consulta_dni', { usu_dni }, function (data) {
            console.log("Respuesta recibida:", data);

            if (data && data.ID_usuario) {
                const usu_id = data.ID_usuario;

                // Mostrar información del usuario
                $("#documento").text("DNI");
                $("#numero").text(data.dni);
                $("#nombre").text(data.nom_usuario);
                $("#apellidos").text(`${data.ape_paterno} ${data.ape_materno}`);

                let foto = data.foto ? data.foto : 'default.png';
                $("#img_perfil").attr("src", BASE_URL + `public/img/img_usuario/${foto}`);

                $("#perfilcard").show();
                $("#divpanel").show();
                $("#divpasos").hide();

                // Destruye la instancia previa si existe
                if ($.fn.DataTable.isDataTable('#cursos_data')) {
                    $('#cursos_data').DataTable().destroy();
                }

                $('#cursos_data').DataTable({
                    ajax: {
                        url: BASE_URL + "controller/usuario.php?op=listar_cursos_top10",
                        type: "POST",
                        data: { usu_id: usu_id },
                        dataSrc: 'aaData'
                    },
                    paging: false,
                    searching: false,
                    info: false,
                    ordering: true,
                    order: [[0, "desc"]],
                    dom: 'Bfrt',
                });
            }
        }, "json");
    });
});

// Abrir certificado en nueva pestaña
function certificado(curd_id) {
    console.log("ID del certificado:", curd_id);
    window.open(BASE_URL + 'view/Certificado/index.php?curd_id=' + curd_id, '_blank');
}

// Descargar temario
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


  
  
