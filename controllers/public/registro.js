// Constante para establecer el formulario de registrar cliente.
const SIGNUP_FORM = document.getElementById('signupForm');
// Llamada a la función para establecer la mascara del campo teléfono.
vanillaTextMask.maskInput({
    inputElement: document.getElementById('contactoCliente'),
    mask: [/\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]
});
// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    // loadTemplate();
    // Se asigna como título la categoría de los productos.
    // MAIN_TITLE.textContent = 'Crear cuenta';
    // LLamada a la función para asignar el token del reCAPTCHA al formulario.
    // reCAPTCHA();
});

// Método del evento para cuando se envía el formulario de registrar cliente.
SIGNUP_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SIGNUP_FORM);
    // Petición para registrar un cliente.
    const DATA = await fetchData(USER_API, 'signUp', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        sweetAlert(1, DATA.message, true, 'index.html');
    } else if (DATA.recaptcha) {
        sweetAlert(2, DATA.error, false, 'index.html');
    } else {
        sweetAlert(2, DATA.error, false);
        // Se genera un nuevo token cuando ocurre un problema.
        // reCAPTCHA();
    }
});