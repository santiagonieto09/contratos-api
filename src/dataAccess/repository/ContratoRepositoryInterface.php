<?php

namespace App\dataAccess\repository;

use App\dataAccess\entity\Contrato;
use App\dataAccess\entity\Usuario;

/**
 * Interface para el repositorio de contratos.
 * 
 * Define el contrato que debe cumplir cualquier implementación
 * del repositorio de contratos, siguiendo el principio de inversión
 * de dependencias (SOLID).
 */
interface ContratoRepositoryInterface
{
    /**
     * Guarda un contrato en la base de datos.
     * 
     * @param Contrato $contrato Contrato a guardar
     * @return void
     */
    public function guardar(Contrato $contrato): void;

    /**
     * Busca un contrato por su ID.
     * 
     * @param string $id ID del contrato
     * @return Contrato|null Contrato encontrado o null
     */
    public function buscarPorId(string $id): ?Contrato;

    /**
     * Busca todos los contratos de un usuario.
     * 
     * @param Usuario $usuario Usuario propietario
     * @return array<Contrato> Lista de contratos
     */
    public function buscarPorUsuario(Usuario $usuario): array;

    /**
     * Busca un contrato por su número.
     * 
     * @param string $numeroContrato Número del contrato
     * @return Contrato|null Contrato encontrado o null
     */
    public function buscarPorNumeroContrato(string $numeroContrato): ?Contrato;

    /**
     * Verifica si existe un contrato con el número dado.
     * 
     * @param string $numeroContrato Número del contrato
     * @return bool True si existe
     */
    public function existePorNumeroContrato(string $numeroContrato): bool;

    /**
     * Elimina un contrato.
     * 
     * @param Contrato $contrato Contrato a eliminar
     * @return void
     */
    public function eliminar(Contrato $contrato): void;

    /**
     * Obtiene todos los contratos.
     * 
     * @return array<Contrato> Lista de todos los contratos
     */
    public function buscarTodos(): array;
}
