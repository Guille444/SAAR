// Constante para completar la ruta de la API.
const USUARIO_API = 'services/admin/administrador.php';

// Constantes para llamar los elementos de la página
const BOTON = document.getElementById('boton');
const CODIGO1 = document.getElementById('code1'),
    CODIGO2 = document.getElementById('code2'),
    CODIGO3 = document.getElementById('code3'),
    CODIGO4 = document.getElementById('code4'),
    CODIGO5 = document.getElementById('code5'),
    CODIGO6 = document.getElementById('code6');


// Constante para obtener los datos del formulario
const SAVE_FORM = document.getElementById('saveForm');

document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
});
// Función para obtener el código completo
const getCodigoCompleto = () => {
    return `${CODIGO1.value}${CODIGO2.value}${CODIGO3.value}${CODIGO4.value}${CODIGO5.value}${CODIGO6.value}`;
};

// Función para permitir solo números en los inputs
const inputs = [CODIGO1, CODIGO2, CODIGO3, CODIGO4, CODIGO5, CODIGO6];

// Función para moverse al siguiente input automáticamente
inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        // Eliminar cualquier caracter que no sea un dígito (0-9)
        input.value = input.value.replace(/\D/g, '');

        // Mover al siguiente input si se ingresó un número
        if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });

    // Agregar funcionalidad para retroceder al campo anterior si se presiona la tecla de retroceso (backspace)
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Backspace' && input.value.length === 0 && index > 0) {
            inputs[index - 1].focus();
        }
    });
});


// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();

    // Obtener el código completo
    const codigoCompleto = getCodigoCompleto();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    FORM.append('pinRecu', codigoCompleto);  // Añadir el código completo al formulario
    try {
        // Petición para verificar el usuario.
        const DATA = await fetchData(USUARIO_API, 'verifPin2FA', FORM);
        // Se comprueba si la respuesta es satisfactoria
        if (DATA.status) {
            // Mostrar mensaje de éxito.
            await sweetAlert(1, DATA.message, true, 'dashboard.html');
        } else {
            // Mostrar mensaje de error.
            sweetAlert(2, DATA.error, false);
            console.log(codigoCompleto);
        }
    } catch (error) {
        console.error(error);
        sweetAlert(2, 'Error en la verificación del usuario', false);
    }
});
