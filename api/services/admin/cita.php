<?php
// Se incluye la clase del modelo.
require_once('../../models/data/citas_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $citas = new CitasData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como valoracion$valoracion, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un valoracion$valoracion ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $citas->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $citas->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen citas registrados';
                }
                break;
            case 'readOne':
                if (!$citas->setId($_POST['idCita'])) {
                    $result['error'] = 'Cita incorrecta';
                } elseif ($result['dataset'] = $citas->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Cita inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$citas->setId($_POST['idCita']) or
                    !$citas->setEstado($_POST['estadoCita'])
                ) {
                    $result['error'] = $citas->getDataError();
                } elseif ($citas->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cita modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el cita';
                }

                break;
        }
    } else {
        // Se compara la acción a realizar cuando el valoracion$valoracion no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readUsers':
                if ($citas->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['error'] = 'Debe crear un valoracion$valoracion para comenzar';
                }
                break;
            default:
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
    print(json_encode('Recurso no disponible'));
}