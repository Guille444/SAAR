// Constante para completar la ruta de la API.
const ADMINISTRADOR_aPI = 'services/admin/administrador.php';
const LIBRERIA = 'libraries/sendCode.php';

// Constantes para llamar los elementos de la página
const BOTON = document.getElementById('boton');
const USUARIO_RECUPERACION = document.getElementById('alias');
const SAVE_FORM = document.getElementById('saveForm');

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();

    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    FORM.append('alias', USUARIO_RECUPERACION.value);

    try {
        // Petición para verificar el usuario.
        const DATA = await fetchData(ADMINISTRADOR_aPI, 'verifUs', FORM);

        // Se comprueba si la respuesta es satisfactoria
        if (DATA.status) {
            // Mostrar mensaje de éxito.
            await sweetAlert(1, 'Alias encontrado, revise su correo electrónico', true);

            // Si los datos de usuario son correctos, procedemos a enviar el correo.
            const userData = {
                codigo_recuperacion: DATA.dataset.codigo_recuperacion,
                alias_administrador: DATA.dataset.alias_administrador,
                correo_administrador: DATA.dataset.correo_administrador
            };

            console.log(userData);
            // Llamada a la función para enviar el correo con los datos.
            await sendMail(userData);

            window.location.href = `../../views/admin/verificar_codigo.html`;
        } else {
            // Mostrar mensaje de error.
            sweetAlert(2, DATA.error, false);
        }
    } catch (error) {
        console.error(error);
        sweetAlert(2, 'Error en la verificación del usuario', false);
    }
});

const sendMail = async (data) => {
    try {
        const formData = new FormData();
        formData.append('codigo_recuperacion', data.codigo_recuperacion);
        formData.append('alias_administrador', data.alias_administrador);
        formData.append('correo_administrador', data.correo_administrador);
        console.log(formData.data);

        const response = await fetchData(LIBRERIA, 'sendCode', formData);
    } catch (error) {
        console.error('Error en sendMail:', error.message);
    }
};