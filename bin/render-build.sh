#!/bin/bash
set -e

echo "=== Render Build Script ==="

# Generar claves JWT si no existen
if [ ! -f config/jwt/private.pem ] || [ ! -f config/jwt/public.pem ]; then
    echo "Generando claves JWT..."
    mkdir -p config/jwt
    openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:2048
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    chmod 600 config/jwt/private.pem
    echo "Claves JWT generadas correctamente"
fi

# Instalar dependencias
echo "Instalando dependencias..."
composer install --no-dev --optimize-autoloader --no-interaction

# Limpiar cache de producción
echo "Limpiando cache..."
php bin/console cache:clear --env=prod --no-debug --no-interaction

echo "=== Build completado ==="
