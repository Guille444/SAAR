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
const CITA_API = 'services/admin/cita.php';
const CLIENTE_API = 'services/admin/cliente.php';
const VEHICULO_API = 'services/admin/vehiculo.php';
const SERIVICIO_API = 'services/admin/servicio.php';
// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');
// Constantes para establecer el contenido de la tabla.
TABLE_BODY = document.getElementById('tableBody'),
    ROWS_FOUND = document.getElementById('rowsFound');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal'),
    CHART_MODAL = new bootstrap.Modal('#chartModal'),
    MODAL_TITLE = document.getElementById('modalTitle');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_CITA = document.getElementById('idCita'),
    CLIENTE_CITA = document.getElementById('nombreCliente'),
    VEHICULO_CITA = document.getElementById('nombreModelo'),
    SERVICIO_CITA = document.getElementById('nombreServicio'),
    FECHA_CITA = document.getElementById('fechaCita'),
    HORA_CITA = document.getElementById('horaCita')
ESTADO_CITA = document.getElementById('estadoCita');
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

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se verifica la acción a realizar.
    //(ID_PEDIDO.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(CITA_API, 'updateRow', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        SAVE_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
        ID_CITA.value = null;
        // Se carga nuevamente la tabla para visualizar los cambios.
        fillTable();
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

// Función asíncrona para llenar la tabla con los registros disponibles.
const fillTable = async (form = null) => {
    ROWS_FOUND.textContent = '';
    TABLE_BODY.innerHTML = '';
    
    const action = form ? 'searchRows' : 'readAll';
    const DATA = await fetchData(CITA_API, action, form);

    if (DATA.status) {
        // Verifica que dataset no esté vacío
        if (DATA.dataset.length > 0) {
            DATA.dataset.forEach(row => {
                const formattedHour = formatHour(row.hora_cita);
                TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.cliente}</td>
                    <td>${row.vehiculo}</td>
                    <td>
                        <button class="btn" id="btn1" onclick="openServices(${row.id_cita})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                    <td>${row.fecha}</td>
                    <td>${formattedHour}</td>
                    <td>${row.estado_cita}</td>
                    <td>
                        <button id="btn1" type="button" class="btn" onclick="openUpdate(${row.id_cita})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                    </td>
                </tr>
                `;
            });
            ROWS_FOUND.textContent = DATA.message; // Muestra el mensaje de coincidencias
        } else {
            ROWS_FOUND.textContent = "No se encontraron resultados."; // Mensaje si no hay resultados
        }
    } else {
        sweetAlert(4, DATA.error, true);
    }
};


// Función para convertir la hora a formato de 12 horas
const formatHour = (time) => {
    const [hour, minute] = time.split(':');
    const period = hour >= 12 ? 'p.m.' : 'a.m.';
    const hour12 = hour % 12 || 12; // Convierte a 12 horas y maneja el caso de 0
    return `${hour12}:${minute} ${period}`;
}


// Función asíncrona para preparar el formulario al momento de actualizar un registro.
const openUpdate = async (id) => {
    // Se define un objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idCita', id);

    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(CITA_API, 'readOne', FORM);

    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar estado de cita';

        // Se prepara el formulario.
        SAVE_FORM.reset();

        // Desactivar los campos que no deben ser editados.
        CLIENTE_CITA.disabled = true;
        VEHICULO_CITA.disabled = true;
        SERVICIO_CITA.disabled = true;
        FECHA_CITA.disabled = true;
        HORA_CITA.disabled = true;

        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;

        // Verificar los nombres de las propiedades en ROW
        ID_CITA.value = ROW.id_cita; // Id de la cita
        CLIENTE_CITA.value = ROW.cliente; // Nombre del cliente
        VEHICULO_CITA.value = ROW.vehiculo; // Placa del vehículo
        SERVICIO_CITA.value = ROW.servicios; // Nombre del servicio
        FECHA_CITA.value = ROW.fecha; // Fecha de la cita
        HORA_CITA.value = ROW.hora_cita;

        // Establecer el valor del estado en el select
        for (let i = 0; i < ESTADO_CITA.options.length; i++) {
            if (ESTADO_CITA.options[i].value === ROW.estado_cita) {
                ESTADO_CITA.selectedIndex = i;
                break;
            }
        }
    } else {
        sweetAlert(2, DATA.error, false);
    }
}

/*
*   Función para abrir un reporte automático de citas_predictivo
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const openReport = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/admin/citas_predictivas.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

//Funcion para abrir los graficos predictivos

const openChart = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(CITA_API, 'PrediccionGananciaAnual');
    const DATA2 = await fetchData(CITA_API, 'PrediccionCitasAnual');
    console.log(DATA.dataset);
    console.log(DATA2.dataset);

    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        CHART_MODAL.show();
        // Se declaran los arreglos para guardar los datos a graficar.
        let año = [];
        let añoCitas = [];
        let valores = [];
        let citas = []
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se agregan los datos a los arreglos.
            año.push(row.Año);
            valores.push(row.Ganancias);
        });

        DATA2.dataset.forEach(row => {
            // Se agregan los datos a los arreglos.
            añoCitas.push(row.Año);
            citas.push((row.Citas).toFixed(0));
        });

        // Se agrega la etiqueta canvas al contenedor de la modal.
        document.getElementById('chartContainer').innerHTML = `
            <canvas id="chart"></canvas>
            <canvas id="chart2"></canvas>
            `;
        // Llamada a la función para generar y mostrar un gráfico predictivos. Se encuentra en el archivo components.js
        lineChart('chart', año, valores, 'Ganancias en dólares', 'Datos predictivos en los proximos 3 años');
        lineChart('chart2', añoCitas, citas, 'Cantidad de Citas', 'Prediccion de las citas en el proximo año');
    } else {
        sweetAlert(4, DATA.error, true);
    }
}

// Cuando se hace clic en el botón, se expande o contrae una barra lateral en la página web. 
const hamBurger = document.querySelector(".toggle-btn");
hamBurger.addEventListener("click", function () {
    document.querySelector("#sidebar").classList.toggle("expand");
});

const openServices = async (idCita) => {
    // Limpia el contenido del elemento con ID 'servicesList'
    document.getElementById('servicesList').innerHTML = '';

    // Crea un nuevo objeto FormData para enviar datos
    const FORM = new FormData();
    // Agrega el ID de la cita al objeto FormData
    FORM.append('idCita', idCita);

    // Llama a la función fetchData para obtener los servicios de la cita
    const DATA = await fetchData(CITA_API, 'getServicesByCita', FORM);

    // Verifica si la respuesta fue exitosa
    if (DATA.status) {
        // Itera sobre el conjunto de servicios devuelto
        DATA.dataset.forEach(service => {
            // Agrega cada nombre de servicio a la lista en el HTML
            document.getElementById('servicesList').innerHTML += `
                <p>- ${service.nombre_servicio}</p>
            `;
        });

        // Crea una instancia del modal de Bootstrap
        const servicesModal = new bootstrap.Modal(document.getElementById('servicesModal'));
        // Muestra el modal con la lista de servicios
        servicesModal.show();
    } else {
        // Si hubo un error, muestra un mensaje usando sweetAlert
        sweetAlert(2, DATA.error, false);
    }
};
