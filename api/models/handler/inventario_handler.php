<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
*	Clase para manejar el comportamiento de los datos de la tabla INVENTARIO.
*/
class InventarioHandler
{
    // Propiedades protegidas para almacenar los datos del inventario y la pieza.
    protected $id = null; // ID del inventario.
    protected $pieza = null; // ID de la pieza relacionada.
    protected $cantidad_disponible = null; // Cantidad disponible de la pieza en el inventario.
    protected $proveedor = null; // Nombre del proveedor de la pieza.
    protected $fecha_ingreso = null; // Fecha de ingreso de la pieza al inventario.

    // Método para buscar filas en el inventario con base en un valor de búsqueda.
    public function searchRows()
    {
        // Se obtiene el valor de búsqueda con el operador de coincidencia parcial LIKE.
        $value = '%' . Validator::getSearchValue() . '%';
        // Se define la consulta SQL para buscar las piezas cuyo nombre coincida con el valor de búsqueda.
        $sql = 'SELECT id_inventario, nombre_pieza, cantidad_disponible, proveedor, fecha_ingreso
                FROM inventario 
                INNER JOIN piezas USING(id_pieza)
                WHERE nombre_pieza LIKE ? 
                ORDER BY nombre_pieza';
        // Se establece el parámetro con el valor de búsqueda.
        $params = array($value);
        // Se ejecuta la consulta y se retornan las filas resultantes.
        return Database::getRows($sql, $params);
    }

    // Método para insertar un nuevo registro en la tabla inventario.
    public function createRow()
    {
        // Consulta SQL para insertar una nueva fila en la tabla inventario.
        $sql = 'INSERT INTO inventario(id_pieza, cantidad_disponible, proveedor, fecha_ingreso)
                VALUES(?, ?, ?, ?)';
        // Parámetros para la consulta, que corresponden a los atributos del objeto.
        $params = array($this->pieza, $this->cantidad_disponible, $this->proveedor, $this->fecha_ingreso);
        // Se ejecuta la consulta para insertar los datos.
        return Database::executeRow($sql, $params);
    }

    // Método para recuperar todas las filas de la tabla inventario.
    public function readAll()
    {
        // Consulta SQL para obtener todas las piezas del inventario, unidas a la tabla de piezas.
        $sql = 'SELECT id_inventario, nombre_pieza, cantidad_disponible, proveedor, fecha_ingreso
                FROM inventario
                INNER JOIN piezas USING(id_pieza)
                ORDER BY nombre_pieza';
        // Se ejecuta la consulta y se retornan las filas.
        return Database::getRows($sql);
    }

    // Método para recuperar un solo registro de la tabla inventario con base en el id.
    public function readOne()
    {
        // Consulta SQL para obtener los datos de un inventario específico según su ID.
        $sql = 'SELECT id_inventario, id_pieza, cantidad_disponible, proveedor, fecha_ingreso
                FROM inventario 
                WHERE id_inventario = ?';
        // Parámetro que corresponde al ID del inventario.
        $params = array($this->id);
        // Se ejecuta la consulta y se retorna la fila correspondiente.
        return Database::getRow($sql, $params);
    }

    // Método para actualizar los datos de un registro en la tabla inventario.
    public function updateRow()
    {
        // Consulta SQL para actualizar una fila existente en la tabla inventario.
        $sql = 'UPDATE inventario
                SET id_pieza = ?, cantidad_disponible = ?, proveedor = ?, fecha_ingreso = ?
                WHERE id_inventario = ?';
        // Parámetros para la consulta, que incluyen los nuevos valores y el ID del inventario.
        $params = array($this->pieza, $this->cantidad_disponible, $this->proveedor, $this->fecha_ingreso, $this->id);
        // Se ejecuta la consulta para actualizar los datos.
        return Database::executeRow($sql, $params);
    }

    // Método para eliminar un registro de la tabla inventario con base en el id.
    public function deleteRow()
    {
        // Consulta SQL para eliminar una fila de la tabla inventario según el ID.
        $sql = 'DELETE FROM inventario
                WHERE id_inventario = ?';
        // Parámetro que corresponde al ID del inventario.
        $params = array($this->id);
        // Se ejecuta la consulta para eliminar el registro.
        return Database::executeRow($sql, $params);
    }

    // Método para leer información detallada del inventario y las piezas relacionadas.
    public function readInventarioPiezas()
    {
        // Consulta SQL para obtener los detalles de las piezas junto con la cantidad disponible en inventario.
        $sql = "SELECT p.nombre_pieza, p.descripcion_pieza, i.cantidad_disponible, i.proveedor, i.fecha_ingreso
                FROM inventario i
                JOIN piezas p ON i.id_pieza = p.id_pieza
                ORDER BY p.nombre_pieza";
        // Se ejecuta la consulta y se retornan las filas resultantes.
        return Database::getRows($sql);
    }
}
