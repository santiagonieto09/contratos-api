<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Kernel principal de la aplicación Symfony.
 * 
 * Configura el contenedor de servicios y las rutas de la aplicación.
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
