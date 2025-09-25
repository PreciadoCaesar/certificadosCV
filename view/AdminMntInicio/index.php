<?php 
require_once("../../config/conexion.php");
if (!isset($_SESSION["ID_administrador"]) && !isset($_SESSION["ID_gerente"])) {
  header("Location:" . Conectar::ruta() . "view/404/");
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <head>
    <?php require_once BASE_PATH . '/view/html/MainHead.php'; ?>
    <title>Inicio:Panel de Consigueventas</title>
    <?php require_once BASE_PATH . '/view/html/MainCss.php'; ?>
        <!-- Enlace a Material Symbols Outlined para iconos de Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Enlace a Font Awesome para iconos adicionales (tarjetas, exportar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Conectar::ruta(); ?>/view/moduls_page/inicio/style.css">
</head>
<body>
  <div class="app-layout">
    <?php require_once BASE_PATH . '/view/components/global_header.php'; ?>
    <?php require_once BASE_PATH . '/view/components/global_sidebar.php'; ?>
    <?php require_once BASE_PATH . '/view/moduls_page/inicio/main.php'; ?>
  </div>
    <!-- Opcional: Modal de perfil -->
    <?php require_once BASE_PATH . '/view/UsuPerfil/usuDatosPerfil.php'; ?>

    <!-- Scripts principales -->
    <?php require_once BASE_PATH . '/view/html/MainJs.php'; ?>

    <!-- Script específico para la página de Inicio -->
    <script src="<?= BASE_URL ?>view/AdminMntInicio/adminInicio.js"></script>
       <script src="<?= BASE_URL ?>/view/moduls_page/instructores/instructores.js"></script>
  
  <script>
      document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.getElementById("menu-toggle-button");
    const sidebar = document.getElementById("main-sidebar");
    const body = document.body;
    
    // Verificar si ya existe un estado guardado en localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    // Aplicar estado inicial
    if (isCollapsed) {
        sidebar.classList.add('main-sidebar--collapsed');
        body.classList.add('sidebar-collapsed');
    }
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function() {
            // Alternar estado colapsado
            sidebar.classList.toggle('main-sidebar--collapsed');
            body.classList.toggle('sidebar-collapsed');

            // Guardar preferencia en localStorage
            localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapsed'));
        });
    }
});
    </script>
  </body>
</html>
