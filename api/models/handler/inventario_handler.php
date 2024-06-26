<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class InventarioHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;

    protected $id_pieza = null;
    protected $cantidad_disponible = null;
    protected $proveedor = null;
    protected $fecha_ingreso = null;


    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
{
    $value = '%' . Validator::getSearchValue() . '%';
    $sql = 'SELECT id_inventario, cantidad_disponible, proveedor, fecha_ingreso
            FROM inventario
            WHERE proveedor LIKE ?
            ORDER BY proveedor';
    $params = array($value);
    return Database::getRows($sql, $params);
}


public function createRow()
{
    $sql = 'INSERT INTO inventario(id_pieza, cantidad_disponible, proveedor, fecha_ingreso)
            VALUES(?, ?, ?, ?)';
    $params = array($this->id_pieza, $this->cantidad_disponible, $this->proveedor, $this->fecha_ingreso);
    return Database::executeRow($sql, $params);
}

public function readAll()
{
    $sql = 'SELECT id_inventario, id_pieza, cantidad_disponible, proveedor, fecha_ingreso
            FROM inventario
            ORDER BY id_inventario';
    return Database::getRows($sql);
}


    public function readOne()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, alias_administrador, correo_administrador
                FROM administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE administradores
                SET nombre_administrador = ?, apellido_administrador = ?, correo_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
