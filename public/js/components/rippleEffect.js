// js/components/rippleEffect.js
// Este es un módulo JS para el efecto de onda (ripple effect) de Material Design

export function initRippleEffect(selector = '.ripple-effect') {
    const elements = document.querySelectorAll(selector);

    elements.forEach(element => {
        // Evita inicializar el mismo elemento varias veces
        if (element.dataset.rippleInitialized) {
            return;
        }
        element.dataset.rippleInitialized = 'true';

        element.addEventListener('click', function(e) {
            const rippleElement = document.createElement('span');
            rippleElement.classList.add('ripple');

            // Calcula el tamaño del ripple (el mayor entre ancho y alto del elemento)
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);

            rippleElement.style.width = rippleElement.style.height = `${size}px`;

            // Posiciona el ripple en el punto del clic
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            rippleElement.style.left = `${x}px`;
            rippleElement.style.top = `${y}px`;

            this.appendChild(rippleElement);

            // Elimina el ripple después de la animación
            rippleElement.addEventListener('animationend', () => {
                rippleElement.remove();
            });
        });
    });
}