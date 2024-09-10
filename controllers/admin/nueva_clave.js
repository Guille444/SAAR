// Constante para completar la ruta de la API.
const ADMINISTRADOR_API = 'services/admin/administrador.php';

// Constantes para llamar los elementos de la página
const BOTON = document.getElementById('boton');
const CONTRASENIA_NUEVA = document.getElementById('nuevaClave');
const CONTRASENIA_VERIFICACION = document.getElementById('confirmarClave');
const SAVE_FORM = document.getElementById('saveForm');

document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    // loadTemplate();
});

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();

    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    FORM.append('nuevaClave', CONTRASENIA_NUEVA.value);
    FORM.append('confirmarClave', CONTRASENIA_VERIFICACION.value);

    try {
        // Petición para verificar el usuario.
        const DATA = await fetchData(ADMINISTRADOR_API, 'changePasswordRecup', FORM);

        // Se comprueba si la respuesta es satisfactoria
        if (DATA.status) {
            // Mostrar mensaje de éxito.
            await sweetAlert(1, 'Contraseña cambiada correctamente', true);
            window.location.href = `../../views/admin/index.html`;
        } else {
            // Mostrar mensaje de error.
            sweetAlert(2, DATA.error, false);
        }
    } catch (error) {
        console.error(error);
        sweetAlert(2, 'Error en la verificación del usuario', false);
    }
});