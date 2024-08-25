<?php
// Incluye las clases de datos para vehículos, marcas y modelos.
require_once('../../models/data/vehiculo_data.php');
// Verifica si se ha especificado una acción a realizar.
if (isset($_GET['action'])) {
    // Inicia la sesión.
    session_start();
    // Crea instancias de las clases de datos.
    $vehiculo = new VehiculoData;
    // Inicializa un array para almacenar los resultados.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'error' => null, 'exception' => null);
    // Verifica si el usuario ha iniciado sesión.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1; // Indica que la sesión está activa.
        // Leer el cuerpo de la solicitud JSON
        $data = json_decode(file_get_contents('php://input'), true);
        // Procesa la acción solicitada.
        switch ($_GET['action']) {
                // Acción para buscar vehículos según un criterio.
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError(); // Error de validación.
                } elseif ($result['dataset'] = $vehiculo->searchRows()) {
                    $result['status'] = 1; // Éxito en la búsqueda.
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias'; // No se encontraron resultados.
                }
                break;
                // Acción para crear un nuevo vehículo.
            case 'createRow':
                // Validar y crear un nuevo vehiculo.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$vehiculo->setIdMarca($_POST['marcaVehiculo']) or
                    !$vehiculo->setIdModelo($_POST['modeloVehiculo']) or
                    !$vehiculo->setAño($_POST['añoVehiculo']) or
                    !$vehiculo->setPlaca($_POST['placaVehiculo']) or
                    !$vehiculo->setColor($_POST['colorVehiculo']) or
                    !$vehiculo->setVin($_POST['vinVehiculo'])
                ) {
                    $result['error'] = $vehiculo->getDataError();
                } elseif ($vehiculo->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Vehiculo registrado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el modelo';
                }
                break;
                // Acción para leer todos los vehículos.
            case 'readAll':
                if ($result['dataset'] = $vehiculo->readAll()) {
                    $result['status'] = 1; // Éxito en la lectura.
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' vehículos registrados';
                } else {
                    $result['error'] = 'No existen vehículos registrados'; // No hay vehículos.
                }
                break;
                // Acción para leer un vehículo específico por ID.
            case 'readOne':
                if (!$vehiculo->setId($_POST['id_vehiculo'])) {
                    $result['error'] = 'Vehículo incorrecto'; // ID de vehículo incorrecto.
                } elseif ($result['dataset'] = $vehiculo->readOne()) {
                    $result['status'] = 1; // Éxito en la lectura del vehículo.
                } else {
                    $result['error'] = 'Vehículo inexistente'; // Vehículo no encontrado.
                }
                break;
                // Acción para actualizar un vehículo existente.
            case 'updateRow':
                $_POST = Validator::validateForm($_POST); // Valida los datos del formulario.
                if (
                    !$vehiculo->setId($_POST['id_vehiculo']) or
                    !$vehiculo->setIdCliente($_POST['id_cliente']) or
                    !$vehiculo->setIdMarca($_POST['marcaVehiculo']) or
                    !$vehiculo->setIdModelo($_POST['modeloVehiculo']) or
                    !$vehiculo->setPlaca($_POST['placa']) or
                    !$vehiculo->setColor($_POST['color']) or
                    !$vehiculo->setVin($_POST['vin']) or
                    !$vehiculo->setAño($_POST['año'])
                ) {
                    $result['error'] = 'Datos incorrectos'; // Error en los datos del formulario.
                } elseif ($vehiculo->updateRow()) {
                    $result['status'] = 1; // Éxito en la actualización.
                    $result['message'] = 'Vehículo actualizado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al actualizar el vehículo'; // Error en la actualización.
                }
                break;
                // Acción para eliminar un vehículo.
            case 'deleteRow':
                if (!$vehiculo->setId($_POST['idVehiculo'])) {
                    $result['error'] = 'Vehículo incorrecto'; // ID de vehículo incorrecto.
                } elseif ($vehiculo->deleteRow()) {
                    $result['status'] = 1; // Éxito en la eliminación.
                    $result['message'] = 'Vehículo eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el vehículo'; // Error en la eliminación.
                }
                break;
                // Acción para obtener todas las marcas.
            case 'getMarcas':
                $marcas = $vehiculo->getAllMarcas();
                if ($marcas) {
                    $result['status'] = 1; // Éxito en la obtención de marcas.
                    $result['message'] = 'Marcas obtenidas correctamente';
                    $result['dataset'] = $marcas;
                } else {
                    $result['status'] = 0;
                    $result['message'] = 'No hay marcas disponibles'; // Mensaje de no hay marcas.
                    $result['dataset'] = []; // Asegúrate de que el dataset sea un array vacío.
                }
                break;
                // Acción para obtener todos los modelos.
            case 'getModelos':
                if ($result['dataset'] = $vehiculo->getAllModelos()) {
                    $result['status'] = 1; // Éxito en la obtención de modelos.
                    $result['message'] = 'Modelos obtenidos correctamente';
                } else {
                    $result['error'] = 'No se encontraron modelos'; // No se encontraron modelos.
                }
                break;
                // Acción para obtener modelos por marca
            case 'getModelosByMarca':
                if (!isset($_POST['marcaVehiculo'])) { // Cambiar de $_GET a $_POST
                    $result['error'] = 'Marca no especificada';
                } elseif ($result['dataset'] = $vehiculo->getModelosByMarca($_POST['marcaVehiculo'])) { // Cambiar de $_GET a $_POST
                    $result['status'] = 1;
                    $result['message'] = 'Modelos obtenidos correctamente';
                } else {
                    $result['error'] = 'No se encontraron modelos';
                }
                break;
            case 'readAllByClient':
                if ($result['dataset'] = $vehiculo->readAllByClient()) {
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' vehículos registrados.';
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No existen vehículos registrados.';
                }
                break;
            case 'searchRowsVehiculos':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError(); // Error de validación.
                } elseif ($result['dataset'] = $vehiculo->searchRowsVehiculos()) {
                    $result['status'] = 1; // Éxito en la búsqueda.
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias'; // No se encontraron resultados.
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión'; // Acción no permitida.
        }
        // Captura cualquier excepción en la base de datos.
        $result['exception'] = Database::getException();
        // Define el tipo de contenido como JSON.
        header('Content-type: application/json; charset=utf-8');
        // Envía la respuesta en formato JSON.
        print(json_encode($result));
    } else {
        $result['error'] = 'Debe iniciar sesión para realizar esta acción'; // El usuario no ha iniciado sesión.
    }
} else {
    // Respuesta en caso de que no se especifique ninguna acción.
    print(json_encode('Recurso no disponible'));
}
