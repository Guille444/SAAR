const tiempoInactividad = 10000; // 1 minuto en milisegundos
const tiempoAdvertencia = 5000; // 50 segundos en milisegundos
let temporizador;
let temporizadorAdvertencia;
let countdownInterval;
let tiempoUltimaActividad = Date.now();
let advertenciaMostrada = false;

// Función para resetear el temporizador de inactividad
const resetearTemporizador = () => {
    clearTimeout(temporizador);
    clearTimeout(temporizadorAdvertencia);
    clearInterval(countdownInterval); // Limpiar temporizador de cuenta atrás
    advertenciaMostrada = false;
    tiempoUltimaActividad = Date.now();

    // Configurar temporizador para advertencia y cierre de sesión
    temporizadorAdvertencia = setTimeout(mostrarAdvertencia, tiempoAdvertencia);
    temporizador = setTimeout(cerrarSesion, tiempoInactividad);
};

// Función para mostrar la advertencia con sweetAlert2
const mostrarAdvertencia = () => {
    if (!advertenciaMostrada) {
        advertenciaMostrada = true;
        const tiempoRestante = Math.max(0, Math.ceil((tiempoInactividad - (Date.now() - tiempoUltimaActividad)) / 1000));

        // Usar sweetAlert existente
        sweetAlert(3, `Tu sesión está a punto de expirar. Tiempo restante: ${tiempoRestante} segundos`, true);

        countdownInterval = setInterval(() => {
            const tiempoRestante = Math.max(0, Math.ceil((tiempoInactividad - (Date.now() - tiempoUltimaActividad)) / 1000));
            // Aquí puedes agregar una lógica para actualizar la cuenta regresiva si la necesitas.
        }, 1000);
    }
};

// Función para cerrar sesión
const cerrarSesion = async () => {
    const DATA = await fetchData(USER_API, 'logOut');
    if (DATA.status) {
        sweetAlert(1, DATA.message, true);
        // Redirigir después de mostrar la alerta
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 2000); // Espera 2 segundos para permitir que la alerta se vea
    } else {
        sweetAlert(4, 'Tu sesión ha sido expirada por inactividad', false);
    }
};

// Función para comprobar inactividad
const comprobarInactividad = () => {
    if (Date.now() - tiempoUltimaActividad > tiempoInactividad) {
        cerrarSesion();
    }
};

// Manejador de eventos de actividad (scroll, mousemove, keypress)
document.addEventListener('mousemove', resetearTemporizador);
document.addEventListener('keypress', resetearTemporizador);
document.addEventListener('scroll', resetearTemporizador);

// Revisar la inactividad en intervalos regulares
setInterval(comprobarInactividad, 1000); // Comprobar cada segundo

// Iniciar temporizadores al cargar la página
resetearTemporizador();