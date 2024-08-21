// Constante para completar la ruta de la API.
const ADMIN_API = 'services/admin/administrador.php';
const CITA_API = 'services/admin/cita.php';
const MODELOS_API = 'services/admin/modelo.php';
const MARCA_API = 'services/admin/marca.php';


// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Constante para obtener el número de horas.
    const HOUR = new Date().getHours();
    // Se define una variable para guardar un saludo.
    let greeting = '';
    // Dependiendo del número de horas transcurridas en el día, se asigna un saludo para el usuario.
    if (HOUR < 12) {
        greeting = 'Buenos días';
    } else if (HOUR < 19) {
        greeting = 'Buenas tardes';
    } else {
        greeting = 'Buenas noches';
    }
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    //MAIN_TITLE.textContent = `${greeting}, bienvenido`;
    // Llamada a la funciones que generan los gráficos en la página web.
    graficoBarrasAdministradores();
    graficoLinealMarcas();
    graficoLinealModelos();
    graficoPastelEstado();
});

/*
*   Función asíncrona para mostrar un gráfico de barras con la cantidad de administradores.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const graficoBarrasAdministradores = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(ADMIN_API, 'cantidadAdministradores');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        // Se declaran los arreglos para guardar los datos a graficar.
        let roles = [];
        let cantidades = [];

        // Se agrega el dato al arreglo.
        DATA.dataset.forEach(row => {
            roles.push(row.nombre_rol);
            cantidades.push(row.cantidad);
        });
        
        // Llamada a la función para generar y mostrar un gráfico de barras. Se encuentra en el archivo components.js
        barGraph('chart1', roles, cantidades, 'Usuarios registrados', 'Cantidad de usuarios segun su rol');
    } else {
        document.getElementById('chart1').remove();
        console.log(DATA.error);
    }
}

const graficoLinealMarcas = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(MARCA_API, 'TopVehiculosPorMarcas');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        // Se declaran los arreglos para guardar los datos a graficar.
        let marcas = [];
        let cantidades = [];

        // Se agrega el dato al arreglo.
        DATA.dataset.forEach(row => {
            marcas.push(row.marca_vehiculo);
            cantidades.push(row.cantidad);
        });
        
        // Llamada a la función para generar y mostrar un gráfico de linea. Se encuentra en el archivo components.js
        lineChart('chart2', marcas, cantidades, 'Coches registrados', 'Cantidad de vehiculos por marca');
    } else {
        document.getElementById('chart2').remove();
        console.log(DATA.error);
    }
}

const graficoLinealModelos = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(MODELOS_API, 'TopVehiculosPorModelos');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        // Se declaran los arreglos para guardar los datos a graficar.
        let modelos = [];
        let cantidades = [];

        // Se agrega el dato al arreglo.
        DATA.dataset.forEach(row => {
            modelos.push(row.modelo_vehiculo);
            cantidades.push(row.cantidad);
        });
        
        // Llamada a la función para generar y mostrar un gráfico de barras. Se encuentra en el archivo components.js
        lineChart('chart3', modelos, cantidades, 'Coches registrados', 'Cantidad de vehiculos por modelo');
    } else {
        document.getElementById('chart3').remove();
        console.log(DATA.error);
    }
}

const graficoPastelEstado = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(CITA_API, 'PorcentajeEstadoCitas');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        // Se declaran los arreglos para guardar los datos a gráficar.
        let estado = [];
        let porcentajes = [];
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se agregan los datos a los arreglos.
            estado.push(row.estado_cita);
            porcentajes.push(row.porcentaje);
        });
        // Llamada a la función para generar y mostrar un gráfico de pastel. Se encuentra en el archivo components.js
        pieGraph('chart4', estado, porcentajes, 'Porcentaje de citas por estado');
    } else {
        document.getElementById('chart4').remove();
        console.log(DATA.error);
    }
}

// Cuando se hace clic en el botón, se expande o contrae una barra lateral en la página web. 
const hamBurger = document.querySelector(".toggle-btn");
hamBurger.addEventListener("click", function () {
    document.querySelector("#sidebar").classList.toggle("expand");
});
