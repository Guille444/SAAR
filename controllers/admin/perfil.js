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

// Constantes para establecer los elementos del formulario de editar perfil.
const PROFILE_FORM = document.getElementById('profileForm'),
    NOMBRE_ADMINISTRADOR = document.getElementById('nombreAdministrador'),
    APELLIDO_ADMINISTRADOR = document.getElementById('apellidoAdministrador'),
    CORREO_ADMINISTRADOR = document.getElementById('correoAdministrador'),
    ALIAS_ADMINISTRADOR = document.getElementById('aliasAdministrador');
// Constante para establecer la modal de cambiar contraseña.
const PASSWORD_MODAL = new bootstrap.Modal('#passwordModal');
// Constante para establecer el formulario de cambiar contraseña.
const PASSWORD_FORM = document.getElementById('passwordForm');

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    // loadTemplate();
    // Se establece el título del contenido principal.
    // MAIN_TITLE.textContent = 'Editar perfil';
    // Petición para obtener los datos del usuario que ha iniciado sesión.
    const DATA = await fetchData(USER_API, 'readProfile');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se inicializan los campos del formulario con los datos del usuario que ha iniciado sesión.
        const ROW = DATA.dataset;
        NOMBRE_ADMINISTRADOR.value = ROW.nombre_administrador;
        APELLIDO_ADMINISTRADOR.value = ROW.apellido_administrador;
        CORREO_ADMINISTRADOR.value = ROW.correo_administrador;
        ALIAS_ADMINISTRADOR.value = ROW.alias_administrador;
    } else {
        sweetAlert(2, DATA.error, null);
    }
});

// Método del evento para cuando se envía el formulario de editar perfil.
PROFILE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(PROFILE_FORM);
    // Petición para actualizar los datos personales del usuario.
    const DATA = await fetchData(USER_API, 'editProfile', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        sweetAlert(1, DATA.message, true);
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

// Mètodo del evento para cuando se envía el formulario de cambiar contraseña.
PASSWORD_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(PASSWORD_FORM);
    // Petición para actualizar la constraseña.
    const DATA = await fetchData(USER_API, 'changePassword', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        PASSWORD_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

/*
*   Función para preparar el formulario al momento de cambiar la constraseña.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const openPassword = () => {
    // Se abre la caja de diálogo que contiene el formulario.
    PASSWORD_MODAL.show();
    // Se restauran los elementos del formulario.
    PASSWORD_FORM.reset();
}

function openTecnicoManual() {
    window.open('../../resources/pdfs/Manual Técnico.pdf', '_blank');
}

function openUsuarioManual() {
    window.open('../../resources/pdfs/Manual de Usuario.pdf', '_blank');
}