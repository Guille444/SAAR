<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class AdministradorHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $alias = null;
    protected $clave = null;
    protected $rol = null;
    protected $pin = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_administrador, id_rol, alias_administrador, clave_administrador, correo_administrador, 
            intentos_usuario, fecha_reactivacion, ultimo_intento, ultimo_cambio_clave, factor_autenticacion
            FROM administradores
            INNER JOIN roles USING (id_rol)
            WHERE alias_administrador = ? AND estado_administrador = true'; // Asegúrate de que 'estado_administrador' es una columna válida en tu tabla de administradores

        $params = array($username);
        $data = Database::getRow($sql, $params);

        if ($data) {
            $intentos = $data['intentos_usuario'];
            $ultimo_intento = $data['ultimo_intento'];
            $fecha_reactivacion = $data['fecha_reactivacion'];

            // Comprobar si el usuario está bloqueado
            if ($intentos >= 3 && $fecha_reactivacion && strtotime($fecha_reactivacion) > time()) {
                return [
                    'status' => false,
                    'message' => "Cuenta bloqueada por 24 horas debido a múltiples intentos fallidos.",
                    'intentos' => $intentos
                ];
            }

            // Verificar si han pasado más de 10 minutos desde el último intento fallido
            if ($ultimo_intento) {
                $now = new DateTime();
                $lastAttempt = new DateTime($ultimo_intento);
                $interval = $now->diff($lastAttempt);

                if ($interval->i >= 10) {
                    // Reiniciar contador de intentos si han pasado más de 10 minutos
                    $intentos = 0;
                    $this->reiniciarIntentos($data['id_administrador']);
                }
            }

            if (password_verify($password, $data['clave_administrador'])) {
                // Restablecer el contador de intentos en caso de inicio de sesión exitoso
                $this->reiniciarIntentos($data['id_administrador']);

                // Establecer las variables de sesión
                $_SESSION['idAdministrador'] = $data['id_administrador'];
                $_SESSION['usuarioEmpleado'] = $data['alias_administrador'];
                $_SESSION['idChange'] = $data['id_administrador'];
                $_SESSION['aliasAdministrador'] = $data['alias_administrador'];
                $_SESSION['pasw'] = $password;
                $_SESSION['ultimo_cambio'] = $data['ultimo_cambio_clave'];
                $_SESSION['alias'] = $data['alias_administrador'];
                $_SESSION['correo'] = $data['correo_administrador'];
                $_SESSION['idRol'] = $data['id_rol'];

                if ($data['factor_autenticacion']) {
                    $_SESSION['2fa'] = $data['id_administrador'];
                }

                return ['status' => true, 'message' => "Credenciales correctas"];
            } else {
                // Incrementar el contador de intentos fallidos
                $this->incrementarIntentos($data['id_administrador']);

                // Verificar si ahora el usuario tiene 3 intentos fallidos para bloquear la cuenta
                if ($intentos + 1 >= 3) {
                    $this->blockUser($data['id_administrador']);
                    return [
                        'status' => false,
                        'message' => "Cuenta bloqueada por 24 horas debido a múltiples intentos fallidos.",
                        'intentos' => $intentos + 1
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => "Credenciales incorrectas. Intento " . ($intentos + 1) . " de 3. Se reinician cada 10 minutos",
                        'intentos' => $intentos + 1,
                    ];
                }
            }
        } else {
            return ['status' => false, 'message' => "Usuario no encontrado", 'intentos' => 0];
        }
    }

    private function reiniciarIntentos($id_administrador)
    {
        $sql = 'UPDATE administradores SET intentos_usuario = 0, ultimo_intento = NULL, fecha_reactivacion = NULL WHERE id_administrador = ?';
        $params = array($id_administrador);
        return Database::executeRow($sql, $params);
    }

    private function incrementarIntentos($id_administrador)
    {
        $sql = 'UPDATE administradores SET intentos_usuario = intentos_usuario + 1, ultimo_intento = CURRENT_TIMESTAMP WHERE id_administrador = ?';
        $params = array($id_administrador);
        return Database::executeRow($sql, $params);
    }

    private function blockUser($id_administrador)
    {
        // Bloquear la cuenta por 24 horas
        $sql = 'UPDATE administradores SET fecha_reactivacion = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 1 DAY) WHERE id_administrador = ?';
        $params = array($id_administrador);
        return Database::executeRow($sql, $params);
    }


    public function checkPassword($password)
    {
        $sql = 'SELECT clave_administrador
                FROM administradores
                WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        $data = Database::getRow($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($password, $data['clave_administrador'])) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE administradores
                SET clave_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    public function changePasswordDays()
    {
        $sql = 'UPDATE administradores
                SET clave_administrador = ?, ultimo_cambio_clave = now()
                WHERE id_administrador = ?';
        $params = array($this->clave, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, correo_administrador, alias_administrador
                FROM administradores
                WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE administradores
                SET nombre_administrador = ?, apellido_administrador = ?, correo_administrador = ?, alias_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->alias, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, correo_administrador, alias_administrador
                FROM administradores
                WHERE apellido_administrador LIKE ?
                ORDER BY apellido_administrador';
        $params = array($value);
        return Database::getRows($sql, $params);
    }
    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     *  Metodo createRow permite crear nas usuarios o administradores
     */
    public function createRow()
    {
        $sql = 'INSERT INTO administradores(nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, clave_administrador, id_rol, codigo_recuperacion)
                VALUES(?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->correo, $this->clave, $this->rol, $this->generarPin());
        return Database::executeRow($sql, $params);
    }

    public function signUp()
    {
        $sql = 'INSERT INTO administradores(nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, clave_administrador, id_rol, codigo_recuperacion)
                VALUES(?, ?, ?, ?, ?, 1, ?)';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->correo, $this->clave, $this->generarPin());
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, nombre_rol
                FROM administradores
                INNER JOIN roles USING(id_rol)
                ORDER BY apellido_administrador';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, id_rol
                FROM administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE administradores
                SET nombre_administrador = ?, apellido_administrador = ?, correo_administrador = ?, id_rol = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->rol, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate($value, $idAdministrador = null)
    {
        if ($idAdministrador) {
            $sql = 'SELECT id_administrador
                FROM administradores
                WHERE correo_administrador = ? AND id_administrador != ?';
            $params = array($value, $idAdministrador);
        } else {
            $sql = 'SELECT id_administrador
                FROM administradores
                WHERE correo_administrador = ?';
            $params = array($value);
        }
        return Database::getRow($sql, $params);
    }

    public function checkDuplicate2($value, $idAdministrador = null)
    {
        if ($idAdministrador) {
            $sql = 'SELECT id_administrador
                FROM administradores
                WHERE alias_administrador = ? AND id_administrador != ?';
            $params = array($value, $idAdministrador);
        } else {
            $sql = 'SELECT id_administrador
                FROM administradores
                WHERE alias_administrador = ?';
            $params = array($value);
        }
        return Database::getRow($sql, $params);
    }

    /*
 * Método para obtener la cantidad total de administradores registrados.
 */
    public function cantidadAdministradores()
    {
        $sql = 'SELECT COUNT(id_administrador) cantidad, nombre_rol 
                FROM administradores
                INNER JOIN roles USING (id_rol)
                GROUP BY nombre_rol;';
        return Database::getRows($sql);
    }

    /*
 * Método para obtener información básica de los administradores.
 */
    public function obtenerAdministradores()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, fecha_registro
            FROM administradores
            ORDER BY fecha_registro DESC';
        return Database::getRows($sql);
    }

    public function readAdministradores()
    {
        $sql = 'SELECT nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, nombre_rol 
                FROM administradores 
                JOIN roles ON administradores.id_rol = roles.id_rol 
                ORDER BY apellido_administrador, nombre_administrador';
        return Database::getRows($sql);
    }

    public function administradoresPorRol()
    {
        $sql = 'SELECT nombre_administrador, apellido_administrador, alias_administrador, correo_administrador
            FROM administradores
            WHERE id_rol = ?
            ORDER BY nombre_administrador';
        $params = array($this->rol);
        return Database::getRows($sql, $params);
    }

    // GENERAR PIN
    public function generarPin()
    {
        $pinLength = 6;
        $pin = '';

        for ($i = 0; $i < $pinLength; $i++) {
            $pin .= mt_rand(0, 9);
        }

        return $pin;
    }

    //Funcion para verificar si el usuaro existe
    public function verifUs()
    {
        // Verificamos si existe el usuario
        $sql = 'SELECT * FROM administradores WHERE alias_administrador = ?';
        $params = array($this->alias);
        $data = Database::getRow($sql, $params);

        // Si se encuentra el usuario
        if ($data) {
            // Generamos un nuevo PIN y lo actualizamos
            $pin = $this->generarPin();
            $sql = 'UPDATE administradores SET codigo_recuperacion = ? WHERE alias_administrador = ?';
            $params = array($pin, $this->alias);
            $updateSuccess = Database::executeRow($sql, $params);

            // Si la actualización fue exitosa
            if ($updateSuccess) {
                // Agregamos el nuevo PIN a los datos del usuario y lo retornamos
                $data['codigo_recuperacion'] = $pin;
                return $data;
            } else {
                // Si no se pudo actualizar el PIN
                return null;
            }
        } else {
            // Si no se encontró el usuario
            return null;
        }
    }

    public function verifPin()
    {
        $sql = 'SELECT * FROM administradores
        WHERE codigo_recuperacion = ? AND id_administrador = ?';
        $params = array($this->pin, $this->id);
        return Database::getRow($sql, $params);
    }

    // Método para cambiar la contraseña del usuario.
    public function changePasswordRecup()
    {
        $sql = 'UPDATE administradores
                SET clave_administrador = ?, codigo_recuperacion = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $this->generarPin(), $this->id);
        return Database::executeRow($sql, $params);
    }
}
