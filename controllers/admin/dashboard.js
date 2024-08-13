// Constante para completar la ruta de la API.
const ADMIN_API = 'services/admin/administrador.php';

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
        let administradores = [];
        let cantidades = [];

        // Se agrega el dato al arreglo.
        DATA.dataset.forEach(row => {
            administradores.push('Administradores');
            cantidades.push(row.cantidad);
        });
        
        // Llamada a la función para generar y mostrar un gráfico de barras. Se encuentra en el archivo components.js
        barGraph('chart1', administradores, cantidades, 'Cantidad de administradores', 'Cantidad de administradores registrados');
    } else {
        document.getElementById('chart1').remove();
        console.log(DATA.error);
    }
}

// Cuando se hace clic en el botón, se expande o contrae una barra lateral en la página web. 
const hamBurger = document.querySelector(".toggle-btn");
hamBurger.addEventListener("click", function () {
    document.querySelector("#sidebar").classList.toggle("expand");
});
