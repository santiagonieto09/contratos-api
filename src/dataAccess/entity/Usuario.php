<?php

namespace App\dataAccess\entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Entidad Usuario para autenticación y gestión de contratos.
 * 
 * Representa un usuario del sistema que puede crear y gestionar contratos.
 * Implementa las interfaces necesarias para la autenticación con Symfony Security.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usuarios')]
#[ORM\HasLifecycleCallbacks]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identificador único del usuario (UUID).
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    /**
     * Correo electrónico del usuario (único).
     */
    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $email;

    /**
     * Contraseña hasheada del usuario.
     */
    #[ORM\Column(type: Types::STRING)]
    private string $password;

    /**
     * Roles asignados al usuario.
     * 
     * @var array<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * Fecha de creación del usuario.
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $creadoEn;

    /**
     * Contratos creados por el usuario.
     * 
     * @var Collection<int, Contrato>
     */
    #[ORM\OneToMany(targetEntity: Contrato::class, mappedBy: 'usuario', cascade: ['persist', 'remove'])]
    private Collection $contratos;

    /**
     * Constructor del usuario.
     * Inicializa el UUID y la colección de contratos.
     */
    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->contratos = new ArrayCollection();
        $this->creadoEn = new \DateTimeImmutable();
    }

    /**
     * Obtiene el identificador único del usuario.
     * 
     * @return Uuid Identificador UUID
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * Obtiene el email del usuario.
     * 
     * @return string Email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Establece el email del usuario.
     * 
     * @param string $email Nuevo email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Obtiene la contraseña hasheada.
     * 
     * @return string Contraseña hasheada
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Establece la contraseña hasheada.
     * 
     * @param string $password Contraseña hasheada
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Obtiene los roles del usuario.
     * Siempre incluye ROLE_USER como mínimo.
     * 
     * @return array<string> Lista de roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantizar que cada usuario tenga al menos ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Establece los roles del usuario.
     * 
     * @param array<string> $roles Lista de roles
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Obtiene el identificador del usuario para autenticación.
     * 
     * @return string Email del usuario
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Limpia credenciales sensibles temporales.
     * Implementación requerida por UserInterface.
     */
    public function eraseCredentials(): void
    {
        // Si almacenas datos sensibles temporales, límpialos aquí
    }

    /**
     * Obtiene la fecha de creación del usuario.
     * 
     * @return \DateTimeImmutable Fecha de creación
     */
    public function getCreadoEn(): \DateTimeImmutable
    {
        return $this->creadoEn;
    }

    /**
     * Obtiene los contratos del usuario.
     * 
     * @return Collection<int, Contrato> Colección de contratos
     */
    public function getContratos(): Collection
    {
        return $this->contratos;
    }

    /**
     * Agrega un contrato al usuario.
     * 
     * @param Contrato $contrato Contrato a agregar
     * @return self
     */
    public function agregarContrato(Contrato $contrato): self
    {
        if (!$this->contratos->contains($contrato)) {
            $this->contratos->add($contrato);
            $contrato->setUsuario($this);
        }

        return $this;
    }

    /**
     * Elimina un contrato del usuario.
     * 
     * @param Contrato $contrato Contrato a eliminar
     * @return self
     */
    public function eliminarContrato(Contrato $contrato): self
    {
        if ($this->contratos->removeElement($contrato)) {
            if ($contrato->getUsuario() === $this) {
                $contrato->setUsuario(null);
            }
        }

        return $this;
    }
}
