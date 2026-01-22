<?php

namespace App\domain\mapper;

use App\dataAccess\entity\Cuota;
use App\domain\models\CuotaModelo;

/**
 * Mapper para convertir entre entidades y modelos de cuota.
 * 
 * Facilita la conversión bidireccional entre la capa de persistencia
 * y la capa de dominio.
 */
class CuotaMapper
{
    /**
     * Convierte una entidad Cuota a modelo de dominio.
     * 
     * @param Cuota $entidad Entidad cuota
     * @return CuotaModelo Modelo de dominio
     */
    public static function entidadAModelo(Cuota $entidad): CuotaModelo
    {
        $modelo = new CuotaModelo();
        $modelo->setNumeroCuota($entidad->getNumeroCuota())
            ->setValorBase($entidad->getValorBaseFloat())
            ->setInteres($entidad->getInteresFloat())
            ->setTarifaPago($entidad->getTarifaPagoFloat())
            ->setTotal($entidad->getTotalFloat())
            ->setFechaPago($entidad->getFechaPago());

        return $modelo;
    }

    /**
     * Convierte un modelo de cuota a entidad.
     * 
     * @param CuotaModelo $modelo Modelo de cuota
     * @return Cuota Entidad cuota
     */
    public static function modeloAEntidad(CuotaModelo $modelo): Cuota
    {
        $entidad = new Cuota();
        $entidad->setNumeroCuota($modelo->getNumeroCuota())
            ->setValorBase($modelo->getValorBase())
            ->setInteres($modelo->getInteres())
            ->setTarifaPago($modelo->getTarifaPago())
            ->setTotal($modelo->getTotal())
            ->setFechaPago($modelo->getFechaPago());

        return $entidad;
    }

    /**
     * Convierte un array de entidades a array de modelos.
     * 
     * @param array<Cuota> $entidades Lista de entidades
     * @return array<CuotaModelo> Lista de modelos
     */
    public static function entidadesAModelos(array $entidades): array
    {
        return array_map(
            fn(Cuota $entidad) => self::entidadAModelo($entidad),
            $entidades
        );
    }

    /**
     * Convierte un array de modelos a array de entidades.
     * 
     * @param array<CuotaModelo> $modelos Lista de modelos
     * @return array<Cuota> Lista de entidades
     */
    public static function modelosAEntidades(array $modelos): array
    {
        return array_map(
            fn(CuotaModelo $modelo) => self::modeloAEntidad($modelo),
            $modelos
        );
    }
}
