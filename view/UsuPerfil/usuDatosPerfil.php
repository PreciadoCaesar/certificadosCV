<?php 
require_once("../../config/conexion.php");
?>

<!-- MODAL REGISTRAR Y EDITAR -->
<div class="modal fade" id="DatosPerfil_modal" tabindex="-1" aria-labelledby="DatosPerfilPopupLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="header-section d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="md-headline-small" id="lbltitulo">Editar Perfil de Administrador</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
            <span aria-hidden="true">&times;</span>
      </button>      
      </div>
      <div class="modal-body">
        <form method="post" id="form_DatosPerfil" enctype="multipart/form-data">
        <input type="hidden" id="id_admin" name="id_admin" value="<?= $_SESSION["ID_administrador"] ?>">

          <ul class="nav nav-tabs mb-3" id="profileTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="datos-DatosPerfil-tab" data-bs-toggle="tab" href="#datos-DatosPerfil" role="tab">Datos Personales</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="info-DatosPerfil-tab" data-bs-toggle="tab" href="#info-DatosPerfil" role="tab">Información de Usuario</a>
            </li>
          </ul>

          <div class="tab-content" id="profileTabContent">
            <!-- DATOS PERSONALES -->
            <div class="tab-pane fade show active" id="datos-DatosPerfil" role="tabpanel">
              <div class="form-layout">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Nombres:</label>
                    <input type="text" class="form-control md-label-medium" id="DatosPerfil_nom" name="DatosPerfil_nom" placeholder="Ingrese su nombre" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Apellido Paterno:</label>
                    <input type="text" class="form-control md-label-medium" id="DatosPerfil_apep" name="DatosPerfil_apep" placeholder="Apellido Paterno" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Apellido Materno:</label>
                    <input type="text" class="form-control md-label-medium" id="DatosPerfil_apem" name="DatosPerfil_apem" placeholder="Apellido Materno" required>
                  </div>
                  <div class="col-12 col-md-6">
                   <label class="md-label-large">Sexo: <span class="tx-danger"></span></label>
                   <select class="md-label-large select-input" style="width:100%" name="DatosPerfil_sexo" id="DatosPerfil_sexo" required>
                    <option value="" disabled selected>Seleccione</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                  </select>
                 </div> 
                 <!-- Input hidden para mantener la imagen original -->
                <input type="hidden" id="imagenOriginal" name="imagen_original" value="nombre_imagen_actual.jpg">

                <div class="col-12 col-md-6">
                  <label class="md-label-large">Fotografía:</label>
                  <div class="input-group">
                    <img id="PrevisualizadoImg" src="" class="img-profile"
                      style="width: 150px; height: 150px; display: none; margin-bottom: 10px; cursor: pointer;"
                      onclick="seleccionarImagen()">
                    <div>
                      <button type="button" class="button button--primary md-label-medium" onclick="seleccionarImagen()">Seleccionar Imagen</button>
                      <br><br>
                      <button type="button" class="button button--outlined md-label-medium" id="btnBorrar" onclick="borrarPreview()" style="display: none;">
                        Borrar Imagen
                      </button>
                    </div>
                    <input type="file" id="InputImg" name="foto_adminperfil" accept=".jpg, .jpeg, .png"
                      style="display: none;" onchange="previsualizarImagen(event)">
                  </div>
                </div>
                </div>
              </div>
            </div>

            <!-- INFORMACIÓN DE USUARIO -->
            <div class="tab-pane fade" id="info-DatosPerfil" role="tabpanel">
              <div class="form-layout">
                <div class="row g-3">
                   <div class="col-12 col-md-6">
                    <label class="md-label-large">Correo Electrónico:</label>
                    <input type="email" class="form-control md-label-medium" id="DatosPerfil_correo" name="DatosPerfil_correo" placeholder="Ingrese su correo" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Teléfono:</label>
                    <input type="number" class="form-control md-label-medium" id="DatosPerfil_telf" name="DatosPerfil_telf" placeholder="Ingrese su teléfono" required>
                  </div>
                 
                  <div class="col-12 col-md-6 mb-3">
  <!-- Botón para mostrar formulario de cambio -->
                <label class="md-label-large">Contraseña:</label><br>
                <button type="button" class="button button--primary md-label-medium" id="btn-toggle-password-form">
                  Cambiar contraseña
                </button>
                <!-- Formulario oculto inicialmente -->
                <div id="form-cambiar-password" class="mt-3" style="display: none;">
                <label class="md-label-large" style="background-color: #f8d7da; padding: 5px; color: #721c24;">Si deseas cambiar la contraseña, debes rellenar correctamente los 3 campos</label>
 
                <div class="mb-3">
                  <label for="password_actual" class="md-label-large">Contraseña actual:</label>
                  <input type="password" class="form-control md-label-medium" id="password_actual" name="password_actual" placeholder="Ingrese su contraseña actual" autocomplete="new-password">
                </div>

                <div class="mb-3">
                  <label for="nueva_password" class="md-label-large">Nueva contraseña:</label>
                  <input type="password" class="form-control md-label-medium" id="nueva_password" name="nueva_password" placeholder="Nueva contraseña" autocomplete="new-password">
                </div>

                <div class="mb-3">
                  <label for="repite_password" class="md-label-large">Repite la nueva contraseña:</label>
                  <input type="password" class="form-control md-label-medium" id="repite_password" name="repite_password" placeholder="Repite la nueva contraseña" autocomplete="new-password">
                </div>
              </div>
              </div>
                </div>
              </div>
            </div>
          </div>

          <!-- BOTONES -->
          <div class="modal-footer">
            <button type="reset" class="button button--outlined md-label-medium" data-bs-dismiss="modal" id="btncancelar-DatosPerfil">Cancelar</button>
            <button type="button" class="button button--primary md-label-medium" id="btnsiguiente-DatosPerfil">Siguiente</button>
            <button type="button" class="button button--outlined md-label-medium d-none" id="btnatras-DatosPerfil">Atrás</button>
            <button type="submit" class="button button--primary md-label-medium d-none" id="btnactualizar-DatosPerfil">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- JS LIBRERÍAS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS PERSONAL -->
