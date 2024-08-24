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

// Se incluye la clase del modelo de marcas.
require_once('../../models/data/marcas_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente de marcas.
    $marca = new MarcaData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);

    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Acción para buscar registros de marcas.
            case 'searchRows':
                // Validar y ejecutar la búsqueda de registros de marcas.
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $marca->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;

                // Acción para crear una nueva marca.
            case 'createRow':
                // Validar y crear una nueva marca.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$marca->setNombre($_POST['nombreMarca'])
                ) {
                    $result['error'] = $marca->getDataError();
                } elseif ($marca->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Marca creada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear la marca';
                }
                break;

                // Acción para leer todas las marcas.
            case 'readAll':
                // Leer todas las marcas.
                if ($result['dataset'] = $marca->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen marcas registradas';
                }
                break;

                // Acción para leer una marca específica por su ID.
            case 'readOne':
                // Validar y leer una marca específica por su ID.
                if (!$marca->setId($_POST['idMarca'])) {
                    $result['error'] = $marca->getDataError();
                } elseif ($result['dataset'] = $marca->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Marca inexistente';
                }
                break;

                // Acción para actualizar una marca.
            case 'updateRow':
                // Validar y actualizar una marca.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$marca->setId($_POST['idMarca']) or
                    !$marca->setNombre($_POST['nombreMarca'])
                ) {
                    $result['error'] = $marca->getDataError();
                } elseif ($marca->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Marca modificada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la marca';
                }
                break;

                // Acción para eliminar una marca.
            case 'deleteRow':
                // Validar y eliminar una marca.
                if (
                    !$marca->setId($_POST['idMarca'])
                ) {
                    $result['error'] = $marca->getDataError();
                } elseif ($marca->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Marca eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar la marca';
                }
                break;
            
            case 'TopVehiculosPorMarcas':
                if ($result['dataset'] = $marca->TopVehiculosPorMarcas()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cantidad de administradores obtenida correctamente';
                } else {
                    $result['error'] = 'No se pudo obtener la cantidad de administradores';
                }
                break;

            case 'readMarcasModelos':
                if (!$marca->setId($_POST['idMarca'])) {
                    $result['error'] = $marca->getDataError();
                } elseif ($result['dataset'] = $marca->readMarcasModelos()) {
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
        // Si no hay sesión de administrador, se deniega el acceso.
        print(json_encode('Acceso denegado'));
    }
} else {
    // Si no se proporciona una acción válida, se muestra un mensaje de recurso no disponible.
    print(json_encode('Recurso no disponible'));
}
