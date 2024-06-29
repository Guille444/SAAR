<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla INVENTARIO.
*/
class InventarioHandler
{
    protected $id = null;
    protected $id_inventario = null;
    protected $id_pieza = null;
    protected $cantidad_disponible = null;
    protected $proveedor = null;
    protected $fecha_ingreso = null;
    protected $nombre_pieza = null;

    

    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_inventario, imagen, nombre_pieza, cantidad_disponible, proveedor, fecha_ingreso
                FROM inventario 
                INNER JOIN piezas  ON id_pieza = id_pieza
                WHERE nombre_pieza LIKE ? or proveedor LIKE ? 
                ORDER BY nombre_pieza';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO inventario(id_pieza, cantidad_disponible, proveedor, fecha_ingreso)
                VALUES(?, ?, ?, ?, ?)';
        $params = array($this->id_pieza, $this->cantidad_disponible, $this->proveedor, $this->fecha_ingreso);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_inventario, imagen, p.nombre_pieza, i.cantidad_disponible, i.proveedor, i.fecha_ingreso
                FROM inventario i
                INNER JOIN piezas p ON i.id_pieza = p.id_pieza';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT i.id_inventario, nombre_pieza, cantidad_disponible, proveedor, fecha_ingreso
                FROM inventario 
                INNER JOIN piezas  ON id_pieza = id_pieza
                WHERE id_inventario = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function updateRow()
    {
        $sql = 'UPDATE inventario
                SET imagen = ?, cantidad_disponible = ?, proveedor = ?, fecha_ingreso = ?
                WHERE id_inventario = ?';
        $params = array($this->cantidad_disponible, $this->proveedor, $this->fecha_ingreso, $this->id_inventario,$this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM inventario
                WHERE id_inventario = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
