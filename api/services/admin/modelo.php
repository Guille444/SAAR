<?php
// Se incluye la clase del modelo.
require_once('../../models/data/modelos_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $modelo = new ModeloData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);

    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Acción para buscar registros de modelos.
            case 'searchRows':
                // Validar y ejecutar la búsqueda de registros de modelos.
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $modelo->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;

                // Acción para crear un nuevo modelo.
            case 'createRow':
                // Validar y crear un nuevo modelo.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$modelo->setNombre($_POST['nombreModelo']) or
                    !$modelo->setMarca($_POST['marcaModelo'])
                ) {
                    $result['error'] = $modelo->getDataError();
                } elseif ($modelo->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Modelo creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el modelo';
                }
                break;
                // Acción para leer todos los modelos.
            case 'readAll':
                // Leer todos los modelos.
                if ($result['dataset'] = $modelo->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen modelos registrados';
                }
                break;
                // Acción para leer un modelo específico por su ID.
            case 'readOne':
                // Validar y leer un modelo específico por su ID.
                if (!$modelo->setId($_POST['idModelo'])) {
                    $result['error'] = $modelo->getDataError();
                } elseif ($result['dataset'] = $modelo->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Modelo inexistente';
                }
                break;

                // Acción para actualizar un modelo.
            case 'updateRow':
                // Validar y actualizar un modelo.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$modelo->setId($_POST['idModelo']) or
                    !$modelo->setNombre($_POST['nombreModelo']) or
                    !$modelo->setMarca($_POST['marcaModelo'])
                ) {
                    $result['error'] = $modelo->getDataError();
                } elseif ($modelo->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Modelo modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el modelo';
                }
                break;

                // Acción para eliminar un modelo.
            case 'deleteRow':
                // Validar y eliminar un modelo.
                if (
                    !$modelo->setId($_POST['idModelo'])
                ) {
                    $result['error'] = $modelo->getDataError();
                } elseif ($modelo->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Modelo eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el modelo';
                }
                break;
            
            case 'TopVehiculosPorModelos':
                if ($result['dataset'] = $modelo->TopVehiculosPorModelos()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cantidad de administradores obtenida correctamente';
                } else {
                    $result['error'] = 'No se pudo obtener la cantidad de administradores';
                }
                break;

            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }

        // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
        $result['exception'] = Database::getException();

        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('Content-type: application/json; charset=utf-8');

        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        // Si no hay sesión de administrador, se deniega el acceso.
        print(json_encode('Acceso denegado'));
    }
} else {
    // Si no se proporciona una acción válida, se muestra un mensaje de recurso no disponible.
    print(json_encode('Recurso no disponible'));
}
