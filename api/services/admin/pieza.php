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

    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Acción para buscar registros de piezas.
            case 'searchRows':
                // Validar y ejecutar la búsqueda de registros de piezas.
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $pieza->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;

                // Acción para crear una nueva pieza.
            case 'createRow':
                // Validar y crear una nueva pieza.
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

                // Acción para leer todas las piezas.
            case 'readAll':
                // Leer todas las piezas.
                if ($result['dataset'] = $pieza->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen piezas registradas';
                }
                break;

                // Acción para leer todas las piezas de un cliente específico.
            case 'readAll2':
                // Leer todas las piezas de un cliente específico.
                if ($result['dataset'] = $pieza->readAll2()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen piezas registradas';
                }
                break;

                // Acción para leer una pieza específica por su ID.
            case 'readOne':
                // Validar y leer una pieza específica por su ID.
                if (!$pieza->setId($_POST['idPieza'])) {
                    $result['error'] = 'Pieza incorrecta';
                } elseif ($result['dataset'] = $pieza->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Pieza inexistente';
                }
                break;

                // Acción para actualizar una pieza.
            case 'updateRow':
                // Validar y actualizar una pieza.
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
                    $result['message'] = 'Pieza modificada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la pieza';
                }
                break;

                // Acción para eliminar una pieza.
            case 'deleteRow':
                // Validar y eliminar una pieza.
                if (!$pieza->setId($_POST['idPieza'])) {
                    $result['error'] = $pieza->getDataError();
                } elseif ($pieza->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Pieza eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar la pieza';
                }
                break;

            case 'readPiezasCoches':
                if (!$pieza->setId($_POST['idPieza'])) {
                    $result['error'] = $pieza->getDataError();
                } elseif ($result['dataset'] = $pieza->readPiezasCoches()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No existen productos vendidos por el momento';
                }
                break;

            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el usuario no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readUsers':
                // Leer todos los usuarios si no hay sesión.
                if ($pieza->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['error'] = 'Debe crear una cuenta para comenzar';
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
    // Si no se proporciona una acción válida, se muestra un mensaje de recurso no disponible.
    print(json_encode('Recurso no disponible'));
}
