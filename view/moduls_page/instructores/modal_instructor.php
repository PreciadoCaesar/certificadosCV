<?php 
require_once("../../config/conexion.php");
if (!isset($_SESSION["ID_administrador"]) && !isset($_SESSION["ID_gerente"])) {
  header("Location:" . Conectar::ruta() . "view/404/");
  exit();
}
?>

<!-- MODAL IMAGEN -->
<div class="modal fade" id="modal_visualizar_foto" tabindex="-1" aria-labelledby="fotoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #7FCBD9; color: black; border-radius: 15px 15px 0 0;">
        <h5 class="md-headline-small" id="fotoLabel">Fotografía del Instructor</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img id="fotoModal" src="" class="img-fluid rounded" style="max-width: 100%; height: auto;">
      </div>
    </div>
  </div>
</div>

<!-- MODAL REGISTRAR Y EDITAR -->
<div class="modal fade" id="modal_instructor" tabindex="-1" aria-labelledby="instructorPopupLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="header-section">
        <h5 class="md-headline-small" id="titulo_modalinstructor">Nuevo Instructor</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" id="form_instructor" enctype="multipart/form-data">
        <input type="hidden" id="inst_id" name="inst_id">

          <ul class="nav nav-tabs mb-3" id="profileTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="datos-instructor-tab" data-bs-toggle="tab" href="#datos-instructor" role="tab">Datos Personales</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="info-instructor-tab" data-bs-toggle="tab" href="#info-instructor" role="tab">Información de Usuario</a>
            </li>
          </ul>

          <div class="tab-content" id="profileTabContent">
            <!-- DATOS PERSONALES -->
            <div class="tab-pane fade show active" id="datos-instructor" role="tabpanel">
              <div class="form-layout">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Nombres: <span class="tx-danger">*</span></label>
                    <input type="text" class="form-control md-label-medium" id="inst_nom" name="inst_nom" placeholder="Ingrese su nombre" required>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Apellido Paterno: <span class="tx-danger">*</span></label>
                    <input type="text" class="form-control md-label-medium" id="inst_apep" name="inst_apep" placeholder="Apellido Paterno" required>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Apellido Materno: <span class="tx-danger">*</span></label>
                    <input type="text" class="form-control md-label-medium" id="inst_apem" name="inst_apem" placeholder="Apellido Materno" required>
                  </div>
                  
                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Fotografía:</label>
                    <div class="input-group">
                      <img id="imagenPreview" src="" class="img-profile" style="width: 150px; height: 150px; display: none; margin-bottom: 10px; cursor: pointer;" onclick="mostrarImagen()">
                      <div>
                        <button type="button" class="button button--primary md-label-medium" onclick="seleccionarImagen()">Seleccionar Imagen</button>
                        <br><br>
                        <button type="button" class="button button--outlined md-label-medium" id="btnBorrarImagen" onclick="borrarPreview()" style="display: none;">Borrar Imagen</button>
                      </div>
                      <input type="file" id="inputFoto" name="foto" accept=".jpg, .jpeg, .png" style="display: none;" onchange="previsualizarImagen(event)">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- INFORMACIÓN DE USUARIO -->
            <div class="tab-pane fade" id="info-instructor" role="tabpanel">
              <div class="form-layout">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Teléfono: <span class="tx-danger">*</span></label>
                    <input type="number" class="form-control md-label-medium" id="inst_telf" name="inst_telf" placeholder="Ingrese su teléfono" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="md-label-large">Correo Electrónico: <span class="tx-danger">*</span></label>
                    <input type="email" class="form-control md-label-medium" id="inst_correo" name="inst_correo" placeholder="Ingrese su correo" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- BOTONES -->
          <div class="modal-footer">
            <button type="reset" class="button button--outlined" data-bs-dismiss="modal" id="btncancelar-instructor">Cancelar</button>
            <button type="button" class="button button--primary" id="btnsiguiente-instructor">Siguiente</button>
            <button type="button" class="button button--o d-none" id="btnatras-instructor">Atrás</button>
            <button type="submit" class="button button--primary d-none" id="btnactualizar-instructor">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>view/AdminMntInstructor/adminmntinstructor.js"></script>
  <script>

    
   $(document).ready(function () {
    // Ocultar modal al cargar
    $("#modal_instructor").modal("hide");

    // Mostrar modal al hacer clic en botón registrar
    $("#btnRegistrarInstructor").click(function () {
        $("#modal_instructor").modal("show");
    });

    // Botón Siguiente: Ir a pestaña "Información del Instructor"
    $("#btnsiguiente-instructor").click(function () {
        $('#info-instructor-tab').tab('show');
    });

    // Botón Atrás: Volver a pestaña "Datos del Instructor"
    $("#btnatras-instructor").click(function () {
        $('#datos-instructor-tab').tab('show');
    });

    // Sincronizar botones según la pestaña activa
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");

        if (target === "#datos-instructor") {
            $("#btncancelar-instructor, #btnsiguiente-instructor").removeClass("d-none");
            $("#btnatras-instructor, #btnactualizar-instructor").addClass("d-none");
        } else if (target === "#info-instructor") {
            $("#btncancelar-instructor, #btnsiguiente-instructor").addClass("d-none");
            $("#btnatras-instructor, #btnactualizar-instructor").removeClass("d-none");
        }
    });
});

    // Previsualizar imagen en el formulario
    function seleccionarImagen() {
      document.getElementById('inputFoto').click();
    }

    function previsualizarImagen(event) {
      var input = event.target;
      var reader = new FileReader();

      reader.onload = function() {
        var imgElement = document.getElementById('imagenPreview');
        var btnBorrar = document.getElementById('btnBorrarImagen');

        imgElement.src = reader.result;
        imgElement.style.display = 'block';
        btnBorrar.style.display = 'inline-block';
      };

      if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Boton borrar imagen de preview

    function borrarPreview() {
      var imgElement = document.getElementById('imagenPreview');
      var inputFoto = document.getElementById('inputFoto');
      var btnBorrar = document.getElementById('btnBorrarImagen');

      inputFoto.value = "";
      inputFoto.type = "";
      inputFoto.type = "file";

      imgElement.src = "";
      imgElement.style.display = 'none';
      btnBorrar.style.display = 'none';
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

    .form-control md-label-medium,
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
      background-color: #EAF4FF;
      padding: 15px;
      border-radius: 0 0 15px 15px;
    }

    .modal-body {
      margin: 40px;
    }
  </style>