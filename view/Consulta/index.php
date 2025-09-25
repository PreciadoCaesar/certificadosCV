<!DOCTYPE html>
<html lang="es">
<head>
    <title>Empresa::Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Consul.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- Barra superior -->
    <div class="top-bar">
        <div class="logo">
            <img src="cosigueventas.png">
        </div>
        <div class="back-button">
            <a href="index.php" class="btn">Volver al Principal</a>
        </div>
    </div>

    <header class="hero">
        <h1>Bienvenido al Portal</h1>
        <h2>Descarga tu Certificado</h2>
        <div class="search-box">
            <select id="documentType">
                <option value="">Elegir tipo de Documento</option>
                <option value="DNI">DNI</option>
                <option value="Pasaporte">Pasaporte</option>
            </select>
            <input type="text" id="documentNumber" placeholder="Ingresa tu número de Documento">
            <button id="searchButton">
                <i class="fa fa-search"></i> Buscar Certificado
            </button>
        </div>
    </header>
    <!-- Sección de Certificados -->
    <section class="certificates">
    <div class="profile-card">
    <img src="perfil.png" alt="Foto de perfil">
    <div class="profile-info">
        <div class="info-item">
            <span class="label">Tipo de Documento:</span>
            <span>DNI</span>
        </div>
        <div class="info-item">
            <span class="label">Número de Documento:</span>
            <span>78965454</span>
        </div>
        <div class="info-item">
            <span class="label">Nombres:</span>
            <span>Juan Hidalgo</span>
        </div>
        <div class="info-item">
            <span class="label">Apellidos:</span>
            <span>Perez Lopez</span>
        </div>
    </div>
</div>
        <table>
            <thead>
                <tr>
                <th>Código <i class="fa fa-sort"></i></th>
                <th>Certificado <i class="fa fa-sort"></i></th>
                <th>Fecha Emisión <i class="fa fa-sort"></i></th>
                <th>Fecha Vencimiento <i class="fa fa-sort"></i></th>
                <th>Nacionalidad <i class="fa fa-sort"></i></th>
                <th colspan="2">Opciones de Descarga</th>
                </tr>
            </thead>
            <!-- Sección de Datos luego borrar -->
            <tbody>
                <tr>
                    <td>5026</td>
                    <td>Introducción a HTML5</td>
                    <td>06 de Enero del 2024</td>
                    <td>06 de Enero del 2025</td>
                    <td>Peruana</td>
                    <td>
                <a href="#"><img src="certificado.png"  width="110"> </a>
              </td>
              <td>
                <a href="#"><img src="Diapositiva.png"  width="110"> </a>
            </td>
            </tr>
                <tr>
                    <td>5066</td>
                    <td>Introducción a CSS</td>
                    <td>08 de Enero del 2024</td>
                    <td>08 de Enero del 2025</td>
                    <td>Peruana</td>
                    <td>
                <a href="#"><img src="certificado.png" width="110"> </a>
            </td>
            <td>
                <a href="#"><img src="Diapositiva.png" width="110"> </a>
            </td>
           </tr>
            </tbody>
            <!-- Fin de Sección de Datos luego borrar -->

        </table>
    </section>
  </body>
</html>
