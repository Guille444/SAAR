<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handlers/inventario_handler.php');
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
            $this->id_inventario = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del inventario es incorrecto';
            return false;
        }
    }

    public function setIdPieza($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_pieza = $value;
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

    public function setProveedor($value)
    {
        if (Validator::validateString($value)) {
            $this->proveedor = $value;
            return true;
        } else {
            $this->data_error = 'El proveedor debe ser un valor alfanumérico';
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


    public function setNombrePieza($value)
    {
        if (Validator::validateString($value)) {
            $this->nombre_pieza = $value;
            return true;
        } else {
            $this->data_error = 'El nombre de la pieza debe ser un valor alfanumérico';
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