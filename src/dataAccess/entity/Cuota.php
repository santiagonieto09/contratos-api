<?php

namespace App\dataAccess\entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Entidad Cuota para la gestión de cuotas de contratos.
 * 
 * Representa una cuota individual de un contrato, con su valor base,
 * interés aplicado, tarifa de pago y total a pagar.
 */
#[ORM\Entity]
#[ORM\Table(name: 'cuotas')]
class Cuota
{
    /**
     * Identificador único de la cuota (UUID).
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    /**
     * Número de la cuota (1, 2, 3, etc.).
     */
    #[ORM\Column(type: Types::INTEGER)]
    private int $numeroCuota;

    /**
     * Valor base de la cuota (valor total / número de meses).
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $valorBase;

    /**
     * Interés calculado sobre el saldo pendiente.
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $interes;

    /**
     * Tarifa de pago aplicada por el servicio de pago.
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $tarifaPago;

    /**
     * Total a pagar (valor base + interés + tarifa).
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $total;

    /**
     * Fecha de pago de la cuota.
     */
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $fechaPago;

    /**
     * Contrato al que pertenece la cuota.
     */
    #[ORM\ManyToOne(targetEntity: Contrato::class, inversedBy: 'cuotas')]
    #[ORM\JoinColumn(name: 'contrato_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Contrato $contrato = null;

    /**
     * Constructor de la cuota.
     * Inicializa el UUID.
     */
    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    /**
     * Obtiene el identificador único de la cuota.
     * 
     * @return Uuid Identificador UUID
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * Obtiene el número de la cuota.
     * 
     * @return int Número de cuota
     */
    public function getNumeroCuota(): int
    {
        return $this->numeroCuota;
    }

    /**
     * Establece el número de la cuota.
     * 
     * @param int $numeroCuota Número de cuota
     * @return self
     */
    public function setNumeroCuota(int $numeroCuota): self
    {
        $this->numeroCuota = $numeroCuota;
        return $this;
    }

    /**
     * Obtiene el valor base de la cuota.
     * 
     * @return string Valor base (como string para precisión decimal)
     */
    public function getValorBase(): string
    {
        return $this->valorBase;
    }

    /**
     * Obtiene el valor base como float.
     * 
     * @return float Valor base
     */
    public function getValorBaseFloat(): float
    {
        return (float) $this->valorBase;
    }

    /**
     * Establece el valor base de la cuota.
     * 
     * @param string|float $valorBase Valor base
     * @return self
     */
    public function setValorBase(string|float $valorBase): self
    {
        $this->valorBase = (string) $valorBase;
        return $this;
    }

    /**
     * Obtiene el interés de la cuota.
     * 
     * @return string Interés (como string para precisión decimal)
     */
    public function getInteres(): string
    {
        return $this->interes;
    }

    /**
     * Obtiene el interés como float.
     * 
     * @return float Interés
     */
    public function getInteresFloat(): float
    {
        return (float) $this->interes;
    }

    /**
     * Establece el interés de la cuota.
     * 
     * @param string|float $interes Interés
     * @return self
     */
    public function setInteres(string|float $interes): self
    {
        $this->interes = (string) $interes;
        return $this;
    }

    /**
     * Obtiene la tarifa de pago.
     * 
     * @return string Tarifa de pago (como string para precisión decimal)
     */
    public function getTarifaPago(): string
    {
        return $this->tarifaPago;
    }

    /**
     * Obtiene la tarifa de pago como float.
     * 
     * @return float Tarifa de pago
     */
    public function getTarifaPagoFloat(): float
    {
        return (float) $this->tarifaPago;
    }

    /**
     * Establece la tarifa de pago.
     * 
     * @param string|float $tarifaPago Tarifa de pago
     * @return self
     */
    public function setTarifaPago(string|float $tarifaPago): self
    {
        $this->tarifaPago = (string) $tarifaPago;
        return $this;
    }

    /**
     * Obtiene el total de la cuota.
     * 
     * @return string Total (como string para precisión decimal)
     */
    public function getTotal(): string
    {
        return $this->total;
    }

    /**
     * Obtiene el total como float.
     * 
     * @return float Total
     */
    public function getTotalFloat(): float
    {
        return (float) $this->total;
    }

    /**
     * Establece el total de la cuota.
     * 
     * @param string|float $total Total
     * @return self
     */
    public function setTotal(string|float $total): self
    {
        $this->total = (string) $total;
        return $this;
    }

    /**
     * Obtiene la fecha de pago de la cuota.
     * 
     * @return \DateTimeImmutable Fecha de pago
     */
    public function getFechaPago(): \DateTimeImmutable
    {
        return $this->fechaPago;
    }

    /**
     * Establece la fecha de pago de la cuota.
     * 
     * @param \DateTimeImmutable $fechaPago Fecha de pago
     * @return self
     */
    public function setFechaPago(\DateTimeImmutable $fechaPago): self
    {
        $this->fechaPago = $fechaPago;
        return $this;
    }

    /**
     * Obtiene el contrato al que pertenece la cuota.
     * 
     * @return Contrato|null Contrato
     */
    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    /**
     * Establece el contrato al que pertenece la cuota.
     * 
     * @param Contrato|null $contrato Contrato
     * @return self
     */
    public function setContrato(?Contrato $contrato): self
    {
        $this->contrato = $contrato;
        return $this;
    }
}
