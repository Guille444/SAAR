/* 
Algunos estanderes de programacion en javascript

1. Los archivos tienen que estar codificados en UTF-8
2. Los nombres de las constantes deben estar escritas en mayúscula y separado con guones bajos
3. Los nombres de las funciones deben estar escritas en camelCase
4. No usar Tab para sangría del código, mejor usar la tecla "espacio"
5. Terminar cada parrafo de código con un punto y coma ";"
6. Al momento de usar corchetes, dejar el corchete de apertura en la primera linea y 
el corchete de cierre en una linea nueva, abajo del código
7. Evitar lineas de mas de 80 pálabras
*/

// Constante para establecer el formulario de registro del primer usuario.
const SIGNUP_FORM = document.getElementById('signupForm');
// Constante para establecer el formulario de inicio de sesión.
const LOGIN_FORM = document.getElementById('loginForm');
const USUARIO_ADMIN = document.getElementById('alias');
const LIBRERIA = 'libraries/twofa.php';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Petición para consultar los usuarios registrados.
    const DATA = await fetchData(USER_API, 'readUsers');
    // Se comprueba si existe una sesión, de lo contrario se sigue con el flujo normal.
    if (DATA.session) {
        // Se direcciona a la página web de bienvenida.
        location.href = 'dashboard.html';
    } else if (DATA.status) {
        // Se establece el título del contenido principal.
        // MAIN_TITLE.textContent = 'Iniciar sesión';
        // Se muestra el formulario para iniciar sesión.
        LOGIN_FORM.classList.remove('d-none');
        sweetAlert(4, DATA.message, true);
    } else {
        // Se establece el título del contenido principal.
        // MAIN_TITLE.textContent = 'Registrar primer usuario';
        // Se muestra el formulario para registrar el primer usuario.
        SIGNUP_FORM.classList.remove('d-none');
        sweetAlert(4, DATA.error, true);
    }
});

// Método del evento para cuando se envía el formulario de registro del primer usuario.
SIGNUP_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SIGNUP_FORM);
    // Petición para registrar el primer usuario del sitio privado.
    const DATA = await fetchData(USER_API, 'signUp', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        sweetAlert(1, DATA.message, true, 'index.html');
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

// Método del evento para cuando se envía el formulario de inicio de sesión.
LOGIN_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(LOGIN_FORM);
    // Petición para iniciar sesión.
    const DATA = await fetchData(USER_API, 'logIn', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.dataset == 1) {
        sweetAlert(1, DATA.message, true, 'dashboard.html');
    } else if (DATA.dataset == 2) {
        sweetAlert(2, DATA.message, true);
    } else if (DATA.dataset == 3) {
        sweetAlert(3, DATA.message, true);
    } else if (DATA.dataset == 4) {
        sweetAlert(4, DATA.message, true, 'cambiar_clave.html');
    } else if (DATA.dataset == 5) {
        sweetAlert(4, DATA.message, true);
        const FORM = new FormData();
        FORM.append('alias', USUARIO_ADMIN.value);
        try {
            const DATA = await fetchData(USER_API, 'verif2FA', FORM);
            if (DATA.status) {
                const userData = {
                    pin_usuario: DATA.dataset.codigo_recuperacion,
                    alias_usuario: DATA.dataset.alias_administrador,
                    email_usuario: DATA.dataset.correo_administrador
                };
                console.log(userData);
                // Llamada a la función para enviar el correo con los datos.
                await sendMail(userData);

                window.location.href = `../../views/admin/codigo_autenticacion.html`;
            } else {
                sweetAlert(2, DATA.error, false);
            }
        } catch (error) {
            console.error(error);
            sweetAlert(2, 'Error en la verificación del usuario', false);
        }
    }
});

// Función para enviar el correo
const sendMail = async (data) => {
    try {
        const formData = new FormData();
        formData.append('pin', data.pin_usuario);
        formData.append('user', data.alias_usuario);
        formData.append('email', data.email_usuario);
        console.log(data);

        const response = await fetchData(LIBRERIA, 'twofa', formData);
        console.log('Correo enviado:', response);
    } catch (error) {
        console.error('Error en sendMail:', error.message);
    }
};