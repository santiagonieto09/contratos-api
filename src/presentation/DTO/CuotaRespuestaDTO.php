<?php

namespace App\presentation\DTO;

use App\dataAccess\entity\Cuota;
use App\domain\models\CuotaModelo;

/**
 * DTO para la respuesta de cuotas.
 * 
 * Formatea los datos de una cuota para la respuesta de la API.
 */
class CuotaRespuestaDTO
{
    /**
     * Número de la cuota.
     */
    public int $numeroCuota;

    /**
     * Valor base de la cuota.
     */
    public float $valorBase;

    /**
     * Interés calculado.
     */
    public float $interes;

    /**
     * Tarifa de pago.
     */
    public float $tarifaPago;

    /**
     * Total a pagar.
     */
    public float $total;

    /**
     * Fecha de pago formateada.
     */
    public string $fechaPago;

    /**
     * Crea una instancia desde una entidad Cuota.
     * 
     * @param Cuota $cuota Entidad cuota
     * @return self Instancia del DTO
     */
    public static function desdeEntidad(Cuota $cuota): self
    {
        $dto = new self();
        $dto->numeroCuota = $cuota->getNumeroCuota();
        $dto->valorBase = $cuota->getValorBaseFloat();
        $dto->interes = $cuota->getInteresFloat();
        $dto->tarifaPago = $cuota->getTarifaPagoFloat();
        $dto->total = $cuota->getTotalFloat();
        $dto->fechaPago = $cuota->getFechaPago()->format('Y-m-d');

        return $dto;
    }

    /**
     * Crea una instancia desde un modelo de cuota.
     * 
     * @param CuotaModelo $modelo Modelo de cuota
     * @return self Instancia del DTO
     */
    public static function desdeModelo(CuotaModelo $modelo): self
    {
        $dto = new self();
        $dto->numeroCuota = $modelo->getNumeroCuota();
        $dto->valorBase = $modelo->getValorBase();
        $dto->interes = $modelo->getInteres();
        $dto->tarifaPago = $modelo->getTarifaPago();
        $dto->total = $modelo->getTotal();
        $dto->fechaPago = $modelo->getFechaPago()->format('Y-m-d');

        return $dto;
    }

    /**
     * Convierte el DTO a array.
     * 
     * @return array<string, mixed> Datos del DTO
     */
    public function toArray(): array
    {
        return [
            'numeroCuota' => $this->numeroCuota,
            'valorBase' => $this->valorBase,
            'interes' => $this->interes,
            'tarifaPago' => $this->tarifaPago,
            'total' => $this->total,
            'fechaPago' => $this->fechaPago,
        ];
    }
}
