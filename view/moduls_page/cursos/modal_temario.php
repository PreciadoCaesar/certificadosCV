<!-- Modal Subir Documento -->
<div class="modal fade" id="modalSubirDocumento" tabindex="-1" aria-labelledby="modalDocumentoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-info text-white" style="background-color: #7FCBD9 !important; border-radius: 10px 10px 10px 10px;">
        <h5 class="md-headline-small">Subir Documento</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="background: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body text-center">
        <form id="formSubirDocumento" enctype="multipart/form-data">

     <input type="hidden" name="id_curso" id="id_curso_doc">
     <input type="hidden" name="nombre_curso" id="nombre_curso_doc">


          <!-- Texto informativo -->
          <p class="md-label-large">
            El temario sólo puede ser un archivo <strong>PDF</strong>.
          </p>

          <!-- Área Drag & Drop -->
          <div id="dropZoneDoc" class="form-control md-label-medium" style="cursor: pointer; border: 2px dashed #ccc; border-radius: 10px; padding: 30px; text-align: center; color: #666;">
            <span class="material-symbols-outlined" style="font-size: 40px;">
            place_item
            </span><br>
            Arrastra y suelta un archivo aquí o haz clic para seleccionar
          </div>

          <input type="file" id="inputDocumento" name="documento" accept=".pdf" style="display: none;">

          <div id="previewDocumento" class="mt-3" style="display: none;">
            <div id="fileInfoContainer"></div>
            <button type="button" class="button button--outlined" onclick="cambiarDocumento()">Cambiar archivo</button>
          </div>

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="button button--outlined" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="button button--primary" onclick="guardarDocumento()">Guardar Documento</button>
      </div>
    </div>
  </div>
</div>


<style>
#dropZoneDoc {
  border: 2px dashed #ccc;
  padding: 30px;
  text-align: center;
  color: #666;
  background-color: #fafafa;
  cursor: pointer;
  transition: all 0.3s ease;
  border-radius: 8px;
}

#dropZoneDoc.dragover {
  border-color: #0d6efd; /* azul */
  background-color: #e9f5ff;
  color: #0d6efd;
}

#dropZoneDoc.dropped {
  border-color: #198754; /* verde */
  background-color: #e6fff2;
  color: #198754;
}

.icon-preview {
  font-size: 1.5rem;
}

#previewDocumento {
  margin-top: 1rem;
}

</style>


<script>
 // Referencias globales
const inputDocumento = document.getElementById('inputDocumento');
const dropZoneDoc = document.getElementById('dropZoneDoc');
const fileInfoContainer = document.getElementById('fileInfoContainer');
const previewDocumento = document.getElementById('previewDocumento');
const formSubirDocumento = document.getElementById('formSubirDocumento');
const modalElemento = document.getElementById('modalSubirDocumento');
let modalInstance = null;

// Función para abrir modal y resetear campos
function abrirModalDocumento(idCurso, nombreCurso) {
  formSubirDocumento.reset();
  document.getElementById('id_curso_doc').value = idCurso;
  document.getElementById('nombre_curso_doc').value = nombreCurso;

  fileInfoContainer.innerHTML = '';
  previewDocumento.style.display = 'none';
  dropZoneDoc.style.display = 'block';

  if (!modalInstance) {
    modalInstance = new bootstrap.Modal(modalElemento);
  }
  modalInstance.show();

  // Verificar si ya hay documento
  $.ajax({
    url: BASE_URL + 'controller/curso.php?op=verificar_documento',
    type: 'POST',
    data: { id_curso: idCurso },
    dataType: 'json',
    success: function (response) {
      if (response.status === 'success') {
        const extension = response.archivo.split('.').pop().toLowerCase();
        let iconHtml = extension === 'pdf'
          ? `<span class="material-symbols-outlined">picture_as_pdf</span>` : '';

        fileInfoContainer.innerHTML = `
          <div class="d-flex align-items-center justify-content-center gap-2">
            ${iconHtml}
            <a href="public/temarios/${response.archivo}" target="_blank"><strong>${response.archivo}</strong></a>
          </div>
        `;
        previewDocumento.style.display = 'block';
        dropZoneDoc.style.display = 'none';
      } else {
        fileInfoContainer.innerHTML = `
          <div class="text-center text-muted">
            <i class="fa-solid fa-circle-info me-2"></i>No hay temario cargado para este curso.
          </div>
        `;
        previewDocumento.style.display = 'none';
        dropZoneDoc.style.display = 'block';
      }
    },
    error: function () {
      fileInfoContainer.innerHTML = `
        <div class="text-center text-danger">
          <i class="fa-solid fa-triangle-exclamation me-2"></i>Error al verificar documento.
        </div>
      `;
      previewDocumento.style.display = 'none';
      dropZoneDoc.style.display = 'block';
    }
  });
}

// Validación de tipo de archivo PDF al seleccionar
inputDocumento.addEventListener('change', function () {
  const archivo = this.files[0];
  if (archivo) {
    const extension = archivo.name.split('.').pop().toLowerCase();
    if (extension !== 'pdf') {
      Swal.fire('Error', 'Solo se permiten archivos PDF.', 'error');
      this.value = '';
    }
  }
});

// Vista previa del archivo
function mostrarPreviewArchivo(file) {
  const extension = file.name.split('.').pop().toLowerCase();
  const extensionesPermitidas = ['pdf'];

  if (!extensionesPermitidas.includes(extension)) {
    Swal.fire({
      icon: 'error',
      title: 'Archivo no permitido',
      text: 'Solo se permiten archivos PDF.',
      confirmButtonText: 'OK'
    });
    cambiarDocumento();
    return;
  }

  let iconHtml = extension === 'pdf'
    ? `<i class="fa-solid fa-file-pdf text-danger icon-preview"></i>` : '';

  fileInfoContainer.innerHTML = `
    <div class="d-flex align-items-center justify-content-center gap-2">
      ${iconHtml}
      <strong>${file.name}</strong>
    </div>
  `;
  previewDocumento.style.display = 'block';
  dropZoneDoc.style.display = 'none';
}

// Limpia selección y UI
function cambiarDocumento() {
  inputDocumento.value = '';
  fileInfoContainer.innerHTML = '';
  previewDocumento.style.display = 'none';
  dropZoneDoc.style.display = 'block';
}

// Eventos de dropzone
dropZoneDoc.addEventListener('dragover', e => {
  e.preventDefault();
  dropZoneDoc.classList.add('dragover');
});

dropZoneDoc.addEventListener('dragleave', e => {
  e.preventDefault();
  dropZoneDoc.classList.remove('dragover');
});

dropZoneDoc.addEventListener('drop', e => {
  e.preventDefault();
  dropZoneDoc.classList.remove('dragover');
  dropZoneDoc.classList.add('dropped');

  const files = e.dataTransfer.files;
  if (files.length > 0) {
    inputDocumento.files = files;
    mostrarPreviewArchivo(files[0]);
  }

  setTimeout(() => {
    dropZoneDoc.classList.remove('dropped');
  }, 1500);
});

dropZoneDoc.addEventListener('click', () => {
  inputDocumento.click();
});

inputDocumento.addEventListener('change', () => {
  if (inputDocumento.files.length > 0) {
    mostrarPreviewArchivo(inputDocumento.files[0]);
  }
});

</script>

