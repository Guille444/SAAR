<?php

/* 
Algunos estanderes de programacion en php
1. Los archivos php deben iniciar con la etiqueta "<?php"
2. El php debe estar escrito en el formato de codificacion UTF-8
3. Un archivo php debe cuidar si usará el archivo para generar conexiones con "require" o para determinar nuevas clases o funciones,
   pero nunca debera usar los 2 en el mismo archivo, el condicional no tiene efectos secundarios
4. Las clases deben estar escritas en StudlyCaps
5. Los metodos tienen que estar escritos en camelCase
*/

// Se incluye la clase del modelo.
require_once('../../models/data/administrador_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $administrador = new AdministradorData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $administrador->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setNombre($_POST['nombreAdministrador']) or
                    !$administrador->setApellido($_POST['apellidoAdministrador']) or
                    !$administrador->setAlias($_POST['aliasAdministrador']) or
                    !$administrador->setCorreo($_POST['correoAdministrador']) or
                    !$administrador->setRol($_POST['rolAdministrador']) or
                    !$administrador->setClave($_POST['claveAdministrador'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($_POST['claveAdministrador'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($administrador->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el administrador';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $administrador->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen administradores registrados';
                }
                break;
            case 'readOne':
                if (!$administrador->setId($_POST['idAdministrador'])) {
                    $result['error'] = 'Administrador incorrecto';
                } elseif ($result['dataset'] = $administrador->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Administrador inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setId($_POST['idAdministrador']) or
                    !$administrador->setNombre($_POST['nombreAdministrador']) or
                    !$administrador->setApellido($_POST['apellidoAdministrador']) or
                    !$administrador->setCorreo($_POST['correoAdministrador']) or
                    !$administrador->setRol($_POST['rolAdministrador'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el administrador';
                }
                break;
            case 'deleteRow':
                if ($_POST['idAdministrador'] == $_SESSION['idAdministrador']) {
                    $result['error'] = 'No se puede eliminar a sí mismo';
                } elseif (!$administrador->setId($_POST['idAdministrador'])) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el administrador';
                }
                break;
            case 'cantidadAdministradores':
                if ($result['dataset'] = $administrador->cantidadAdministradores()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cantidad de administradores obtenida correctamente';
                } else {
                    $result['error'] = 'No se pudo obtener la cantidad de administradores';
                }
                break;
            case 'getUser':
                unset($_SESSION['pasw']);
                if (isset($_SESSION['aliasAdministrador'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['aliasAdministrador'];
                } else {
                    $result['error'] = 'Alias de administrador indefinido';
                }
                break;
            case 'logOut':
                if (session_destroy()) {
                    $result['status'] = 1;
                    $result['message'] = 'Sesión eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cerrar la sesión';
                }
                break;
            case 'readProfile':
                if ($result['dataset'] = $administrador->readProfile()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Ocurrió un problema al leer el perfil';
                }
                break;
            case 'editProfile':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setId($_SESSION['idAdministrador']) ||  // Asegúrate de establecer el ID del cliente
                    !$administrador->setNombre($_POST['nombreAdministrador']) or
                    !$administrador->setApellido($_POST['apellidoAdministrador']) or
                    !$administrador->setCorreo($_POST['correoAdministrador']) or
                    !$administrador->setAlias($_POST['aliasAdministrador'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->editProfile()) {
                    $result['status'] = 1;
                    $result['message'] = 'Perfil modificado correctamente';
                    $_SESSION['aliasAdministrador'] = $_POST['aliasAdministrador'];
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el perfil';
                }
                break;
            case 'changePassword':
                $_POST = Validator::validateForm($_POST);
                // Verifica que la contraseña actual es correcta.
                if (!$administrador->checkPassword($_POST['claveActual'])) {
                    $result['error'] = 'Contraseña actual incorrecta';
                }
                // Verifica que la nueva contraseña sea diferente a la actual.
                elseif ($_POST['claveActual'] == $_POST['nuevaClave']) {
                    $result['error'] = 'La nueva contraseña no puede ser igual a la contraseña actual';
                }
                // Verifica que la confirmación de la contraseña coincida con la nueva contraseña.
                elseif ($_POST['nuevaClave'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Confirmación de contraseña diferente';
                }
                // Valida y establece la nueva contraseña.
                elseif (!$administrador->setClave($_POST['nuevaClave'])) {
                    $result['error'] = $administrador->getDataError();
                }
                // Intenta cambiar la contraseña en la base de datos.
                elseif ($administrador->changePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña cambiada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readUsers':
                if ($administrador->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['error'] = 'Debe crear un administrador para comenzar';
                }
                break;
            case 'signUp':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setNombre($_POST['nombreAdministrador']) or
                    !$administrador->setApellido($_POST['apellidoAdministrador']) or
                    !$administrador->setAlias($_POST['aliasAdministrador']) or
                    !$administrador->setCorreo($_POST['correoAdministrador']) or
                    !$administrador->setClave($_POST['claveAdministrador'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($_POST['claveAdministrador'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($administrador->readAll()) {
                    $result['error'] = 'Ya hay un administrador creado';
                } elseif ($administrador->signUp()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador registrado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar el administrador';
                }
                break;
            case 'logIn':
                $_POST = Validator::validateForm($_POST);

                // Llama a la función checkUser y captura la respuesta detallada
                $loginResult = $administrador->checkUser($_POST['alias'], $_POST['clave']);

                if ($loginResult['status']) {
                    // Verificar la última vez que se cambió la clave
                    $ultima_clave = new DateTime($_SESSION['ultimo_cambio']);
                    $fecha_actual = new DateTime();
                    $interval = $fecha_actual->diff($ultima_clave);

                    if ($interval->days > 90) {
                        // Si han pasado más de 90 días, solicitar cambio de clave
                        unset($_SESSION['idAdministrador']);
                        $result['dataset'] = 4; // Código para requerir cambio de contraseña
                        $result['message'] = 'Debe cambiar su contraseña cada 90 días.';
                    } else {
                        if (isset($_SESSION['2fa'])) {
                            unset($_SESSION['idAdministrador']);
                            $result['dataset'] = 5; // Código para indicar que se requiere 2FA
                            $result['message'] = 'Código enviado a su correo.';
                        } else {
                            // Inicio de sesión exitoso sin 2FA
                            $result['status'] = 1;
                            $result['dataset'] = 1; // Código para inicio de sesión exitoso
                            $result['message'] = $loginResult['message'];
                        }
                    }
                } else {
                    // Verificar si hay un error de bloqueo de cuenta o intentos fallidos
                    if (isset($loginResult['intentos'])) {
                        if ($loginResult['intentos'] >= 3) {
                            $result['dataset'] = 3; // Código para cuenta bloqueada
                            $result['message'] = 'Cuenta suspendida por 24 horas debido a múltiples intentos fallidos.';
                        } else {
                            $result['dataset'] = 2; // Código para credenciales incorrectas
                            $result['message'] = 'Credenciales incorrectas. Intento ' . $loginResult['intentos'] . ' de 3.';
                        }
                    } else {
                        $result['dataset'] = 2; // Código para credenciales incorrectas
                        $result['message'] = 'Credenciales incorrectas';
                    }
                }
                break;
            case 'verifUs':
                if (!$administrador->setAliasRecu($_POST['alias'])) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($result['dataset'] = $administrador->verifUs()) {
                    $result['status'] = 1;
                    $_SESSION['administradorRecu'] = $result['dataset']['id_administrador'];
                } else {
                    $result['error'] = 'Alias inexistente';
                }
                break;
            case 'verifPin':
                if (
                    !$administrador->setpinRecu($_POST['pinRecu']) or
                    !$administrador->setId($_SESSION['administradorRecu'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($result['dataset'] = $administrador->verifPin()) {
                    $result['status'] = 1;
                    //$_SESSION['clienteRecup'] = $result['dataset']['id_cliente'];
                } else {
                    $result['error'] = 'Código de recuperación incorrecto, verifque su correo electronico';
                }
                break;
                // Cambiar contraseña de ad$administrador.
            case 'changePasswordRecup':
                if (!$administrador->setId($_SESSION['administradorRecu'])) {
                    $result['error'] = 'Acción no disponible';
                } elseif ($_POST['nuevaClave'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif (!$administrador->setClave($_POST['nuevaClave'])) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->changePasswordRecup()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña modificada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
                }
                break;
            case 'get2FA':
                if (isset($_SESSION['2fa'])) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Accion no habilitada';
                }
                break;
            case 'verif2FA':
                if (!$administrador->setAliasRecu($_POST['alias'])) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($result['dataset'] = $administrador->verifUs()) {
                    $result['status'] = 1;
                    $_SESSION['administrador2FA'] = $result['dataset']['id_administrador'];
                } else {
                    $result['error'] = 'Alias inexistente';
                }
                break;
            case 'verifPin2FA':
                if (
                    !$administrador->setpinRecu($_POST['pinRecu']) or
                    !$administrador->setId($_SESSION['administrador2FA'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($result['dataset'] = $administrador->verifPin()) {
                    $result['status'] = 1;
                    $result['message'] = 'Inicio de sesión exitoso';
                    $_SESSION['idAdministrador'] = $result['dataset']['id_administrador'];
                } else {
                    $result['error'] = 'Código de seguridad incorrecto';
                }
                break;
            case 'getChange':
                if (isset($_SESSION['idChange'])) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Accion no habilitada';
                }
            case 'getRecup':
                if (isset($_SESSION['administradorRecu'])) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Accion no habilitada';
                }
                break;
            case 'newPassword':
                if (!$administrador->setId($_SESSION['idChange'])) {
                    $result['error'] = 'Acción no disponible';
                } elseif ($_SESSION['pasw'] == $_POST['nuevaClave']) {
                    $result['error'] = 'La clave nueva no puede ser igual a la actual';
                } elseif ($_POST['nuevaClave'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif (!$administrador->setClave($_POST['nuevaClave'])) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->changePasswordDays()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña modificada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
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
