<?php

namespace App\domain\services\paymentMethod;

/**
 * Interface para métodos de pago en línea.
 * 
 * Define el contrato que deben implementar todos los servicios de pago.
 * Utiliza el patrón Strategy para permitir diferentes implementaciones
 * de cálculo de intereses y tarifas.
 */
interface MetodoPagoInterface
{
    /**
     * Obtiene el nombre identificador del método de pago.
     * 
     * @return string Nombre del método (ej: 'paypal', 'payonline')
     */
    public function obtenerNombre(): string;

    /**
     * Obtiene el nombre descriptivo del método de pago.
     * 
     * @return string Nombre para mostrar (ej: 'PayPal', 'PayOnline')
     */
    public function obtenerNombreDescriptivo(): string;

    /**
     * Calcula el interés sobre el saldo pendiente.
     * 
     * @param float $saldoPendiente Saldo pendiente del contrato
     * @return float Monto del interés calculado
     */
    public function calcularInteresSaldoPendiente(float $saldoPendiente): float;

    /**
     * Calcula la tarifa de pago sobre el monto de la cuota.
     * 
     * @param float $montoCuota Monto de la cuota (valor base + interés)
     * @return float Monto de la tarifa de pago
     */
    public function calcularTarifaPago(float $montoCuota): float;

    /**
     * Obtiene la tasa de interés mensual.
     * 
     * @return float Tasa de interés (ej: 0.01 para 1%)
     */
    public function obtenerTasaInteres(): float;

    /**
     * Obtiene la tasa de tarifa de pago.
     * 
     * @return float Tasa de tarifa (ej: 0.02 para 2%)
     */
    public function obtenerTasaTarifa(): float;
}
