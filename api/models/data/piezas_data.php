<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/piezas_handler.php');
/*
*	Clase para manejar el encapsulamiento de los datos de la tabla CLIENTE.
*/
class PiezaData extends PiezaHandler

{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
    *   Métodos para validar y establecer los datos.
    */
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la pieza es incorrecto';
            return false;
        }
    }

    public function setIdCliente($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_cliente = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la pieza es incorrecto';
            return false;
        }
    }

    public function setPieza($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre_pieza = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setDescripcion($value, $min = 1, $max = 1000)
    {
        if (!Validator::validateString($value)) {
            $this->data_error = 'La descripción contiene caracteres prohibidos';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->descripcion_pieza = $value;
            return true;
        } else {
            $this->data_error = 'La descripción debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setPrecio($value)
    {
        if (Validator::validateMoney($value)) {
            $this->precio_unitario = $value;
            return true;
        } else {
            $this->data_error = 'El precio debe ser un número positivo';
            return false;
        }
    }



    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
