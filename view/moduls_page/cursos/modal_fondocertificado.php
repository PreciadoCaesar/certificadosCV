<!-- Modal Subir Certificado -->
<div class="modal fade" id="modalSubirCertificado" tabindex="-1" aria-labelledby="modalCertificadoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header bg-info text-white" style="background-color: #7FCBD9 !important; border-radius: 10px 10px 10px 10px;">
        <h5 class="md-headline-small">Subir Certificado</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body text-center">
        <form id="formSubirCertificado" enctype="multipart/form-data">
          <input type="hidden" name="id_curso" id="id_curso">

          <!-- Área Drag & Drop -->
          <div id="dropZone" class="drop-zone mb-3">
            <div class="icon"><i class="fa-solid fa-cloud-arrow-up fa-2x"></i></div>
            <p>Arrastra y suelta la imagen aquí<br>o</p>
            <button type="button" class="button button--primary" onclick="document.getElementById('inputCertificado').click()">Buscar Imagen</button>
            <input type="file" name="certificado" id="inputCertificado" class="d-none" accept=".jpg,.jpeg,.png" onchange="handleFiles(this.files)">
          </div>

          <!-- Vista previa -->
          <div id="previewCertificado" style="display: none;">
            <p>Vista previa:</p>
            <div id="fileContainer" class="mb-2"></div>
            <button type="button" class="button button--outlined" onclick="cambiarImagen()">
              <span class="material-symbols-outlined">
              reset_image
              </span> Cambiar imagen
            </button>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="button button--outlined" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="button button--primary" onclick="guardarCertificado()">Guardar Certificado</button>

      </div>
    </div>
  </div>
</div>

<!-- Estilos del DropZone -->
<style>
.drop-zone {
  border: 2px dashed #ccc;
  border-radius: 10px;
  padding: 30px;
  text-align: center;
  color: #666;
  cursor: pointer;
  transition: border-color 0.3s ease;
}
.drop-zone.dragover {
  border-color: #17a2b8;
  background-color: #f8f9fa;
}
.drop-zone .icon {
  color: #17a2b8;
  margin-bottom: 10px;
}
</style>
<script>
const dropZone = document.getElementById('dropZone');
const inputFile = document.getElementById('inputCertificado');
const preview = document.getElementById('previewCertificado');
const container = document.getElementById('fileContainer');

// Abrir modal y mostrar imagen existente si hay
function abrirModalCertificado(idCurso) {
  document.getElementById('formSubirCertificado').reset();
  container.innerHTML = '';
  preview.style.display = 'none';
  dropZone.style.display = 'block';
  document.getElementById('id_curso').value = idCurso;

  // Buscar imagen ya guardada
  const extensiones = ['jpg', 'jpeg', 'png'];
  let encontrado = false;

  extensiones.forEach(function(ext) {
    if (encontrado) return;
    const imgPath = `<?= BASE_URL ?>public/img/certificado/${idCurso}_curso_certificado.${ext}?v=${new Date().getTime()}`;
    const img = new Image();
    img.src = imgPath;

    img.onload = function () {
      container.innerHTML = `<img src="${imgPath}" alt="Certificado" class="img-fluid" style="max-height: 300px;">`;
      dropZone.style.display = 'none';
      preview.style.display = 'block';
      encontrado = true;
    };

    img.onerror = function () {
      // No se encontró imagen con esta extensión
    };
  });

  $('#modalSubirCertificado').modal('show');
}

// Drag & Drop
dropZone.addEventListener('dragover', function(e) {
  e.preventDefault();
  dropZone.classList.add('dragover');
});
dropZone.addEventListener('dragleave', function() {
  dropZone.classList.remove('dragover');
});
dropZone.addEventListener('drop', function(e) {
  e.preventDefault();
  dropZone.classList.remove('dragover');
  const files = e.dataTransfer.files;
  if (files.length > 0) {
    inputFile.files = files;
    handleFiles(files);
  }
});

// Vista previa de imagen nueva
function handleFiles(files) {
  const file = files[0];
  if (!file) return;

  let fileType = file.type;

  if (fileType.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = function(e) {
      container.innerHTML = `<img src="${e.target.result}" alt="Imagen" class="img-fluid" style="max-height: 300px;">`;
    };
    reader.readAsDataURL(file);

    dropZone.style.display = 'none';
    preview.style.display = 'block';
  } else {
    Swal.fire('Archivo no válido', 'Solo se permiten imágenes (.jpg, .jpeg, .png)', 'warning');
    inputFile.value = '';
  }
}

// Cambiar imagen
function cambiarImagen() {
  inputFile.value = '';
  container.innerHTML = '';
  preview.style.display = 'none';
  dropZone.style.display = 'block';
}
</script>

