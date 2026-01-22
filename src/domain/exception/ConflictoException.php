<?php

namespace App\domain\exception;

/**
 * Excepción para conflictos de datos (duplicados, etc).
 */
class ConflictoException extends \Exception
{
    /**
     * Constructor de la excepción.
     * 
     * @param string $mensaje Mensaje de error
     */
    public function __construct(string $mensaje)
    {
        parent::__construct($mensaje);
    }
}
