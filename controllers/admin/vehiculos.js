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

// Constantes para completar las rutas de la API.
const VEHICULO_API = 'services/admin/vehiculo.php';
// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');
// Constantes para establecer el contenido de la tabla.
TABLE_BODY = document.getElementById('tableBody'),
    ROWS_FOUND = document.getElementById('rowsFound');
// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    // MAIN_TITLE.textContent = 'Pedidos';
    // Llamada a la función para llenar la tabla con los registros existentes.
    fillTable();
});

// Método del evento para cuando se envía el formulario de buscar.
SEARCH_FORM.addEventListener('submit', (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SEARCH_FORM);
    // Llamada a la función para llenar la tabla con los resultados de la búsqueda.
    fillTable(FORM);
});

// Función asíncrona para llenar la tabla con los registros disponibles.
const fillTable = async (form = null) => {
    // Se inicializa el contenido de la tabla.
    ROWS_FOUND.textContent = '';
    TABLE_BODY.innerHTML = '';
    // Se verifica la acción a realizar.
    (form) ? action = 'searchRows' : action = 'readAll';
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(VEHICULO_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            console.log(DATA.dataset);
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre_completo}</td>
                    <td>${row.marca_vehiculo}</td>
                    <td>${row.modelo_vehiculo}</td>
                    <td>${row.placa_vehiculo}</td>
                    <td>${row.color_vehiculo}</td>
                    <td>${row.vin_motor}</td>
                </tr>
            `;  
        });
        // Se muestra un mensaje de acuerdo con el resultado.
        ROWS_FOUND.textContent = DATA.message;
    } else {
        sweetAlert(4, DATA.error, true);
    }
}



// Cuando se hace clic en el botón, se expande o contrae una barra lateral en la página web. 
const hamBurger = document.querySelector(".toggle-btn");
hamBurger.addEventListener("click", function () {
    document.querySelector("#sidebar").classList.toggle("expand");
});