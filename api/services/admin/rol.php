<?php
// Se incluye la clase del modelo de roles.
require_once('../../models/data/roles_data.php');
// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente de roles.
    $roles = new RolesData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Acción para buscar registros de roles.
            case 'searchRows':
                // Validar y ejecutar la búsqueda de registros de roles.
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $roles->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
                // Acción para crear un rol.
            case 'createRow':
                // Validar y crear una rol.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$roles->setNombre($_POST['nombreRol'])
                ) {
                    $result['error'] = $roles->getDataError();
                } elseif ($roles->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Rol creada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear la roles';
                }
                break;

                // Acción para leer todas las roles.
            case 'readAll':
                // Leer todas las roles.
                if ($result['dataset'] = $roles->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen roles registradas';
                }
                break;

                // Acción para leer una roles específica por su ID.
            case 'readOne':
                // Validar y leer una roles específica por su ID.
                if (!$roles->setId($_POST['idRol'])) {
                    $result['error'] = $roles->getDataError();
                } elseif ($result['dataset'] = $roles->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Rol inexistente';
                }
                break;

                // Acción para actualizar una roles.
            case 'updateRow':
                // Validar y actualizar una roles.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$roles->setId($_POST['idRol']) or
                    !$roles->setNombre($_POST['nombreRol'])
                ) {
                    $result['error'] = $roles->getDataError();
                } elseif ($roles->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Rol modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la roles';
                }
                break;

                // Acción para eliminar una roles.
            case 'deleteRow':
                // Validar y eliminar una roles.
                if (
                    !$roles->setId($_POST['idRol'])
                ) {
                    $result['error'] = $roles->getDataError();
                } elseif ($roles->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Rol eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar la roles';
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