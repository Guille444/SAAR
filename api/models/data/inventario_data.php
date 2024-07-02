<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/inventario_handler.php');
/*
 *	Clase para manejar el encapsulamiento de los datos de la tabla INVENTARIO.
 */
class InventarioData extends InventarioHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;
    private $filename = null;

    /*
     *   Métodos para validar y establecer los datos.
     */
    public function setIdInventario($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del inventario es incorrecto';
            return false;
        }
    }

    public function setPieza($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->pieza = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la pieza es incorrecto';
            return false;
        }
    }

    public function setCantidadDisponible($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->cantidad_disponible = $value;
            return true;
        } else {
            $this->data_error = 'La cantidad disponible debe ser un valor numérico entero';
            return false;
        }
    }

    public function setProveedor($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El nombre debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->proveedor = $value;
            return true;
        } else {
            $this->data_error = 'El nombre del proveedor debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setFechaIngreso($value)
    {
        if (Validator::validateDate($value)) {
            $this->fecha_ingreso = $value;
            return true;
        } else {
            $this->data_error = 'La fecha de ingreso debe ser un valor de fecha válido';
            return false;
        }
    }

    /*
     *  Métodos para obtener los atributos adicionales.
     */
    public function getDataError()
    {
        return $this->data_error;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}