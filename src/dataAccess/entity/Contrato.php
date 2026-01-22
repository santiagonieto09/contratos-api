<?php

namespace App\dataAccess\entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Entidad Contrato para la gestión de contratos.
 * 
 * Representa un contrato con su información básica y las cuotas asociadas.
 * Cada contrato pertenece a un usuario y tiene un método de pago seleccionado.
 */
#[ORM\Entity]
#[ORM\Table(name: 'contratos')]
#[ORM\HasLifecycleCallbacks]
class Contrato
{
    /**
     * Identificador único del contrato (UUID).
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    /**
     * Número único del contrato.
     */
    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    private string $numeroContrato;

    /**
     * Fecha del contrato.
     */
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $fechaContrato;

    /**
     * Valor total del contrato.
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $valorTotal;

    /**
     * Método de pago seleccionado (paypal o payonline).
     */
    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $metodoPago;

    /**
     * Número de meses para el pago.
     */
    #[ORM\Column(type: Types::INTEGER)]
    private int $numeroMeses;

    /**
     * Usuario propietario del contrato.
     */
    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'contratos')]
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id', nullable: true)]
    private ?Usuario $usuario = null;

    /**
     * Fecha de creación del registro.
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $creadoEn;

    /**
     * Cuotas generadas para este contrato.
     * 
     * @var Collection<int, Cuota>
     */
    #[ORM\OneToMany(targetEntity: Cuota::class, mappedBy: 'contrato', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['numeroCuota' => 'ASC'])]
    private Collection $cuotas;

    /**
     * Constructor del contrato.
     * Inicializa el UUID y la colección de cuotas.
     */
    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->cuotas = new ArrayCollection();
        $this->creadoEn = new \DateTimeImmutable();
    }

    /**
     * Obtiene el identificador único del contrato.
     * 
     * @return Uuid Identificador UUID
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * Obtiene el número del contrato.
     * 
     * @return string Número de contrato
     */
    public function getNumeroContrato(): string
    {
        return $this->numeroContrato;
    }

    /**
     * Establece el número del contrato.
     * 
     * @param string $numeroContrato Número de contrato
     * @return self
     */
    public function setNumeroContrato(string $numeroContrato): self
    {
        $this->numeroContrato = $numeroContrato;
        return $this;
    }

    /**
     * Obtiene la fecha del contrato.
     * 
     * @return \DateTimeImmutable Fecha del contrato
     */
    public function getFechaContrato(): \DateTimeImmutable
    {
        return $this->fechaContrato;
    }

    /**
     * Establece la fecha del contrato.
     * 
     * @param \DateTimeImmutable $fechaContrato Fecha del contrato
     * @return self
     */
    public function setFechaContrato(\DateTimeImmutable $fechaContrato): self
    {
        $this->fechaContrato = $fechaContrato;
        return $this;
    }

    /**
     * Obtiene el valor total del contrato.
     * 
     * @return string Valor total (como string para precisión decimal)
     */
    public function getValorTotal(): string
    {
        return $this->valorTotal;
    }

    /**
     * Obtiene el valor total como float.
     * 
     * @return float Valor total
     */
    public function getValorTotalFloat(): float
    {
        return (float) $this->valorTotal;
    }

    /**
     * Establece el valor total del contrato.
     * 
     * @param string|float $valorTotal Valor total
     * @return self
     */
    public function setValorTotal(string|float $valorTotal): self
    {
        $this->valorTotal = (string) $valorTotal;
        return $this;
    }

    /**
     * Obtiene el método de pago seleccionado.
     * 
     * @return string Método de pago (paypal o payonline)
     */
    public function getMetodoPago(): string
    {
        return $this->metodoPago;
    }

    /**
     * Establece el método de pago.
     * 
     * @param string $metodoPago Método de pago
     * @return self
     */
    public function setMetodoPago(string $metodoPago): self
    {
        $this->metodoPago = strtolower($metodoPago);
        return $this;
    }

    /**
     * Obtiene el número de meses del contrato.
     * 
     * @return int Número de meses
     */
    public function getNumeroMeses(): int
    {
        return $this->numeroMeses;
    }

    /**
     * Establece el número de meses del contrato.
     * 
     * @param int $numeroMeses Número de meses
     * @return self
     */
    public function setNumeroMeses(int $numeroMeses): self
    {
        $this->numeroMeses = $numeroMeses;
        return $this;
    }

    /**
     * Obtiene el usuario propietario del contrato.
     * 
     * @return Usuario|null Usuario propietario
     */
    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    /**
     * Establece el usuario propietario del contrato.
     * 
     * @param Usuario|null $usuario Usuario propietario
     * @return self
     */
    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * Obtiene la fecha de creación del registro.
     * 
     * @return \DateTimeImmutable Fecha de creación
     */
    public function getCreadoEn(): \DateTimeImmutable
    {
        return $this->creadoEn;
    }

    /**
     * Obtiene las cuotas del contrato.
     * 
     * @return Collection<int, Cuota> Colección de cuotas
     */
    public function getCuotas(): Collection
    {
        return $this->cuotas;
    }

    /**
     * Agrega una cuota al contrato.
     * 
     * @param Cuota $cuota Cuota a agregar
     * @return self
     */
    public function agregarCuota(Cuota $cuota): self
    {
        if (!$this->cuotas->contains($cuota)) {
            $this->cuotas->add($cuota);
            $cuota->setContrato($this);
        }

        return $this;
    }

    /**
     * Elimina una cuota del contrato.
     * 
     * @param Cuota $cuota Cuota a eliminar
     * @return self
     */
    public function eliminarCuota(Cuota $cuota): self
    {
        if ($this->cuotas->removeElement($cuota)) {
            if ($cuota->getContrato() === $this) {
                $cuota->setContrato(null);
            }
        }

        return $this;
    }

    /**
     * Limpia todas las cuotas del contrato.
     * 
     * @return self
     */
    public function limpiarCuotas(): self
    {
        $this->cuotas->clear();
        return $this;
    }
}
