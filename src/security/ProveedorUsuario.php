<?php

namespace App\security;

use App\dataAccess\entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Proveedor de usuarios para autenticación.
 * 
 * Esta clase se encarga de cargar usuarios desde la base de datos
 * para el sistema de seguridad de Symfony.
 * 
 * @implements UserProviderInterface<Usuario>
 */
class ProveedorUsuario implements UserProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Refresca el usuario desde la base de datos.
     * 
     * @param UserInterface $user Usuario a refrescar
     * @return UserInterface Usuario actualizado
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->cargarUsuarioPorIdentificador($user->getUserIdentifier());
    }

    /**
     * Verifica si esta clase soporta el tipo de usuario dado.
     * 
     * @param string $class Clase del usuario
     * @return bool True si soporta la clase
     */
    public function supportsClass(string $class): bool
    {
        return Usuario::class === $class || is_subclass_of($class, Usuario::class);
    }

    /**
     * Carga un usuario por su identificador (email).
     * 
     * @param string $identifier Email del usuario
     * @return UserInterface Usuario encontrado
     * @throws UserNotFoundException Si el usuario no existe
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->cargarUsuarioPorIdentificador($identifier);
    }

    /**
     * Método interno para cargar usuario por email.
     * 
     * @param string $email Email del usuario
     * @return Usuario Usuario encontrado
     * @throws UserNotFoundException Si el usuario no existe
     */
    private function cargarUsuarioPorIdentificador(string $email): Usuario
    {
        $usuario = $this->entityManager
            ->getRepository(Usuario::class)
            ->findOneBy(['email' => $email]);

        if (!$usuario) {
            throw new UserNotFoundException(sprintf('Usuario con email "%s" no encontrado.', $email));
        }

        return $usuario;
    }
}
