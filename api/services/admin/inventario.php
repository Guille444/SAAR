<?php
// Se incluye la clase del modelo de inventario.
require_once('../../Models/data/inventario_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se instancia la clase correspondiente.
    $inventario = new InventarioData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'essage' => null, 'dataset' => null, 'error' => null, 'exception' => null);
    // Se compara la acción a realizar según la petición del controlador.
    switch ($_GET['action']) {
        case 'earchRows':
            if (!Validator::validateSearch($_POST['search'])) {
                $result['error'] = Validator::getSearchError();
            } elseif ($result['dataset'] = $inventario->searchRows()) {
                $result['status'] = 1;
                $result['message'] = 'Existen '. count($result['dataset']). 'oincidencias';
            } else {
                $result['error'] = 'No hay coincidencias';
            }
            break;
        case 'eadAll':
            if ($result['dataset'] = $inventario->readAll()) {
                $result['status'] = 1;
            } else {
                $result['error'] = 'No existen inventarios para mostrar';
            }
            break;
        case 'createRow':
            $_POST = Validator::validateForm($_POST);
            if (
               !$inventario->setIdPieza($_POST['idPieza']) or
               !$inventario->setCantidadDisponible($_POST['cantidadDisponible']) or
               !$inventario->setProveedor($_POST['proveedor']) or
               !$inventario->setFechaIngreso($_POST['fechaIngreso'])
            ) {
                $result['error'] = $inventario->getDataError();
            } elseif ($inventario->createRow()) {
                $result['status'] = 1;
                $result['message'] = 'Inventario creado correctamente';
            } else {
                $result['error'] = 'Ocurrió un problema al crear el inventario';
            }
            break;
        case 'updateRow':
            $_POST = Validator::validateForm($_POST);
            if (
               !$inventario->setIdInventario($_POST['idInventario']) or
               !$inventario->setIdPieza($_POST['idPieza']) or
               !$inventario->setCantidadDisponible($_POST['cantidadDisponible']) or
               !$inventario->setProveedor($_POST['proveedor']) or
               !$inventario->setFechaIngreso($_POST['fechaIngreso'])
            ) {
                $result['error'] = $inventario->getDataError();
            } else {
                if ($inventario->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Inventario modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el inventario';
                }
            }
            break;
        case 'eadOne':
            if (!$inventario->setIdInventario($_POST['idInventario'])) {
                $result['error'] = 'Inventario incorrecto';
            } elseif ($result['dataset'] = $inventario->readOne()) {
                $result['status'] = 1;
            } else {
                $result['error'] = 'Inventario inexistente';
            }
            break;
        case 'deleteRow':
            if (!$inventario->setIdInventario($_POST['idInventario'])) {
                $result['error'] = $inventario->getDataError();
            } else {
                if ($inventario->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Inventario eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el inventario';
                }
            }
            break;
        default:
            $result['error'] = 'Acción no disponible';
    }
    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print (json_encode($result));
} else{
    print (json_encode('Recurso no disponible'));
}