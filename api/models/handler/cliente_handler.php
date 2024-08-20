<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla CLIENTE.
*/
class ClienteHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $alias = null;
    protected $correo = null;
    protected $clave = null;
    protected $contacto = null;
    protected $estado = null;

    /*
    *   Métodos para gestionar la cuenta del cliente.
    */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_cliente, alias_cliente, clave_cliente, estado_cliente
            FROM clientes
            WHERE alias_cliente = ?';
        $params = array($username);
        $data = Database::getRow($sql, $params);
        // Verificar si $data no es false y es un array antes de acceder a sus índices
        if ($data && is_array($data) && password_verify($password, $data['clave_cliente'])) {
            $this->id = $data['id_cliente'];
            $this->alias = $data['alias_cliente'];
            $this->estado = $data['estado_cliente'];
            return true;
        } else {
            return false;
        }
    }

    public function checkStatus()
    {
        if ($this->estado) {
            $_SESSION['idCliente'] = $this->id;
            $_SESSION['aliasCliente'] = $this->alias;
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT clave_cliente
                FROM clientes
                WHERE id_cliente = ?';
        $params = array($_SESSION['idCliente']);
        $data = Database::getRow($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($password, $data['clave_cliente'])) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE clientes
                SET clave_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->clave, $_SESSION['idCliente']);
        return Database::executeRow($sql, $params);
    }

    public function changeStatus()
    {
        $sql = 'UPDATE clientes
                SET estado_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, contacto_cliente
                FROM clientes
                WHERE apellido_cliente LIKE ?
                ORDER BY apellido_cliente';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO clientes(nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, clave_cliente, contacto_cliente)
                VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->correo, $this->clave, $this->contacto);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, contacto_cliente, estado_cliente
                FROM clientes
                ORDER BY apellido_cliente';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, contacto_cliente, estado_cliente
                FROM clientes
                WHERE id_cliente = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE clientes
                SET estado_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM clientes
                WHERE id_cliente = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, alias_cliente, contacto_cliente, correo_cliente, clave_cliente
                FROM clientes
                WHERE id_cliente = ?';
        $params = array($_SESSION['idCliente']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE clientes
                SET nombre_cliente = ?, apellido_cliente = ?, alias_cliente = ?, contacto_cliente = ?, correo_cliente = ?  
                WHERE id_cliente = ?';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->contacto, $this->correo, $_SESSION['idCliente']);
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate($value, $idCliente = null)
    {
        if ($idCliente) {
            $sql = 'SELECT id_cliente
                FROM clientes
                WHERE correo_cliente = ? AND id_cliente != ?';
            $params = array($value, $idCliente);
        } else {
            $sql = 'SELECT id_cliente
                FROM clientes
                WHERE correo_cliente = ?';
            $params = array($value);
        }
        return Database::getRow($sql, $params);
    }

    public function checkDuplicate2($value, $idCliente = null)
    {
        if ($idCliente) {
            $sql = 'SELECT id_cliente
                FROM clientes
                WHERE alias_cliente = ? AND id_cliente != ?';
            $params = array($value, $idCliente);
        } else {
            $sql = 'SELECT id_cliente
                FROM clientes
                WHERE alias_cliente = ?';
            $params = array($value);
        }
        return Database::getRow($sql, $params);
    }

    public function readPiezaCliente()
    {
        $sql = 'SELECT nombre_pieza, cantidad
                FROM clientes, piezas, detalle_citas, citas
                WHERE clientes.id_cliente = citas.id_cliente AND
                citas.id_cita = detalle_citas.id_cita and
                piezas.id_pieza = detalle_citas.id_pieza and
                clientes.id_cliente = ?
                GROUP BY nombre_pieza;';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }
}
