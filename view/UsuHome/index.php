<?php
define('BASE_URL', 'https://certificados.consigueventas.com/');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inicio</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="UsuStyle.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/atomic_design/pages/modal_template.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/atomic_design/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/atomic_design/pages/modal_template.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/atomic_design/components/_input.css">
        <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- CDN Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    
</head>

<body>
    <!-- Barra superior -->
    <div class="top-bar">
        <div class="logo">
            <img src="cosigueventas.png">
        </div>
    </div>
      <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>

    <header class="hero">
        <h1>Bienvenido al Portal</h1>
        <h2>Descarga tu Certificado</h2>
        <div class="search-box">
            <select id="documentType" class="select-input -input-wrapper-mobile">
                <option value="">Elegir tipo de Documento</option>
                <option value="DNI">DNI</option>
            </select>
            <div class="text-input-wrapper input-wrapper-mobile">
            <input type="text" id="documentNumber" class="text-input"  placeholder="Ingresa tu número de Documento">
            </div>

            <button class="button button--primary button--icon-leading" id="btnconsultar">
                <span class="material-symbols-outlined" aria-hidden="true">search</span>
                Buscar Certificado
            </button>
        </div>
    </header>
    <main id="divpasos">
        <section class="steps">
            <div class="step">
                <h3>1. Ingresa tu documento</h3>
                <img src="document-icon.png">
                <p>Selecciona el tipo de documento, ingresa el número en el campo correspondiente y presiona <br>
                    "Buscar" para iniciar la consulta.</p>
            </div>
            <div class="step">
                <h3>2. Visualiza tus certificados</h3>
                <img src="view-icon.png">
                <p>Se mostrará una tabla con la lista <br>
                    de tus certificados, incluyendo <br>
                    nombre del curso, fecha de <br>
                    emisión y su estado.</p>
            </div>
            <div class="step">
                <h3>3. Descarga tu certificado</h3>
                <img src="download-icon.png">
                <p>Haz clic en el botón de descarga junto al certificado que necesitas y obtendrás el archivo en formato PDF.</p>
            </div>
        </section>
    </main>
    <section class="certificates">
        <div id="perfilcard" class="profile-card">
            <img  id="img_perfil" src="" alt="Foto de perfil">
            <div class="profile-info">
                <div class="info-item">
                    <span class="label">Tipo de Documento:</span>
                    <span id="documento"></span>
                </div>
                <div class="info-item">
                    <span class="label">Número de Documento:</span>
                    <span id="numero"></span>
                </div>
                <div class="info-item">
                    <span class="label">Nombres:</span>
                    <span id="nombre"></span>
                </div>
                <div class="info-item">
                    <span class="label">Apellidos:</span>
                    <span id="apellidos"></span>
                </div>
            </div>
        </div>
    </section>
    <div id="divpanel" class="table_container">
    <table id="cursos_data" style="width: 100%;">
        <thead>
            <tr>
                <th>Código <i class="fa fa-sort"></i></th>
                <th>Certificado <i class="fa fa-sort"></i></th>
                <th>Fecha Emisión <i class="fa fa-sort"></i></th>
                <th>Fecha Vencimiento <i class="fa fa-sort"></i></th>
                <th>Nacionalidad <i class="fa fa-sort"></i></th>
                <th>Opciones de Descarga</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= BASE_URL ?>view/UsuHome/usuhome.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

</body>

</html>