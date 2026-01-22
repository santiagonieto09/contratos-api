<?php

namespace App\domain\models;

/**
 * Modelo de dominio para representar un contrato.
 * 
 * Este modelo se utiliza para transferir datos de contratos
 * entre las diferentes capas de la aplicación.
 */
class ContratoModelo
{
 
    private ?string $id = null;

    private string $numeroContrato;

    private \DateTimeImmutable $fechaContrato;

    private float $valorTotal;

    private string $metodoPago;

    private int $numeroMeses;

    private array $cuotas = [];

    /**
     * Obtiene el ID.
     * 
     * @return string|null ID
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Establece el ID.
     * 
     * @param string|null $id ID
     * @return self
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Obtiene el número de contrato.
     * 
     * @return string Número de contrato
     */
    public function getNumeroContrato(): string
    {
        return $this->numeroContrato;
    }

    /**
     * Establece el número de contrato.
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
     * Obtiene el valor total.
     * 
     * @return float Valor total
     */
    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    /**
     * Establece el valor total.
     * 
     * @param float $valorTotal Valor total
     * @return self
     */
    public function setValorTotal(float $valorTotal): self
    {
        $this->valorTotal = $valorTotal;
        return $this;
    }

    /**
     * Obtiene el método de pago.
     * 
     * @return string Método de pago
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
        $this->metodoPago = $metodoPago;
        return $this;
    }

    /**
     * Obtiene el número de meses.
     * 
     * @return int Número de meses
     */
    public function getNumeroMeses(): int
    {
        return $this->numeroMeses;
    }

    /**
     * Establece el número de meses.
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
     * Obtiene las cuotas.
     * 
     * @return array<CuotaModelo> Cuotas
     */
    public function getCuotas(): array
    {
        return $this->cuotas;
    }

    /**
     * Establece las cuotas.
     * 
     * @param array<CuotaModelo> $cuotas Cuotas
     * @return self
     */
    public function setCuotas(array $cuotas): self
    {
        $this->cuotas = $cuotas;
        return $this;
    }

    /**
     * Convierte el modelo a array.
     * 
     * @return array<string, mixed> Datos del modelo
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'numeroContrato' => $this->numeroContrato,
            'fechaContrato' => $this->fechaContrato->format('Y-m-d'),
            'valorTotal' => $this->valorTotal,
            'metodoPago' => $this->metodoPago,
            'numeroMeses' => $this->numeroMeses,
            'cuotas' => array_map(fn(CuotaModelo $c) => $c->toArray(), $this->cuotas),
        ];
    }
}
