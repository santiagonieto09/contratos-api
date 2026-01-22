<?php

namespace App\dataAccess\mapper;

use App\dataAccess\entity\Contrato;
use App\domain\models\ContratoModelo;
use App\domain\models\CuotaModelo;

/**
 * Mapper para convertir entre entidades y modelos de contrato.
 * 
 * Facilita la conversión bidireccional entre la capa de persistencia
 * y la capa de dominio.
 */
class ContratoMapper
{
    /**
     * Convierte una entidad Contrato a modelo de dominio.
     * 
     * @param Contrato $entidad Entidad contrato
     * @return ContratoModelo Modelo de dominio
     */
    public static function entidadAModelo(Contrato $entidad): ContratoModelo
    {
        $modelo = new ContratoModelo();
        $modelo->setId((string) $entidad->getId())
            ->setNumeroContrato($entidad->getNumeroContrato())
            ->setFechaContrato($entidad->getFechaContrato())
            ->setValorTotal($entidad->getValorTotalFloat())
            ->setMetodoPago($entidad->getMetodoPago())
            ->setNumeroMeses($entidad->getNumeroMeses());

        // Mapear cuotas si existen
        $cuotas = [];
        foreach ($entidad->getCuotas() as $cuotaEntidad) {
            $cuotaModelo = new CuotaModelo();
            $cuotaModelo->setNumeroCuota($cuotaEntidad->getNumeroCuota())
                ->setValorBase($cuotaEntidad->getValorBaseFloat())
                ->setInteres($cuotaEntidad->getInteresFloat())
                ->setTarifaPago($cuotaEntidad->getTarifaPagoFloat())
                ->setTotal($cuotaEntidad->getTotalFloat())
                ->setFechaPago($cuotaEntidad->getFechaPago());
            $cuotas[] = $cuotaModelo;
        }
        $modelo->setCuotas($cuotas);

        return $modelo;
    }

    /**
     * Convierte un array de entidades a array de modelos.
     * 
     * @param array<Contrato> $entidades Lista de entidades
     * @return array<ContratoModelo> Lista de modelos
     */
    public static function entidadesAModelos(array $entidades): array
    {
        return array_map(
            fn(Contrato $entidad) => self::entidadAModelo($entidad),
            $entidades
        );
    }
}
