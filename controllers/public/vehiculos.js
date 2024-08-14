// Constante para completar la ruta de la API.
const VEHICULOS_API = 'services/public/vehiculo.php';
// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');
// Constantes para establecer los elementos de la tabla.
const TABLE_BODY = document.getElementById('tableBody'),
    ROWS_FOUND = document.getElementById('rowsFound');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal'),
    MODAL_TITLE = document.getElementById('modalTitle');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_VEHICULO = document.getElementById('idVehiculo'),
    MARCA_VEHICULO = document.getElementById('marcaVehiculo'),
    MODELO_VEHICULO = document.getElementById('modeloVehiculo'),
    ANIO_VEHICULO = document.getElementById('anioVehiculo'),
    PLACA_VEHICULO = document.getElementById('placaVehiculo'),
    COLOR_VEHICULO = document.getElementById('colorVehiculo'),
    VIN_VEHICULO = document.getElementById('vinVehiculo');

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Se establece el título del contenido principal.
    loadTemplate();
    // Llamada a la función para llenar la tabla con los registros existentes.
    fillTable();
    // Llamada a la función para cargar las marcas.
    loadMarcas();
});

MARCA_VEHICULO.addEventListener('change', (event) => {
    const idMarca = event.target.value;
    console.log('Marca seleccionada:', idMarca);  // Verifica el valor seleccionado
    loadModelos(idMarca);
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

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se verifica la acción a realizar.
    const action = ID_VEHICULO.value ? 'updateRow' : 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(VEHICULOS_API, action, FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        SAVE_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
        // Se carga nuevamente la tabla para visualizar los cambios.
        fillTable();
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

/*
 * Función asíncrona para llenar la tabla con los registros disponibles.
 * Parámetros: form (objeto opcional con los datos de búsqueda).
 * Retorno: ninguno.
 */
const fillTable = async (form = null) => {
    ROWS_FOUND.textContent = '';
    TABLE_BODY.innerHTML = '';
    const action = form ? 'searchRows' : 'readAllByClient';
    const DATA = await fetchData(VEHICULOS_API, action, form);

    // Verificar la estructura de DATA
    console.log('DATA:', DATA);

    if (DATA && !DATA.error) {  // Modificado para manejar DATA.error
        const dataset = Array.isArray(DATA) ? DATA : Object.values(DATA); // Convertir DATA a un array si es necesario
        dataset.forEach(row => {
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.marca_vehiculo}</td>
                    <td>${row.modelo_vehiculo}</td>
                    <td>${row.año_vehiculo}</td>
                    <td>${row.placa_vehiculo}</td>
                    <td>${row.color_vehiculo}</td>
                    <td>${row.vin_motor}</td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="openUpdate(${row.id_vehiculo})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="openDelete(${row.id_vehiculo})">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        ROWS_FOUND.textContent = DATA.message || 'Datos cargados exitosamente.';
    } else {
        sweetAlert(4, DATA.error || 'Error al procesar los datos', true);
    }
}

/*
 * Función para cargar las marcas en el selector de marcas.
 * Parámetros: ninguno.
 * Retorno: ninguno.
 */
const loadMarcas = async () => {
    // Petición para obtener las marcas disponibles.
    const DATA = await fetchData(VEHICULOS_API, 'getMarcas');

    // Se inicializa el contenido del selector de marcas.
    MARCA_VEHICULO.innerHTML = '<option value="">Seleccione una marca</option>';

    if (DATA.status) {
        if (DATA.dataset.length > 0) {
            // Se recorre el conjunto de registros fila por fila.
            DATA.dataset.forEach(row => {
                // Se crean y concatenan las opciones del selector de marcas.
                MARCA_VEHICULO.innerHTML += `<option value="${row.id_marca}">${row.marca_vehiculo}</option>`;
            });
        } else {
            // Si no hay marcas, se muestra un mensaje en el selector.
            MARCA_VEHICULO.innerHTML = '<option value="">No hay marcas disponibles</option>';
        }

        // Se agrega un evento para cargar los modelos cuando se selecciona una marca.
        MARCA_VEHICULO.addEventListener('change', (event) => {
            loadModelos(event.target.value);
        });
    } else {
        // Aquí podrías manejar otros tipos de errores si es necesario
        // pero sin mostrar alertas grandes.
        console.error(DATA.message); // Mostrar el error en la consola para depuración.
    }
}

/*
 * Función para cargar los modelos de una marca en el selector de modelos.
 * Parámetros: idMarca (identificador de la marca seleccionada).
 * Retorno: ninguno.
 */
const loadModelos = async (idMarca) => {
    if (idMarca) {
        const FORM = new FormData();
        FORM.append('id_marca', idMarca);
        console.log('Formulario de envío:', [...FORM.entries()]);  // Verifica el contenido del FormData

        const DATA = await fetchData(VEHICULOS_API, 'getModelosByMarca', FORM);
        console.log('Respuesta del servidor:', DATA);  // Verifica la respuesta del servidor

        if (DATA.status) {
            MODELO_VEHICULO.innerHTML = '<option value="">Seleccione un modelo</option>';
            DATA.dataset.forEach(row => {
                MODELO_VEHICULO.innerHTML += `<option value="${row.id_modelo}">${row.modelo_vehiculo}</option>`;
            });
        } else {
            sweetAlert(4, DATA.error, true);
        }
    } else {
        MODELO_VEHICULO.innerHTML = '<option value="">Seleccione un modelo</option>';
    }
}


/*
 * Función para preparar el formulario al momento de insertar un registro.
 * Parámetros: ninguno.
 * Retorno: ninguno.
 */
const openCreate = () => {
    // Se muestra la caja de diálogo con su título.
    SAVE_MODAL.show();
    MODAL_TITLE.textContent = 'Registrar vehículo';
    // Se prepara el formulario.
    SAVE_FORM.reset();
    // Se vacían los selectores de marcas y modelos.
    MARCA_VEHICULO.innerHTML = '<option value="">Seleccione una marca</option>';
    MODELO_VEHICULO.innerHTML = '<option value="">Seleccione un modelo</option>';
    // Se recarga la lista de marcas.
    loadMarcas();
}

/*
 * Función asíncrona para preparar el formulario al momento de actualizar un registro.
 * Parámetros: id (identificador del registro seleccionado).
 * Retorno: ninguno.
 */
const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idVehiculo', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(VEHICULOS_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar información de vehículo';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_VEHICULO.value = ROW.id_vehiculo;
        MARCA_VEHICULO.value = ROW.id_marca;
        await loadModelos(ROW.id_marca);
        MODELO_VEHICULO.value = ROW.id_modelo;
        ANIO_VEHICULO.value = ROW.anio_vehiculo;
        PLACA_VEHICULO.value = ROW.placa_vehiculo;
        COLOR_VEHICULO.value = ROW.color_vehiculo;
        VIN_VEHICULO.value = ROW.vin_motor;
    } else {
        sweetAlert(2, DATA.error, false);
    }
}

/*
 * Función asíncrona para eliminar un registro.
 * Parámetros: id (identificador del registro seleccionado).
 * Retorno: ninguno.
 */
const openDelete = async (id) => {
    // Llamada a la función para mostrar un mensaje de confirmación, capturando la respuesta en una constante.
    const RESPONSE = await confirmAction('¿Desea eliminar el registro del vehículo?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idVehiculo', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(VEHICULOS_API, 'deleteRow', FORM);
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se muestra un mensaje de éxito.
            await sweetAlert(1, DATA.message, true);
            // Se carga nuevamente la tabla para visualizar los cambios.
            fillTable();
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
}

// Cuando se hace clic en el botón, se expande o contrae una barra lateral en la página web. 
const hamBurger = document.querySelector(".toggle-btn");
hamBurger.addEventListener("click", function () {
    document.querySelector("#sidebar").classList.toggle("expand");
});