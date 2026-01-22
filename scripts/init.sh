#!/bin/bash
# Script de inicialización para Linux/Mac
# Ejecutar desde la raíz del proyecto: ./scripts/init.sh

set -e

echo "=== Inicializando proyecto Contratos API ==="

# Copiar archivo de entorno si no existe
if [ ! -f ".env" ]; then
    cp env.example .env
    echo "Archivo .env creado desde env.example"
fi

# Construir contenedores Docker
echo "Construyendo contenedores Docker..."
docker-compose build

# Iniciar contenedores
echo "Iniciando contenedores..."
docker-compose up -d

# Esperar a que PostgreSQL esté listo
echo "Esperando a que PostgreSQL esté listo..."
sleep 10

# Instalar dependencias de Composer
echo "Instalando dependencias de Composer..."
docker-compose exec php composer install

# Crear directorio para claves JWT
echo "Generando claves JWT..."
docker-compose exec php mkdir -p config/jwt
docker-compose exec php openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:contratos_jwt_secret
docker-compose exec php openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:contratos_jwt_secret

# Ejecutar migraciones
echo "Ejecutando migraciones de base de datos..."
docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

echo "=== Inicialización completada ==="
echo "API disponible en: http://localhost:8081"
echo "PostgreSQL disponible en: localhost:5434"
echo "  - Base de datos: contratos_db"
echo "  - Usuario: contratos_user"
echo "  - Contraseña: contratos_password"
