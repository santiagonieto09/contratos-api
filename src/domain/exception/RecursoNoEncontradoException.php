<?php

namespace App\domain\exception;

/**
 * Excepción para recursos no encontrados.
 */
class RecursoNoEncontradoException extends \Exception
{
    /**
     * Constructor de la excepción.
     * 
     * @param string $recurso Nombre del recurso
     * @param string $identificador Identificador del recurso
     */
    public function __construct(string $recurso, string $identificador)
    {
        parent::__construct(
            sprintf('%s con identificador "%s" no encontrado', $recurso, $identificador)
        );
    }
}
