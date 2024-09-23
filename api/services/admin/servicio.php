<?php

/* 
Algunos estanderes de programacion en php
1. Los archivos php deben iniciar con la etiqueta "<?php"
2. El php debe estar escrito en el formato de codificacion UTF-8.
3. Un archivo php debe cuidar si usará el archivo para generar conexiones con "require" o para determinar nuevas clases o funciones,
   pero nunca debera usar los 2 en el mismo archivo, usando el condicional no hay problemas.
4. Las clases deben estar escritas en StudlyCaps
5. Los metodos tienen que estar escritos en camelCase 
6. "camelCase", "StudyClap", "snake_case"
*/

// Se incluye la clase del modelo.
require_once('../../models/data/servicio_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $servicio = new ServicioData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);

    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Acción para buscar registros de servicios.
            case 'searchRows':
                // Validar y ejecutar la búsqueda de registros de servicios.
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $servicio->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;

                // Acción para crear un nuevo servicio.
            case 'createRow':
                // Validar y crear un nuevo servicio.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$servicio->setNombre($_POST['NombreServicio']) or
                    !$servicio->setDescripcion($_POST['DescripcionServicio'])
                ) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($servicio->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Servicio creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el servicio';
                }
                break;

                // Acción para leer todos los servicios.
            case 'readAll':
                // Leer todos los servicios.
                if ($result['dataset'] = $servicio->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen servicios registrados';
                }
                break;

                // Acción para leer un servicio específico por su ID.
            case 'readOne':
                // Validar y leer un servicio específico por su ID.
                if (!$servicio->setId($_POST['idServicio'])) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($result['dataset'] = $servicio->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Servicio inexistente';
                }
                break;

                // Acción para actualizar un servicio.
            case 'updateRow':
                // Validar y actualizar un servicio.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$servicio->setId($_POST['idServicio']) or
                    !$servicio->setNombre($_POST['NombreServicio']) or
                    !$servicio->setDescripcion($_POST['DescripcionServicio'])
                ) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($servicio->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Servicio modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el servicio';
                }
                break;

                // Acción para eliminar un servicio.
            case 'deleteRow':
                // Validar y eliminar un servicio.
                if (!$servicio->setId($_POST['idServicio'])) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($servicio->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Servicio eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el servicio';
                }
                break;
            case 'readServiciosMarcas':
                if (!$servicio->setId($_POST['idServicio'])) {
                    $result['error'] = $servicio->getDataError();
                } elseif ($result['dataset'] = $servicio->readServiciosMarcas()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No existen productos vendidos por el momento';
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
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}
