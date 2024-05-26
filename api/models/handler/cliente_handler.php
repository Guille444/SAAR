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
    public function checkUser($mail, $password)
    {
        $sql = 'SELECT id_cliente, correo_cliente, clave_cliente, estado_cliente
                FROM clientes
                WHERE correo_cliente = ?';
        $params = array($mail);
        $data = Database::getRow($sql, $params);
        if (password_verify($password, $data['clave_cliente'])) {
            $this->id = $data['id_cliente'];
            $this->correo = $data['correo_cliente'];
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
            $_SESSION['correoCliente'] = $this->correo;
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
        $params = array($this->clave, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE clientes
                SET nombre_cliente = ?, apellido_cliente = ?, alias_cliente = ?, contacto_cliente = ?, correo_cliente = ?  
                WHERE id_cliente = ?';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->contacto, $this->correo, $this->id);
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
                WHERE apellido_cliente LIKE ? OR nombre_cliente LIKE ? OR alias_cliente LIKE ?
                ORDER BY apellido_cliente';
        $params = array($value, $value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO clientes(nombre_cliente, apellido_cliente, alias_cliente, correo_cliente, clave_cliente, contacto_cliente)
                VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->alias, $this->correo, $this->clave, $this->contacto,);
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

    public function checkDuplicate($value)
    {
        $sql = 'SELECT id_cliente
                FROM clientes
                WHERE dui_cliente = ? OR correo_cliente = ?';
        $params = array($value, $value);
        return Database::getRow($sql, $params);
    }
}
