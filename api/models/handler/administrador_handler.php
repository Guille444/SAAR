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

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_administrador, alias_administrador, clave_administrador
                FROM administradores
                WHERE  alias_administrador = ?';
        $params = array($username);
        $data = Database::getRow($sql, $params);
        if (password_verify($password, $data['clave_administrador'])) {
            $_SESSION['idAdministrador'] = $data['id_administrador'];
            $_SESSION['aliasAdministrador'] = $data['alias_administrador'];
            return true;
        } else {
            return false;
        }
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

    public function createRow()
    {
        $sql = 'INSERT INTO administradores(nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, clave_administrador, id_rol)
                VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->correo, $this->clave, $this->rol);
        return Database::executeRow($sql, $params);
    }

    public function signUp()
    {
        $sql = 'INSERT INTO administradores(nombre_administrador, apellido_administrador, alias_administrador, correo_administrador, clave_administrador, id_rol)
                VALUES(?, ?, ?, ?, ?, 1)';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->correo, $this->clave);
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

    public function TopVehiculosPorMarcas()
    {
        $sql = 'SELECT COUNT(id_vehiculo) cantidad, marca_vehiculo
                FROM vehiculos
                INNER JOIN marcas USING (id_marca)
                GROUP BY marca_vehiculo
                ORDER BY cantidad desc
                LIMIT 3;';
        return Database::getRows($sql);
    }

    public function TopVehiculosPorModelos()
    {
        $sql = 'SELECT COUNT(id_vehiculo) cantidad, modelo_vehiculo
                FROM vehiculos
                INNER JOIN modelos USING (id_modelo)
                GROUP BY modelo_vehiculo
                ORDER BY cantidad desc
                LIMIT 3;';
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
}
