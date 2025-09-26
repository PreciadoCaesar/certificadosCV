<?php
// view/html/GlobalHeader.php

// Puedes agregar aquí lógica PHP si la necesitas para cosas como el nombre de usuario
// o para mostrar/ocultar elementos basado en la sesión, aunque esto ya lo hace el JS.
?>

<header class="main-header">
    <div class="header__left-section">
        <button class="icon-button header__menu-toggle-button material-symbols-outlined" id="menu-toggle-button"
            aria-label="Toggle navigation menu">
            menu
        </button>
        <span class="header__logo">ConsigueVentas</span>
    </div>
    <div class="header__right-section">
        <input type="hidden" id="admin_idx" value="<?= (isset($_SESSION['ID_administrador']) && !isset($_SESSION['ID_gerente'])) 
                           ? 'admin' 
                           : (isset($_SESSION['ID_gerente']) ? 'gerente' : '') ?>">
        <!-- Botón de notificaciones -->
        <button class="icon-button header__notification-button" aria-label="Notificaciones" onclick="toggleNotificaciones()">
            <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
        </button>

        <!-- Popup de notificaciones -->
        <div id="popupNotificaciones" class="notificaciones-popup oculto">
        <h4 class="md-label-medium">Notificaciones</h4>
        <ul id="listaNotificaciones">
            <!-- Las notificaciones se cargarán aquí desde JS -->
        </ul>
        <div class="ver-mas-container">
            <a href="<?= BASE_URL ?>view/AdminHistorialAcciones" class="ver-mas">
            <span class="material-symbols-outlined">arrow_drop_down</span>
            Ver más
        </a>
        </div>  
    </div>



        <!-- Botón de cerrar sesión -->
       <a href="<?= BASE_URL ?>logout/" id="logout-button" class="button button--filled button--logout-button button--active"
            aria-label="Cerrar sesión de usuario" tabindex="0">
            <span class="material-symbols-outlined" aria-hidden="true">logout</span>
            Cerrar Sesión
        </a>
        <a href="<?= BASE_URL ?>logout/" id="logout-button" class="button button--filled button--logout-button button--none"
            aria-label="Cerrar sesión de usuario" tabindex="0">
            <span class="material-symbols-outlined" aria-hidden="true">logout</span>
        </a>
        <!-- Versión móvil opcional 
                <button type="button" class="icon-button mobile-only" aria-label="Cerrar sesión">
                    <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                </button>
                -->
    </div>
</header>
<script>
function toggleNotificaciones() {
    const popup = document.getElementById("popupNotificaciones");
    const ul = document.getElementById("listaNotificaciones");

    if (!popup || !ul) {
        console.error("No se encontró el contenedor de notificaciones.");
        return;
    }

    popup.classList.toggle("mostrar");
    popup.classList.toggle("oculto");

    if (popup.classList.contains("mostrar")) {
        fetch("<?= BASE_URL ?>controller/usuario.php?op=notificaciones", {
            method: "POST"
        })
        .then(res => res.json())
        .then(data => {
            ul.innerHTML = "";

            if (data.length === 0) {
                ul.innerHTML = "<li>No hay notificaciones.</li>";
                return;
            }

            data.forEach(log => {
                const admin = log.nom_admin ?? "Gerente";
                const ID_registro = log.registro;
                const tabla = log.tabla_afectada;
                const accion = log.accion.toUpperCase();

                // Traducir la acción
                let accionTraducida = "";
                switch (accion) {
                    case "INSERT": accionTraducida = "registró"; break;
                    case "UPDATE": accionTraducida = "actualizó"; break;
                    case "DELETE": accionTraducida = "eliminó"; break;
                    default: accionTraducida = accion.toLowerCase(); break;
                }

                // Usar new_data para INSERT/UPDATE, old_data para DELETE
                let dataFuente = accion === "DELETE" ? log.old_data : log.new_data;

                // Buscar "Nombre", si no existe buscar "Usuario"
                let nombreEntidad = extraerValorDeCampo(dataFuente, "Nombre");
                if (!nombreEntidad || nombreEntidad === "desconocido") {
                    nombreEntidad = extraerValorDeCampo(dataFuente, "Usuario");
                }

                // Determinar tipo de entidad
                let entidadTexto = `el registro ${ID_registro}`;
                switch (tabla.toLowerCase()) {
                    case "administrador":
                        entidadTexto = `al administrador ${nombreEntidad}`;
                        break;
                    case "usuario":
                        entidadTexto = `al usuario ${nombreEntidad}`;
                        break;
                    case "categoria":
                        entidadTexto = `la categoría ${nombreEntidad}`;
                        break;
                    case "certificado":
                        entidadTexto = `el certificado de ${nombreEntidad}`;
                        break;
                    case "instructor":
                        entidadTexto = `al instructor ${nombreEntidad}`;
                        break;
                    case "curso":
                        entidadTexto = `al curso ${nombreEntidad}`;
                        break;
                    default:
                        entidadTexto = `el registro ${ID_registro}`;
                        break;
                }

                const mensaje = `${admin} ${accionTraducida} ${entidadTexto}.`;

                ul.innerHTML += `
                    <li class="md-label-large">
                        ${mensaje}<br>
                        <small style="color:var(--md-sys-color-primary);">${log.fecha}</small>
                    </li>`;
            });
        })
        .catch(error => {
            console.error("Error al cargar notificaciones.", error);
            ul.innerHTML = "<li>Error al cargar notificaciones.</li>";
        });
    }
}

// 🔍 Extrae el valor de un campo desde un string tipo: "Nombre:React, Estado:Inactivo"
function extraerValorDeCampo(dataStr, campo) {
    if (!dataStr) return "desconocido";
    const regex = new RegExp(`${campo}\\s*:\\s*([^,|]+)`, "i");
    const match = dataStr.match(regex);
    return match ? match[1].trim() : "desconocido";
}

</script>





<style>
 .notificaciones-popup {
    position: absolute; /* o fixed según tu diseño */
    top: 60px; /* ajusta según posición del botón */
    right: 190px;
    width: 300px;
    max-height: 400px; /* limita la altura */
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 15px;
    z-index: 1050;
}

.notificaciones-popup h4 {
    margin-top: 0;
    font-size: 16px;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
}

.notificaciones-popup ul {
    max-height: 320px; /* espacio restante para scroll */
    overflow-y: auto;
    margin: 0;
    padding: 0;
    list-style: none;
}

.notificaciones-popup ul li {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
}

.notificaciones-popup ul li:last-child {
    border-bottom: none;
}


/* Clase para mostrar/ocultar */
.oculto {
    display: none;
}

.mostrar {
    display: block;
}
@media (max-width: 768px) {
    .notificaciones-popup {
        right: 10px;
        left: 10px;
        width: auto;
        top: 65px;
        max-height: 80vh;
        padding: 16px;
    }

    .notificaciones-popup h4 {
        font-size: 15px;
    }.notificaciones-popup ul{
    max-height: 320px;
    overflow-y: auto;
    overflow-x: auto; 
    white-space: nowrap; 
    }

    .notificaciones-popup ul li {
        font-size: 13px;
        padding: 6px 0;
        
    }
}

</style>

