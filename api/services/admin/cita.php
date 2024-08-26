<?php

/* 
Estándares de programación en PHP:
1. Los archivos PHP deben iniciar con la etiqueta "<?php".
2. El PHP debe estar escrito en el formato de codificación UTF-8.
3. Un archivo PHP debe definir si será usado para generar conexiones con "require" o para definir clases o funciones,
   pero nunca debe usar ambos en el mismo archivo, a menos que se utilicen condicionales.
4. Las clases deben estar escritas en StudlyCaps.
5. Los métodos tienen que estar escritos en camelCase.
*/

// Se incluye la clase del modelo de citas, necesaria para interactuar con la base de datos.
require_once('../../models/data/citas_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario, se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se inicia o reanuda una sesión para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente de citas para gestionar las operaciones relacionadas.
    $citas = new CitasData;
    // Se declara e inicializa un arreglo para almacenar el resultado que será retornado por la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);

    // Se verifica si existe una sesión iniciada como administrador, de lo contrario, se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se establece que hay una sesión activa.
        $result['session'] = 1;
        // Se evalúa la acción solicitada para realizar la operación correspondiente.
        switch ($_GET['action']) {
            // Acción para buscar citas en la base de datos.
            case 'searchRows':
                // Validar la búsqueda de citas y ejecutar la consulta si es válida.
                if (!Validator::validateSearch($_POST['search'])) {
                    // Si la validación falla, se guarda el mensaje de error.
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $citas->searchRows()) {
                    // Si la búsqueda es exitosa, se actualiza el estado y mensaje del resultado.
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    // Si no hay coincidencias, se establece un mensaje de error.
                    $result['error'] = 'No hay coincidencias';
                }
                break;

            // Acción para leer todas las citas registradas.
            case 'readAll':
                // Leer todos los registros de citas desde la base de datos.
                if ($result['dataset'] = $citas->readAll()) {
                    // Si la consulta es exitosa, se actualiza el estado y mensaje del resultado.
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    // Si no hay citas registradas, se establece un mensaje de error.
                    $result['error'] = 'No existen citas registradas';
                }
                break;

            // Acción para leer los datos de una cita específica.
            case 'readOne':
                // Validar el ID de la cita y leer sus datos si es válido.
                if (!$citas->setId($_POST['idCita'])) {
                    // Si el ID es incorrecto, se establece un mensaje de error.
                    $result['error'] = 'Cita incorrecta';
                } elseif ($result['dataset'] = $citas->readOne()) {
                    // Si la cita existe, se actualiza el estado del resultado.
                    $result['status'] = 1;
                } else {
                    // Si la cita no existe, se establece un mensaje de error.
                    $result['error'] = 'Cita inexistente';
                }
                break;

            // Acción para actualizar los datos de una cita.
            case 'updateRow':
                // Validar y actualizar el estado de una cita específica.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$citas->setId($_POST['idCita']) or
                    !$citas->setEstado($_POST['estadoCita'])
                ) {
                    // Si la validación falla, se guarda el mensaje de error.
                    $result['error'] = $citas->getDataError();
                } elseif ($citas->updateRow()) {
                    // Si la actualización es exitosa, se actualiza el estado y mensaje del resultado.
                    $result['status'] = 1;
                    $result['message'] = 'Cita modificada correctamente';
                } else {
                    // Si la actualización falla, se establece un mensaje de error.
                    $result['error'] = 'Ocurrió un problema al modificar la cita';
                }
                break;

            // Acción para obtener el porcentaje de citas por estado.
            case 'PorcentajeEstadoCitas':
                if ($result['dataset'] = $citas->PorcentajeEstadoCitas()) {
                    // Si la consulta es exitosa, se actualiza el estado y mensaje del resultado.
                    $result['status'] = 1;
                    $result['message'] = 'Porcentaje de los estados de las citas obtenido correctamente';
                } else {
                    // Si la consulta falla, se establece un mensaje de error.
                    $result['error'] = 'No se pudo obtener el porcentaje de los estados de las citas';
                }
                break;

            // Acción para predecir las ganancias anuales basadas en las citas.
            case 'PrediccionGananciaAnual':
                if ($result['dataset'] = $citas->PrediccionGananciaAnual()) {
                    // Si la consulta es exitosa, se actualiza el estado y mensaje del resultado.
                    $result['status'] = 1;
                    $result['message'] = 'Ganancias anuales obtenidas correctamente';
                } else {
                    // Si la consulta falla, se establece un mensaje de error.
                    $result['error'] = 'No se pudo obtener la predicción de ganancias anuales';
                }
                break;

            // Acción para predecir el número de citas anuales.
            case 'PrediccionCitasAnual':
                if ($result['dataset'] = $citas->PrediccionCitasAnual()) {
                    // Si la consulta es exitosa, se actualiza el estado y mensaje del resultado.
                    $result['status'] = 1;
                    $result['message'] = 'Citas anuales obtenidas correctamente';
                } else {
                    // Si la consulta falla, se establece un mensaje de error.
                    $result['error'] = 'No se pudo obtener la predicción de citas anuales';
                }
                break;
        }
    } else {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
            // Acción para leer todas las citas, pero requiere autenticación.
            case 'readUsers':
                // Verifica si se puede leer todas las citas y si es necesario autenticarse.
                if ($citas->readAll()) {
                    // Si hay citas registradas, se establece un mensaje solicitando autenticación.
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    // Si no hay citas registradas, se establece un mensaje de error.
                    $result['error'] = 'Debe crear un administrador para comenzar';
                }
                break;

            // Acción por defecto si no hay una sesión iniciada.
            default:
                // Se establece un mensaje de error para acciones no disponibles fuera de sesión.
                $result['error'] = 'Acción no disponible fuera de la sesión';
        }
    }

    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();

    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');

    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    // Si no se especifica ninguna acción, se indica que el recurso no está disponible.
    print(json_encode('Recurso no disponible'));
}
