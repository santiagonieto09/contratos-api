<?php

namespace App\domain\models;

/**
 * Modelo de dominio para representar una cuota.
 * 
 * Este modelo se utiliza para transferir datos de cuotas
 * entre las diferentes capas de la aplicación sin depender
 * de la entidad de persistencia.
 */
class CuotaModelo
{
    private int $numeroCuota;

    private float $valorBase;

    private float $interes;

    private float $tarifaPago;

    private float $total;

    private \DateTimeImmutable $fechaPago;

    /**
     * Obtiene el número de cuota.
     * 
     * @return int Número de cuota
     */
    public function getNumeroCuota(): int
    {
        return $this->numeroCuota;
    }

    /**
     * Establece el número de cuota.
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
     * Obtiene el valor base.
     * 
     * @return float Valor base
     */
    public function getValorBase(): float
    {
        return $this->valorBase;
    }

    /**
     * Establece el valor base.
     * 
     * @param float $valorBase Valor base
     * @return self
     */
    public function setValorBase(float $valorBase): self
    {
        $this->valorBase = $valorBase;
        return $this;
    }

    /**
     * Obtiene el interés.
     * 
     * @return float Interés
     */
    public function getInteres(): float
    {
        return $this->interes;
    }

    /**
     * Establece el interés.
     * 
     * @param float $interes Interés
     * @return self
     */
    public function setInteres(float $interes): self
    {
        $this->interes = $interes;
        return $this;
    }

    /**
     * Obtiene la tarifa de pago.
     * 
     * @return float Tarifa de pago
     */
    public function getTarifaPago(): float
    {
        return $this->tarifaPago;
    }

    /**
     * Establece la tarifa de pago.
     * 
     * @param float $tarifaPago Tarifa de pago
     * @return self
     */
    public function setTarifaPago(float $tarifaPago): self
    {
        $this->tarifaPago = $tarifaPago;
        return $this;
    }

    /**
     * Obtiene el total.
     * 
     * @return float Total
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * Establece el total.
     * 
     * @param float $total Total
     * @return self
     */
    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Obtiene la fecha de pago.
     * 
     * @return \DateTimeImmutable Fecha de pago
     */
    public function getFechaPago(): \DateTimeImmutable
    {
        return $this->fechaPago;
    }

    /**
     * Establece la fecha de pago.
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
     * Convierte el modelo a array.
     * 
     * @return array<string, mixed> Datos del modelo
     */
    public function toArray(): array
    {
        return [
            'numeroCuota' => $this->numeroCuota,
            'valorBase' => $this->valorBase,
            'interes' => $this->interes,
            'tarifaPago' => $this->tarifaPago,
            'total' => $this->total,
            'fechaPago' => $this->fechaPago->format('Y-m-d'),
        ];
    }
}
