<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre que maneja la lógica del inventario.
require_once('../../models/handler/inventario_handler.php');

/*
 * Clase para manejar el encapsulamiento de los datos de la tabla INVENTARIO.
 */
class InventarioData extends InventarioHandler
{
    /*
     * Atributos adicionales para manejo de errores y nombre de archivo.
     */
    private $data_error = null; // Almacena errores de validación.
    private $filename = null; // Almacena el nombre del archivo.

    /*
     * Métodos para validar y establecer los datos del inventario.
     */
    public function setIdInventario($value)
    {
        // Valida que el ID del inventario sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value; // Asigna el valor al atributo id.
            return true;
        } else {
            $this->data_error = 'El identificador del inventario es incorrecto'; // Mensaje de error.
            return false;
        }
    }

    public function setPieza($value)
    {
        // Valida que el ID de la pieza sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            $this->pieza = $value; // Asigna el valor al atributo pieza.
            return true;
        } else {
            $this->data_error = 'El identificador de la pieza es incorrecto'; // Mensaje de error.
            return false;
        }
    }

    public function setCantidadDisponible($value)
    {
        // Valida que la cantidad disponible sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            $this->cantidad_disponible = $value; // Asigna el valor al atributo cantidad_disponible.
            return true;
        } else {
            $this->data_error = 'La cantidad disponible debe ser un valor numérico entero'; // Mensaje de error.
            return false;
        }
    }

    public function setProveedor($value, $min = 2, $max = 50)
    {
        // Valida que el nombre del proveedor sea alfanumérico.
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El nombre debe ser un valor alfanumérico'; // Mensaje de error.
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->proveedor = $value; // Asigna el valor al atributo proveedor.
            return true;
        } else {
            $this->data_error = 'El nombre del proveedor debe tener una longitud entre ' . $min . ' y ' . $max; // Mensaje de error.
            return false;
        }
    }

    public function setFechaIngreso($value)
    {
        // Valida que la fecha de ingreso sea un valor de fecha válido.
        if (Validator::validateDate($value)) {
            $this->fecha_ingreso = $value; // Asigna el valor al atributo fecha_ingreso.
            return true;
        } else {
            $this->data_error = 'La fecha de ingreso debe ser un valor de fecha válido'; // Mensaje de error.
            return false;
        }
    }

    /*
     * Métodos para obtener los atributos adicionales.
     */
    public function getDataError()
    {
        return $this->data_error; // Retorna el mensaje de error.
    }

    public function getFilename()
    {
        return $this->filename; // Retorna el nombre del archivo.
    }
}