<script src="<?= BASE_URL ?>view/UsuPerfil/usuDatosPerfil.js"></script>

<!-- JS para solucionar cierre correcto del modal -->
<script>
  // Mostrar/ocultar formulario de cambio de contraseña
  document.getElementById('btn-toggle-password-form').addEventListener('click', function () {
    const form = document.getElementById('form-cambiar-password');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
  });

  // ==============================
  // CONFIGURACIÓN DEL MODAL DE PERFIL
  // ==============================
  $(document).ready(function () {
    // Ocultar modal al iniciar
    $("#DatosPerfil_modal").modal("hide");

    // Mostrar modal al hacer clic en el botón "Editar"
    $("#boton-editar").click(function () {
      $("#DatosPerfil_modal").modal("show");
    });

    // Botón siguiente pestaña
    $("#btnsiguiente-DatosPerfil").click(function () {
      $("#datos-DatosPerfil-tab").removeClass("active");
      $("#datos-DatosPerfil").removeClass("show active");

      $("#info-DatosPerfil-tab").addClass("active");
      $("#info-DatosPerfil").addClass("show active");

      $("#btncancelar-DatosPerfil, #btnsiguiente-DatosPerfil").addClass("d-none");
      $("#btnatras-DatosPerfil, #btnactualizar-DatosPerfil").removeClass("d-none");
    });

    // Botón pestaña anterior
    $("#btnatras-DatosPerfil").click(function () {
      $("#info-DatosPerfil-tab").removeClass("active");
      $("#info-DatosPerfil").removeClass("show active");

      $("#datos-DatosPerfil-tab").addClass("active");
      $("#datos-DatosPerfil").addClass("show active");

      $("#btncancelar-DatosPerfil, #btnsiguiente-DatosPerfil").removeClass("d-none");
      $("#btnatras-DatosPerfil, #btnactualizar-DatosPerfil").addClass("d-none");
    });
  });

  // ==============================
  // PREVISUALIZACIÓN DE IMAGEN
  // ==============================

  let imagenBorrada = false; // Estado para saber si borró la imagen
let nuevaImagenSeleccionada = false; // Estado para saber si seleccionó una imagen nueva

function seleccionarImagen() {
  document.getElementById('InputImg').click();
}

function previsualizarImagen(event) {
  const input = event.target;
  const reader = new FileReader();

  if (input.files && input.files[0]) {
    reader.onload = function () {
      const imgElement = document.getElementById('PrevisualizadoImg');
      const btnBorrar = document.getElementById('btnBorrar');

      imgElement.src = reader.result;
      imgElement.style.display = 'block';
      btnBorrar.style.display = 'inline-block';

      imagenBorrada = false; // Ya no está borrada porque seleccionó nueva imagen
      nuevaImagenSeleccionada = true;
    };

    reader.readAsDataURL(input.files[0]);
  }
}

function borrarPreview() {
  const imgElement = document.getElementById('PrevisualizadoImg');
  const inputImg = document.getElementById('InputImg');
  const btnBorrar = document.getElementById('btnBorrar');
  const imagenOriginal = document.getElementById('imagenOriginal');

  // Limpias selección del input file
  inputImg.value = "";

  // Limpias el preview
  imgElement.src = "";
  imgElement.style.display = 'none';
  btnBorrar.style.display = 'none';

  // Indicas que imagen fue borrada
  imagenBorrada = true;
  nuevaImagenSeleccionada = false;

  // Opcional: limpia el valor de la imagen original para avisar que no hay imagen
  imagenOriginal.value = "";
}

</script>



  <style>
    /* Estilos responsivos */
    @media (max-width: 768px) {
      .modal-dialog {
        max-width: 95%;
        margin: auto;
      }

      .modal-body {
        padding: 15px;
        margin: 0px !important;
      }

      .form-layout {
        margin: 0;
        padding: 15px;
      }

      .nav-tabs .nav-link {
        font-size: 14px;
        padding: 8px;
      }
    }

    .mb-3 {
      margin-bottom: 0px !important;
    }

    /* Estilos generales */
    .modal-content {
      border-radius: 15px;
      border: 2px solid #98CDE4;
    }

    .header-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #7FCBD9;
      color: black;
      padding: 15px;
      border-radius: 15px 15px 0 0;
    }

    .close-btn {
      background: none;
      border: none;
      color: white;
      font-size: 24px;
      cursor: pointer;
    }

    .nav-tabs .nav-link {
      font-weight: bold;
      border-radius: 10px 10px 0 0;
      background: #7FCBD9;
      color: white !important;
    }


    .nav-tabs .nav-link.active {
      background-color: #13232B;
      color: #7FCBD9 !important;
    }

    .form-control,
    .form-select {
      border-radius: 50px;
      padding: 10px 15px;
      border: 1px solid #98CDE4;
    }

    .form-layout {
      border: 1px solid #000;
      border-radius: 0px 10px 10px 10px;
      padding: 20px;
    }

    .btn {
      border-radius: 50px;
      padding: 10px 20px;
      font-weight: bold;
    }

    .btn-info {
      background-color: #17A2B8;
      border-color: #17A2B8;
      color: white;
    }

    .btn-info:hover {
      background-color: #138496;
    }

    .img-profile {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      background-color: white;
      padding: 15px;
      border-radius: 0 0 15px 15px;
    }
  </style>