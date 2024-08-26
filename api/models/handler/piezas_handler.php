<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla "piezas".
 *  Esta clase proporciona métodos para realizar operaciones CRUD sobre los registros de piezas.
 */
class PiezaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     *  Cada atributo corresponde a un campo en la tabla "piezas".
     */
    protected $id = null; // ID único de la pieza
    protected $id_cliente = null; // ID del cliente asociado a la pieza
    protected $nombre_pieza = null; // Nombre de la pieza
    protected $descripcion_pieza = null; // Descripción de la pieza
    protected $precio_unitario = null; // Precio unitario de la pieza

    /*
     *  Método para buscar registros en la tabla "piezas" que coincidan con un valor de búsqueda.
     *  Utiliza una consulta SQL con LIKE para encontrar coincidencias parciales en el nombre de la pieza.
     *  Retorna un conjunto de filas que coinciden con el criterio de búsqueda.
     */
    public function searchRows()
    {
        // Se utiliza un valor de búsqueda con comodines para la consulta.
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_pieza, id_cliente, nombre_pieza, descripcion_pieza, precio_unitario
                FROM piezas 
                WHERE nombre_pieza LIKE ?
                ORDER BY nombre_pieza';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    /*
     *  Método para crear un nuevo registro en la tabla "piezas".
     *  Inserta un nuevo registro con los valores proporcionados en los atributos de la clase.
     *  Retorna el resultado de la operación de inserción.
     */
    public function createRow()
    {
        $sql = 'INSERT INTO piezas(id_cliente, nombre_pieza, descripcion_pieza, precio_unitario)
                VALUES(?, ?, ?, ?)';
        $params = array($this->id_cliente, $this->nombre_pieza, $this->descripcion_pieza, $this->precio_unitario);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Método para leer todos los registros de la tabla "piezas".
     *  Realiza una consulta SQL para obtener todos los registros ordenados por el ID de la pieza.
     *  Retorna un conjunto de filas que representan todas las piezas en la base de datos.
     */
    public function readAll()
    {
        $sql = 'SELECT id_pieza, id_cliente, nombre_pieza, descripcion_pieza, precio_unitario
                FROM piezas
                ORDER BY id_pieza';
        return Database::getRows($sql);
    }

    /*
     *  Método para leer todos los registros de la tabla "piezas", pero solo selecciona los campos "id_pieza" y "nombre_pieza".
     *  Este método puede ser útil cuando solo se necesita una lista de identificación y nombres de las piezas.
     *  Retorna un conjunto de filas con los ID y nombres de las piezas.
     */
    public function readAll2()
    {
        $sql = 'SELECT id_pieza, nombre_pieza
                FROM piezas
                ORDER BY id_pieza';
        return Database::getRows($sql);
    }

    /*
     *  Método para leer un único registro de la tabla "piezas" basado en el ID de la pieza.
     *  Realiza una consulta SQL para obtener los detalles de la pieza especificada.
     *  Retorna una fila que representa la pieza solicitada o null si no existe.
     */
    public function readOne()
    {
        $sql = 'SELECT id_pieza, id_cliente, nombre_pieza, descripcion_pieza, precio_unitario
                FROM piezas
                WHERE id_pieza = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /*
     *  Método para actualizar un registro existente en la tabla "piezas".
     *  Actualiza los valores del registro de la pieza con los valores proporcionados en los atributos de la clase.
     *  Retorna el resultado de la operación de actualización.
     */
    public function updateRow()
    {
        $sql = 'UPDATE piezas
                SET id_cliente = ?, nombre_pieza = ?, descripcion_pieza = ?, precio_unitario = ?
                WHERE id_pieza = ?';
        $params = array($this->id_cliente, $this->nombre_pieza, $this->descripcion_pieza, $this->precio_unitario, $this->id);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Método para eliminar un registro de la tabla "piezas".
     *  Elimina el registro de la pieza especificada basado en el ID.
     *  Retorna el resultado de la operación de eliminación.
     */
    public function deleteRow()
    {
        $sql = 'DELETE FROM piezas
                WHERE id_pieza = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Método para leer la cantidad de vehículos que utilizan una pieza específica.
     *  Realiza una consulta SQL que cuenta la cantidad de coches que tienen la pieza especificada.
     *  Agrupa los resultados por modelo de vehículo.
     *  Retorna un conjunto de filas que representan los modelos de vehículos y la cantidad de coches asociados.
     */
    public function readPiezasCoches()
    {
        $sql = 'SELECT modelo_vehiculo, COUNT(vehiculos.id_vehiculo) coches
                FROM modelos, vehiculos, citas, piezas, detalle_citas
                WHERE modelos.id_modelo = vehiculos.id_modelo AND
                      citas.id_vehiculo = vehiculos.id_vehiculo AND
                      citas.id_cita = detalle_citas.id_cita AND
                      detalle_citas.id_pieza = piezas.id_pieza AND
                      piezas.id_pieza = ?
                GROUP BY modelo_vehiculo;';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }
}
