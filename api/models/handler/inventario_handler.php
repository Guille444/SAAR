<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla INVENTARIO.
*/
class InventarioHandler
{
    protected $id = null;
    protected $pieza = null;
    protected $cantidad_disponible = null;
    protected $proveedor = null;
    protected $fecha_ingreso = null;


    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_inventario, nombre_pieza, cantidad_disponible, proveedor, fecha_ingreso
                FROM inventario 
                INNER JOIN piezas USING(id_pieza)
                WHERE nombre_pieza LIKE ? 
                ORDER BY nombre_pieza';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO inventario(id_pieza, cantidad_disponible, proveedor, fecha_ingreso)
                VALUES(?, ?, ?, ?)';
        $params = array($this->pieza, $this->cantidad_disponible, $this->proveedor, $this->fecha_ingreso);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_inventario, nombre_pieza, cantidad_disponible, proveedor, fecha_ingreso
                FROM inventario
                INNER JOIN piezas USING(id_pieza)
                ORDER BY nombre_pieza';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_inventario, id_pieza, cantidad_disponible, proveedor, fecha_ingreso
                FROM inventario 
                WHERE id_inventario = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function updateRow()
    {
        $sql = 'UPDATE inventario
                SET id_pieza = ?, cantidad_disponible = ?, proveedor = ?, fecha_ingreso = ?
                WHERE id_inventario = ?';
        $params = array($this->pieza, $this->cantidad_disponible, $this->proveedor, $this->fecha_ingreso, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM inventario
                WHERE id_inventario = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function readInventarioPiezas()
    {
        $sql = "SELECT p.nombre_pieza, p.descripcion_pieza, i.cantidad_disponible, i.proveedor, i.fecha_ingreso
                FROM inventario i
                JOIN piezas p ON i.id_pieza = p.id_pieza
                ORDER BY p.nombre_pieza";
        return Database::getRows($sql);
    }
}
