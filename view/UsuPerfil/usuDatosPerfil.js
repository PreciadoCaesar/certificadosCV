$(document).ready(function () {
  MostrarPerfil();
  ActualizarPerfil();
});

function MostrarPerfil() {
  $('#DatosPerfil_modal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const adminId = button.data('id');
    $('#id_admin').val(adminId);

    if (adminId) {
      $.post(
        BASE_URL + "controller/usuario.php?op=mostrar_perfil_admin",
        { ID_administrador: adminId },
        function (data) {
          if (Array.isArray(data) && data.length > 0) {
            const perfil = data[0];
            $('#DatosPerfil_nom').val(perfil.nom_admin || '');
            $('#DatosPerfil_apep').val(perfil.ape_paterno || '');
            $('#DatosPerfil_apem').val(perfil.ape_materno || '');
            $('#DatosPerfil_sexo').val(perfil.sexo || '').trigger("change");
            $('#DatosPerfil_telf').val(perfil.telefono || '');
            $('#DatosPerfil_correo').val(perfil.correo || '');

            // Asignar ruta imagen y actualizar preview y botones
            if (perfil.foto) {
              const rutaImagen = BASE_URL + "public/img/img_AdGe/" + perfil.foto;
              $("#PrevisualizadoImg").attr("src", rutaImagen).show();
              $("#btnBorrar").show();
              $("#imagenOriginal").val(perfil.foto); // importante
            } else {
              const rutaDefault = BASE_URL + "public/img/img_AdGe/default.png";
              $("#PrevisualizadoImg").attr("src", rutaDefault).hide();
              $("#btnBorrar").hide();
              $("#imagenOriginal").val("");
            }
          } else {
            Swal.fire("Error", "No se encontraron datos del administrador.", "error");
          }
        },
        'json'
      ).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error AJAX:", textStatus, errorThrown);
        Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
      });
    }
  });
}


function ActualizarPerfil() {
  // Eliminamos cualquier evento submit anterior antes de asignar uno nuevo
  $('#form_DatosPerfil').off('submit').on('submit', function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    $.ajax({
      url: BASE_URL + "controller/usuario.php?op=editar_perfil_admin",
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (response) {
        console.log(response);

        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'Perfil actualizado correctamente.',
            confirmButtonText: 'Aceptar'
          }).then(() => {
            $('#DatosPerfil_modal').modal('hide');
            location.reload(); // Si deseas evitar el reload, puedes actualizar los campos manualmente
          });
        } else {
          Swal.fire("Error", response.message || "Error al actualizar perfil.", "error");
        }
      },
      error: function () {
        Swal.fire("Error", "Error en la conexión con el servidor.", "error");
      }
    });
  });
}



