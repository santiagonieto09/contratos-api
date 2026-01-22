# Script de inicialización para Windows PowerShell
# Ejecutar desde la raíz del proyecto: .\scripts\init.ps1

Write-Host "=== Inicializando proyecto Contratos API ===" -ForegroundColor Green

# Copiar archivo de entorno si no existe
if (-not (Test-Path ".env")) {
    Copy-Item "env.example" ".env"
    Write-Host "Archivo .env creado desde env.example" -ForegroundColor Yellow
}

# Construir contenedores Docker
Write-Host "Construyendo contenedores Docker..." -ForegroundColor Cyan
docker-compose build

# Iniciar contenedores
Write-Host "Iniciando contenedores..." -ForegroundColor Cyan
docker-compose up -d

# Esperar a que PostgreSQL esté listo
Write-Host "Esperando a que PostgreSQL esté listo..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Instalar dependencias de Composer
Write-Host "Instalando dependencias de Composer..." -ForegroundColor Cyan
docker-compose exec php composer install

# Crear directorio para claves JWT
Write-Host "Generando claves JWT..." -ForegroundColor Cyan
docker-compose exec php mkdir -p config/jwt
docker-compose exec php openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:contratos_jwt_secret
docker-compose exec php openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:contratos_jwt_secret

# Ejecutar migraciones
Write-Host "Ejecutando migraciones de base de datos..." -ForegroundColor Cyan
docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

Write-Host "=== Inicialización completada ===" -ForegroundColor Green
Write-Host "API disponible en: http://localhost:8081" -ForegroundColor White
Write-Host "PostgreSQL disponible en: localhost:5434" -ForegroundColor White
Write-Host "  - Base de datos: contratos_db" -ForegroundColor White
Write-Host "  - Usuario: contratos_user" -ForegroundColor White
Write-Host "  - Contraseña: contratos_password" -ForegroundColor White
