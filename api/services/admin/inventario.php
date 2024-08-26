<?php

/* 
Algunos estanderes de programacion en php
1. Los archivos php deben iniciar con la etiqueta "<?php"
2. El php debe estar escrito en el formato de codificacion UTF-8
3. Un archivo php debe cuidar si usará el archivo para generar conexiones con "require" o para determinar nuevas clases o funciones,
   pero nunca debera usar los 2 en el mismo archivo, usando el condicional no hay problemas
4. Las clases deben estar escritas en StudlyCaps
5. Los metodos tienen que estar escritos en camelCase
*/

// Se incluye la clase del modelo de inventario.
require_once('../../models/data/inventario_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente de inventario.
    $inventario = new InventarioData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);

    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Acción para buscar registros en el inventario.
            case 'searchRows':
                // Validar y ejecutar la búsqueda de registros en el inventario.
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $inventario->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;

                // Acción para crear un nuevo registro en el inventario.
            case 'createRow':
                // Validar y crear un nuevo registro en el inventario.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$inventario->setPieza($_POST['nombrePieza']) or
                    !$inventario->setCantidadDisponible($_POST['cantidadInventario']) or
                    !$inventario->setProveedor($_POST['proveedorInventario']) or
                    !$inventario->setFechaIngreso($_POST['fechaInventario'])
                ) {
                    $result['error'] = $inventario->getDataError();
                } elseif ($inventario->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Registro creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el registro en el inventario';
                }
                break;

                // Acción para leer todos los registros del inventario.
            case 'readAll':
                // Leer todos los registros del inventario.
                if ($result['dataset'] = $inventario->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen registros en el inventario';
                }
                break;

                // Acción para leer un registro específico del inventario por su ID.
            case 'readOne':
                // Validar y leer un registro específíco del inventario por su ID.
                if (!$inventario->setIdInventario($_POST['idInventario'])) {
                    $result['error'] = $inventario->getDataError();
                } elseif ($result['dataset'] = $inventario->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Registro inexistente';
                }
                break;

                // Acción para actualizar un registro del inventario.
            case 'updateRow':
                // Validar y actualizar un registro del inventario.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$inventario->setIdInventario($_POST['idInventario']) or
                    !$inventario->setPieza($_POST['nombrePieza']) or
                    !$inventario->setCantidadDisponible($_POST['cantidadInventario']) or
                    !$inventario->setProveedor($_POST['proveedorInventario']) or
                    !$inventario->setFechaIngreso($_POST['fechaInventario'])
                ) {
                    $result['error'] = $inventario->getDataError();
                } elseif ($inventario->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Registro modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el registro en el inventario';
                }
                break;

                // Acción para eliminar un registro del inventario.
            case 'deleteRow':
                // Validar y eliminar un registro del inventario.
                if (!$inventario->setIdInventario($_POST['idInventario'])) {
                    $result['error'] = $inventario->getDataError();
                } elseif ($inventario->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Registro eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el registro del inventario';
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
