const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');

const image = new Image();
const qrTemp = document.createElement('canvas');
const qrCtx = qrTemp.getContext('2d');
const CERTIFICATE_BASE_URL = 'https://certificados.consigueventas.com/'; //CORRECION DE RUTA CERTIFICADO
let certificadoCargadoCorrectamente = false;



$(document).ready(function () {
    let curd_id = getUrlParameter('curd_id');

    if (!curd_id) return;

    $.post(BASE_URL + 'controller/usuario.php?op=mostrar_curso_detalle', { ID_certificado: curd_id }, function (data) {
        if (!data || typeof data !== 'object' || !data.nombre_usuario) return;

        let url_certificado = `${CERTIFICATE_BASE_URL}view/Certificado/index.php?curd_id=${curd_id}`;

        fetch(BASE_URL + 'controller/certificado.php?op=guardar_url_certificado', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                curd_id: curd_id,
                url_certificado: url_certificado
            })
        })
        .then(res => {
            if (!res.ok) throw new Error();
            return res.text();
        })
        .then(text => {
            let json = JSON.parse(text);
            if (json.status !== 'ok') throw new Error();

            return fetch(BASE_URL + 'controller/certificado.php?op=obtener_url_certificado', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ id_certificado: curd_id })
            });
        })
        .then(res => {
            if (!res.ok) throw new Error();
            return res.json();
        })
        .then(resp => {
            if (!resp || !resp.url || resp.url.trim() === "") return;

            const qr = new QRious({
                element: qrTemp,
                value: resp.url,
                size: 150,
                level: 'H'
            });

            image.onload = function () {
                ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
                const x = canvas.width / 2;

                ctx.font = 'bold 60px Arial';
                ctx.fillStyle = '#000';
                ctx.textAlign = "center";
                ctx.textBaseline = 'middle';
                ctx.fillText("CERTIFICADO", x, 150);

                ctx.font = '30px Arial';
                // Dividir el nombre del curso en líneas para mejor presentación
                const nombreCurso = data.nombre_curso;
                const maxAncho = canvas.width - 100; // Margen lateral
                const lineasCurso = dividirTextoEnLineas(ctx, nombreCurso, maxAncho);
                
                let yInicial = 220;
                lineasCurso.forEach((linea, index) => {
                    ctx.fillText(linea, x, yInicial + (index * 35));
                });

                ctx.font = '25px Arial';
                ctx.fillStyle = '#000';
                ctx.textAlign = "center";
                ctx.textBaseline = 'middle';
                const yOtorga = yInicial + (lineasCurso.length * 35) + 20;
                ctx.fillText("Otorga a:", x, yOtorga);

                ctx.font = '40px Arial';
                ctx.textAlign = "center";
                ctx.textBaseline = 'middle';
                const yNombre = yOtorga + 50;
                ctx.fillText(`${data.nombre_usuario} ${data.apellido_paterno_usuario} ${data.apellido_materno_usuario}`, x, yNombre);


               ctx.font = '20px Arial';
                const yTexto1 = yNombre + 60;
                ctx.fillText(
                    `La empresa Consigue Ventas otorga el presente certificado por haber`,
                    x,
                    yTexto1
                );
                ctx.fillText(
                    `participado en el programa de habilidades duras, con una duración de ${data.Horas} horas,`,
                    x,
                    yTexto1 + 30
                );
                const fechaInicioTexto = formatearFechaLarga(data.fecha_inicio_curso);
                const fechaFinTexto = formatearFechaLarga(data.fecha_fin_curso);
                ctx.fillText(
                    `desde el ${fechaInicioTexto} al ${fechaFinTexto}.`,
                    x,
                    yTexto1 + 60
                );
                ctx.fillText(
                    `Nota final obtenida: ${data.Nota}.`,
                    x,
                    yTexto1 + 90
                );


                const categoriaCodigo = data.nombre_categoria.substring(0, 3).toUpperCase();
                const dni = data.DNI;
                const anio = new Date(data.fecha_inicio_curso).getFullYear();
                const codigoGenerado = `${categoriaCodigo}-${dni}-${anio}`;

                ctx.font = '18px Arial';
                ctx.textAlign = "left";
                ctx.textBaseline = 'bottom';

                ctx.fillText(codigoGenerado, 200, canvas.height - 100);
                // puedes ajustar la posición Y


                
                const firma = new Image();
                firma.onload = function () {
                    const firmaWidth = 150;
                    const firmaHeight = 100;
                    // Posicionar la firma debajo de la nota final obtenida
                    const xFirma = (canvas.width / 2) - (firmaWidth / 2); // Centrada horizontalmente
                    const yFirma = yTexto1 + 130; // Debajo de la nota final con espaciado

                    ctx.drawImage(firma, xFirma, yFirma, firmaWidth, firmaHeight);
                    
                    // Agregar texto de identificación debajo de la firma
                    ctx.font = '14px Arial';
                    ctx.fillStyle = '#000';
                    ctx.textAlign = "center";
                    ctx.textBaseline = 'top';
                    ctx.fillText("JHOEL FERNANDEZ A.", xFirma + (firmaWidth / 2), yFirma + firmaHeight + 5);
                    ctx.fillText("GERENTE GENERAL", xFirma + (firmaWidth / 2), yFirma + firmaHeight + 25);
                };
                firma.src = BASE_URL + "view/Certificado/firma.jpg";

                // Mover el código QR hacia la derecha
                const qrX = canvas.width - 140; // Posición desde la derecha
                const qrY = 10; // Mantener en la parte superior
                ctx.drawImage(qrTemp, qrX, qrY, 120, 120);

                certificadoCargadoCorrectamente = true;
            };

            image.onerror = function () {};

            const fondo = data.fondo_certificado && data.fondo_certificado.trim() !== ""
                ? data.fondo_certificado
                : "certificado.png";

            image.src = BASE_URL + "public/img/certificado/" + fondo;
        })
        .catch(err => {});
    }, "json").fail(function () {});
});

