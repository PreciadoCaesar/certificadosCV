
<?php
// Asegúrate de que BASE_PATH y BASE_URL estén definidos correctamente en tu configuración.
// Por ejemplo:
// define('BASE_PATH', __DIR__);
require_once("../../config/conexion.php");

if (isset($_SESSION['ID_administrador'])) {
    // Esto parece ser la inclusión de un modal o sección de perfil.
    // Si usuDatosPerfil.php incluye HTML, debería estar fuera de las etiquetas script.
    require_once BASE_PATH . '/view/UsuPerfil/usuDatosPerfil.php';
}
?>

<script src="public/lib/datatables-responsive/dataTables.responsive.js"></script>

<?php
$fotoSesion = $_SESSION['foto'] ?? '';
$nombreFoto = basename($fotoSesion);

$rutaRelativa = 'public/img/img_AdGe/';
$rutaWeb = BASE_URL . $rutaRelativa;
$rutaCompleta = BASE_PATH . '/' . $rutaRelativa . $nombreFoto;

$mostrarFoto = (!empty($nombreFoto) && file_exists($rutaCompleta))
    ? $rutaWeb . $nombreFoto
    : $rutaWeb . 'default.png';

// --- NUEVA LÓGICA PARA DETECTAR LA PÁGINA ACTUAL ---

// Obtiene la ruta de la URL actual (ej: /AdminMntUsuario/ o /AdminMntInicio/)
$currentFullPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Elimina la BASE_URL de la ruta completa para obtener una ruta relativa más limpia
// Ejemplo: si BASE_URL es http://localhost/mi_proyecto/ y currentFullPath es /mi_proyecto/AdminMntUsuario/
// entonces $currentRelativePath será AdminMntUsuario/
$currentRelativePath = str_replace(BASE_URL, '', $currentFullPath);



function isActiveMenuItem($menuUrl, $currentRelativePath) {
    $menuPath = parse_url($menuUrl, PHP_URL_PATH); // convierte a ruta sin dominio
    $cleanMenuUrl = trim($menuPath, '/');
    $cleanCurrentPath = trim($currentRelativePath, '/');

    return strpos($cleanCurrentPath, $cleanMenuUrl) === 0;
}


?>


<aside class="main-sidebar" id="main-sidebar">
    <div class="sidebar__profile-section">
        <img src="<?= $mostrarFoto ?>" alt="Avatar de usuario" class="sidebar__profile-avatar">
        <div class="sidebar__profile-info">
            <span class="sidebar__profile-name"><?= $_SESSION["nom_admin"] ?></span>
            <span class="sidebar__profile-email"><?= $_SESSION["correo"] ?></span>
        </div>

        <?php if (!isset($_SESSION["ID_gerente"])): ?>
            <div class="perfil-dropdown">
                <button class="icon-button" aria-label="Opciones de perfil" id="toggle-perfil-opciones">
                    <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                </button>

                <div class="text-center my-2 perfil-opciones">
                    <button class="boton-menu" id="boton-editar"
                        data-id="<?= $_SESSION["ID_administrador"] ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#DatosPerfil_modal">
                        <i class="fa-solid fa-pen-to-square" style="font-size:15px;"></i>
                        Editar Perfil
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <nav class="sidebar__navigation">
    <div class="sidebar__menu-category">MENU</div>
    <?php
    // Definir los elementos del menú en un array
    $menuItems = [
        ['url' => BASE_URL . 'view/AdminMntInicio/', 'icon' => 'home', 'text' => 'Inicio'],
        ['url' => BASE_URL . 'view/AdminMntUsuario/', 'icon' => 'group', 'text' => 'Usuarios'],
        ['url' => BASE_URL . 'view/AdminMntCategoria/', 'icon' => 'category', 'text' => 'Categorías'],
        ['url' => BASE_URL . 'view/AdminMntInstructor/', 'icon' => 'face', 'text' => 'Instructores'],
        ['url' => BASE_URL . 'view/AdminMntCurso/', 'icon' => 'menu_book', 'text' => 'Cursos'],
        ['url' => BASE_URL . 'view/AdminDetalleCertificado/', 'icon' => 'school', 'text' => 'Certificado'],
        ['url' => BASE_URL . 'view/AdminHistorialAcciones/', 'icon' => 'history', 'text' => 'Historial de acciones'],
    ];

    // Si existe la sesión de gerente, agregamos la opción de Administradores
    if (isset($_SESSION['ID_gerente'])) {
        $menuItems[] = [
            'url' => BASE_URL . 'view/AdminMntAdmin/',
            'icon' => 'person',
            'text' => 'Administradores'
        ];
    }

    // Imprimir los elementos del menú
    foreach ($menuItems as $item) {
        $isActive = isActiveMenuItem($item['url'], $currentRelativePath) ? ' sidebar__menu-item--active' : '';
        echo '<a href="' . $item['url'] . '" class="sidebar__menu-item' . $isActive . '">';
        echo '    <span class="material-symbols-outlined">' . $item['icon'] . '</span>';
        echo '    <span class="sidebar__menu-item-text">' . $item['text'] . '</span>';
        echo '</a>';
    }
    ?>
</nav>


<div class="sidebar__bottom-link-group">
    <!-- Título general -->
    <div class="sidebar__menu-category">Centro de Ayuda
    </div>

    <!-- Enlace a WhatsApp -->
    <a href="https://wa.me/51901726884?text=Necesito%20ayuda%20de%20soporte%20en%20el%20sistema%20de%20certificación%20de%20Grupo%20Consigueventas" 
       class="sidebar__menu-item " 
       target="_blank">
        <span class="material-symbols-outlined">chat</span>
        <span class="sidebar__menu-item-text">WhatsApp Soporte</span>
    </a>

    <!-- Enlace a Correo -->
    <a href="mailto:juancarlosminayarodriguez72@gmail.com?subject=Soporte%20Sistema%20de%20Certificación&body=Necesito%20ayuda%20de%20soporte%20en%20el%20sistema%20de%20certificación%20de%20Grupo%20Consigueventas" 
       class="sidebar__menu-item">
        <span class="material-symbols-outlined">mail</span>
        <span class="sidebar__menu-item-text">Correo Soporte</span>
    </a>
</div>



</aside>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("toggle-perfil-opciones");
        const opciones = document.querySelector(".perfil-opciones");

        if (toggleBtn && opciones) {
            toggleBtn.addEventListener("click", function () {
                opciones.classList.toggle("is-active");
                toggleBtn.classList.toggle("rotated");
            });

            document.addEventListener("click", function(event) {
                if (!toggleBtn.contains(event.target) && !opciones.contains(event.target)) {
                    if (opciones.classList.contains("is-active")) {
                        opciones.classList.remove("is-active");
                        toggleBtn.classList.remove("rotated");
                    }
                }
            });
        }
    });
</script>

