<?php
require_once("../../config/conexion.php");

if (!isset($_SESSION["ID_administrador"]) && !isset($_SESSION["ID_gerente"])) {
  header("Location: " . Conectar::ruta() . "view/404/");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once BASE_PATH . '/view/html/MainHead.php'; ?>
    <title>Gestión de Cursos</title>
    <?php require_once BASE_PATH . '/view/html/MainCss.php'; ?>
</head>

<body>
    <div class="app-layout">
        <?php require_once BASE_PATH . '/view/components/global_header.php'; ?>
        <?php require_once BASE_PATH . '/view/components/global_sidebar.php'; ?>
        <?php require_once BASE_PATH . '/view/moduls_page/cursos/main.php'; ?>
    </div>

    <?php
      require_once BASE_PATH . '/view/moduls_page/cursos/modal_curso.php';
      require_once BASE_PATH . '/view/moduls_page/cursos/modal_fondocertificado.php';
      require_once BASE_PATH . '/view/moduls_page/cursos/modal_temario.php';
      require_once BASE_PATH . '/view/html/MainJs.php';
    ?>

    <script src="<?= BASE_URL ?>/view/moduls_page/cursos/cursos.js"></script>
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