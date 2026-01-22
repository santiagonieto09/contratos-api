<?php

namespace App\dataAccess\repository;

use App\dataAccess\entity\Usuario;

/**
 * Interface para el repositorio de usuarios.
 * 
 * Define el contrato que debe cumplir cualquier implementación
 * del repositorio de usuarios.
 */
interface UsuarioRepositoryInterface
{
    /**
     * Guarda un usuario en la base de datos.
     * 
     * @param Usuario $usuario Usuario a guardar
     * @return void
     */
    public function guardar(Usuario $usuario): void;

    /**
     * Busca un usuario por su ID.
     * 
     * @param string $id ID del usuario
     * @return Usuario|null Usuario encontrado o null
     */
    public function buscarPorId(string $id): ?Usuario;

    /**
     * Busca un usuario por su email.
     * 
     * @param string $email Email del usuario
     * @return Usuario|null Usuario encontrado o null
     */
    public function buscarPorEmail(string $email): ?Usuario;

    /**
     * Verifica si existe un usuario con el email dado.
     * 
     * @param string $email Email a verificar
     * @return bool True si existe
     */
    public function existePorEmail(string $email): bool;

    /**
     * Elimina un usuario.
     * 
     * @param Usuario $usuario Usuario a eliminar
     * @return void
     */
    public function eliminar(Usuario $usuario): void;
}
