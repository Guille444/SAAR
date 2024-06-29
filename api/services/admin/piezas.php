<?php
// Se incluye la clase del modelo.
require_once('../../models/data/piezas_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pieza = new PiezaData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como pieza, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un pieza ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $pieza->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
                case 'createRow':
                    $_POST = Validator::validateForm($_POST);
                    if (
                        !$pieza->setPieza($_POST['AgregarPieza']) or
                        !$pieza->setPrecio($_POST['AgregarPrecio']) or
                        !$pieza->setDescripcion($_POST['AgregarDescripcion']) or
                        !$pieza->setIdCliente($_POST['cliente']) 
                    ) {
                        $result['error'] = $pieza->getDataError();
                    } elseif ($pieza->createRow()) {
                        $result['status'] = 1;
                        $result['message'] = 'Pieza creada correctamente';
                    } else {
                        $result['error'] = 'Ocurrió un problema al crear la pieza';
                    }
                    break;
            case 'readAll':
                if ($result['dataset'] = $pieza->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen piezas registrados';
                }
                break;
            case 'readOne':
                if (!$pieza->setId($_POST['idPieza'])) {
                    $result['error'] = 'pieza incorrecto';
                } elseif ($result['dataset'] = $pieza->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Pieza inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$pieza->setId($_POST['idPieza']) or 
                    !$pieza->setPieza($_POST['AgregarPieza']) or
                    !$pieza->setPrecio($_POST['AgregarPrecio']) or
                    !$pieza->setDescripcion($_POST['AgregarDescripcion']) or
                    !$pieza->setIdCliente($_POST['cliente']) 
                ) {
                    $result['error'] = $pieza->getDataError();
                } elseif ($pieza->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'pieza modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el pieza';
                }

                break;
                case 'deleteRow':
                    if (
                        !$pieza->setId($_POST['idPieza'])
                    ) {
                        $result['error'] = $pieza->getDataError();
                    } elseif ($pieza->deleteRow()) {
                        $result['status'] = 1;
                        $result['message'] = 'pieza eliminada correctamente';
                        
                    } else {
                        $result['error'] = 'Ocurrió un problema al eliminar la pieza';
                    }
                    break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el pieza no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readUsers':
                if ($pieza->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['error'] = 'Debe crear un pieza para comenzar';
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
