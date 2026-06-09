# API de Tramitación de Contratos

API REST desarrollada en **Symfony 7.2** para la gestión y tramitación de contratos con proyección de cuotas.

## Tecnologías

- **PHP 8.3**
- **Symfony 7.2**
- **PostgreSQL 16**
- **Docker & Docker Compose**
- **JWT para autenticación**
- **PHPUnit 10** para testing

## Características

- Gestión de contratos con múltiples métodos de pago
- Proyección automática de cuotas con intereses y tarifas
- Autenticación JWT segura
- Patrón Strategy para métodos de pago (PayPal, PayOnline)
- Arquitectura limpia con separación de capas (DDD)
- Validación robusta con excepciones personalizadas
- CI/CD con GitHub Actions y AWS

## Métodos de Pago

| Método | Interés sobre saldo pendiente | Tarifa por pago |
|--------|-------------------------------|-----------------|
| **PayPal** | 1% | 2% |
| **PayOnline** | 2% | 1% |

## Requisitos

- Docker y Docker Compose
- Git

## Instalación Rápida

```bash
# Clonar repositorio
git clone <url-repositorio>
cd contratos-api

# Iniciar contenedores
docker-compose up -d

# Esperar a que PostgreSQL inicie (10 segundos)
# Instalar dependencias
docker-compose exec php composer install

# Crear claves JWT
docker-compose exec php mkdir -p config/jwt
docker-compose exec php openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:tu_passphrase
docker-compose exec php openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:tu_passphrase

# Ejecutar migraciones
docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

## Conexión a Base de Datos (DataGrip/DBeaver)

| Campo | Valor |
|-------|-------|
| **Host** | localhost |
| **Puerto** | 5434 |
| **Base de datos** | contratos_db |
| **Usuario** | contratos_user |
| **Contraseña** | contratos_password |

## Endpoints API

### Autenticación (Públicos)

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/register` | Registrar usuario y devolver token JWT |
| POST | `/api/login` | Obtener token JWT |

### Usuario (Requiere JWT)

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/perfil` | Obtener perfil del usuario |

### Contratos (Requiere JWT)

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/contratos` | Crear contrato |
| GET | `/api/contratos` | Listar contratos del usuario |
| GET | `/api/contratos/{id}` | Obtener contrato por ID |
| GET | `/api/contratos/{id}/cuotas` | Obtener cuotas proyectadas |

### Públicos

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/contratos/proyeccion` | Simular proyección de cuotas |
| GET | `/api/contratos/metodos-pago` | Listar métodos de pago disponibles |

## Ejemplos de Uso (Postman/cURL)

### 1. Registrar Usuario

```json
POST http://localhost:8081/api/register
Content-Type: application/json

{
    "email": "usuario@ejemplo.com",
    "password": "password123"
}
```

Respuesta esperada:

```json
{
    "mensaje": "Usuario registrado exitosamente",
    "token": "<jwt>",
    "usuario": {
        "id": "...",
        "email": "usuario@ejemplo.com",
        "creadoEn": "2026-01-22 12:00:00"
    }
}
```

### 2. Obtener Token JWT

```json
POST http://localhost:8081/api/login
Content-Type: application/json

{
    "email": "usuario@ejemplo.com",
    "password": "password123"
}
```

Postman guarda este token en la variable de colección `jwt_token`.

### 3. Crear Contrato

```json
POST http://localhost:8081/api/contratos
Content-Type: application/json
Authorization: Bearer <tu-token-jwt>

{
    "numeroContrato": "CONT-2026-001",
    "fechaContrato": "2026-01-22",
    "valorTotal": 10000000,
    "metodoPago": "paypal",
    "numeroMeses": 12
}
```

### 4. Proyectar Cuotas (Sin autenticación)

```json
POST http://localhost:8081/api/contratos/proyeccion
Content-Type: application/json

{
    "valorTotal": 5000000,
    "numeroMeses": 6,
    "metodoPago": "payonline",
    "fechaContrato": "2026-01-22"
}
```

## Estructura del Proyecto

```
src/
├── dataAccess/
│   ├── entity/              # Entidades Doctrine (Usuario, Contrato, Cuota)
│   ├── mapper/              # Mappers entidad → modelo
│   └── repository/          # Repositorios e interfaces
├── domain/
│   ├── exception/           # Excepciones personalizadas
│   ├── mapper/              # Mappers de dominio
│   ├── models/              # Modelos de dominio (POPOs)
│   └── services/            # Servicios de negocio
│       └── paymentMethod/   # Estrategias de pago (Strategy Pattern)
├── presentation/
│   ├── controller/          # Controladores REST (thin controllers)
│   └── DTO/                 # Data Transfer Objects con validación
└── security/                # Proveedor de usuarios JWT
```

## Patrones de Diseño Implementados

| Patrón | Ubicación | Descripción |
|--------|-----------|-------------|
| **Strategy** | `paymentMethod/` | PayPal y PayOnline como estrategias intercambiables |
| **Repository** | `repository/` | Abstracción del acceso a datos con interfaces |
| **DTO** | `DTO/` | Transferencia y validación de datos |
| **Mapper** | `mapper/` | Conversión entre capas |

## Principios SOLID Aplicados

- **S** - Single Responsibility: Cada servicio tiene una única responsabilidad
- **O** - Open/Closed: Nuevos métodos de pago sin modificar código existente
- **L** - Liskov Substitution: Estrategias de pago intercambiables
- **I** - Interface Segregation: Interfaces pequeñas y específicas
- **D** - Dependency Inversion: Controladores dependen de abstracciones

## Tests

```bash
# Ejecutar tests
docker-compose exec php vendor/bin/phpunit

# Con formato detallado
docker-compose exec php vendor/bin/phpunit --testdox
```

### Tests Incluidos

- `CalculadoraCuotasServicioTest` - Validación de cálculos de cuotas
  - ✅ PayPal calcula interés (1%) y tarifa (2%) correctamente
  - ✅ PayOnline calcula interés (2%) y tarifa (1%) correctamente
  - ✅ Fechas de cuotas son correctas (mes+1, mes+2, etc.)
  - ✅ Método de pago inválido lanza excepción
  - ✅ Obtener métodos disponibles

## CI/CD (GitHub Actions)

El pipeline incluye:

1. **Tests** - PHPUnit con PostgreSQL
2. **Seguridad** - Composer audit
3. **Docker** - Build de imagen
4. **Deploy** - AWS ECR/ECS (solo rama main)

## Variables de Entorno

```env
APP_ENV=dev
APP_SECRET=tu_secret_key
DATABASE_URL=postgresql://user:pass@host:5432/db?serverVersion=16
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=tu_passphrase
```

## Comandos Útiles

```bash
# Iniciar contenedores
docker-compose up -d

# Detener contenedores
docker-compose down

# Ver logs
docker-compose logs -f php

# Limpiar caché
docker-compose exec php php bin/console cache:clear

# Crear migración
docker-compose exec php php bin/console make:migration

# Ejecutar migraciones
docker-compose exec php php bin/console doctrine:migrations:migrate
```

## Documentación API

La documentación del código está generada con phpDocumentor:

```
docs/api/index.html
```

Para regenerar la documentación:

```bash
docker-compose exec php vendor/bin/phpdoc -d src -t docs/api --title="API Contratos - Documentacion"
```

## Autor

Desarrollado con - Symfony 7.2 + PHP 8.3

