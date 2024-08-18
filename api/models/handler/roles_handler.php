<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla Marca.
 */
class RolesHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_rol, nombre_rol
                FROM roles
                WHERE nombre_rol LIKE ?
                ORDER BY nombre_rol';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO roles(nombre_rol)
                VALUES(?)';
        $params = array($this->nombre);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_rol, nombre_rol
                FROM roles
                ORDER BY nombre_rol';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_rol, nombre_rol
                FROM roles
                WHERE id_rol = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function updateRow()
    {
        $sql = 'UPDATE roles
                SET nombre_rol = ?
                WHERE id_rol = ?';
        $params = array($this->nombre, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM roles
                WHERE id_rol = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate($value)
    {
        $sql = 'SELECT id_rol
                FROM roles
                WHERE nombre_rol = ?';
        $params = array($value);
        return Database::getRow($sql, $params);
    }

    // Método para obtener todas las roles
    public function getAllroles()
    {
        $sql = 'SELECT id_rol, nombre_rol 
                FROM roles';
        return Database::getRows($sql);
    }
}