$(document).on("click", "#btnpng", function () {
    let lblpng = document.createElement('a');
    lblpng.download = "Certificado.png";
    lblpng.href = canvas.toDataURL();
    lblpng.click();
});

$(document).on("click", "#btnpdf", function () {
    let imgData = canvas.toDataURL('image/png');
    let doc = new jsPDF('l', 'mm');
    doc.addImage(imgData, 'PNG', 8, 4, 280, 200);
    doc.save('Certificado.pdf');
});

function getUrlParameter(sParam) {
    let sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName;

    for (let i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

function formatearFechaLarga(fechaStr) {
    const meses = [
        "enero", "febrero", "marzo", "abril", "mayo", "junio",
        "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
    ];
    const [anio, mes, dia] = fechaStr.split("-");
    return `${parseInt(dia)} de ${meses[parseInt(mes) - 1]} del ${anio}`;
}

// Función para dividir texto largo en múltiples líneas
function dividirTextoEnLineas(ctx, texto, maxAncho) {
    const palabras = texto.split(' ');
    const lineas = [];
    let lineaActual = '';

    for (let i = 0; i < palabras.length; i++) {
        const pruebaLinea = lineaActual + (lineaActual.length > 0 ? ' ' : '') + palabras[i];
        const anchoLinea = ctx.measureText(pruebaLinea).width;
        
        if (anchoLinea > maxAncho && lineaActual.length > 0) {
            lineas.push(lineaActual);
            lineaActual = palabras[i];
        } else {
            lineaActual = pruebaLinea;
        }
    }
    
    if (lineaActual.length > 0) {
        lineas.push(lineaActual);
    }
    
    return lineas;
}


window.onload = function () {
    if (!sessionStorage.getItem('canvasLoaded')) {
        sessionStorage.setItem('canvasLoaded', true);
        window.location.reload();
    }
};
