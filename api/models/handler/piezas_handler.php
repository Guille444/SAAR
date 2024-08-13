<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class PiezaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $id_cliente = null;
    protected $nombre_pieza = null;
    protected $descripcion_pieza = null;
    protected $precio_unitario = null;


    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_pieza, id_cliente, nombre_pieza, descripcion_pieza, precio_unitario
            FROM piezas 
            WHERE nombre_pieza LIKE ?
            ORDER BY nombre_pieza';
        $params = array($value);
        return Database::getRows($sql, $params);
    }


    public function createRow()
    {
        $sql = 'INSERT INTO piezas(id_cliente, nombre_pieza, descripcion_pieza, precio_unitario)
            VALUES(?, ?, ?, ?)';
        $params = array($this->id_cliente, $this->nombre_pieza, $this->descripcion_pieza, $this->precio_unitario);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_pieza, id_cliente, nombre_pieza, descripcion_pieza, precio_unitario
            FROM piezas
            ORDER BY id_pieza';
        return Database::getRows($sql);
    }

    public function readAll2()
{
    $sql = 'SELECT id_pieza, nombre_pieza
            FROM piezas
            ORDER BY id_pieza';
    return Database::getRows($sql);
}


    public function readOne()
    {
        $sql = 'SELECT id_pieza, id_cliente, nombre_pieza, descripcion_pieza, precio_unitario
                FROM piezas
                WHERE id_pieza = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE piezas
                SET id_cliente = ?, nombre_pieza = ?, descripcion_pieza = ?, precio_unitario = ?
                WHERE id_pieza = ?';
        $params = array($this->id_cliente, $this->nombre_pieza, $this->descripcion_pieza, $this->precio_unitario, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM piezas
                WHERE id_pieza = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
