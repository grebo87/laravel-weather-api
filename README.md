# Prueba Técnica API Laravel - Consulta de Clima y Gestión de Usuarios

Esta es una API desarrollada en Laravel como parte de una prueba técnica. Permite la gestión de usuarios (registro, autenticación), la consulta de datos climáticos desde WeatherAPI.com, y la gestión de ciudades favoritas y historial de búsquedas.

## Requisitos Previos

- Docker Desktop (o Docker Engine + Docker Compose)
- Composer (solo para un comando inicial)

## Instalación y Ejecución con Laravel Sail (Recomendado)

Laravel Sail proporciona un entorno de desarrollo Docker completo.

1.  **Clonar el Repositorio**
    ```bash
    git clone https://github.com/grebo87/laravel-weather-api.git
    cd laravel-weather-api
    ```

2.  **Configurar el Entorno**
    Copia el archivo de ejemplo de entorno y configúralo:
    ```bash
    cp .env.example .env
    ```
    Abre el archivo `.env` y **asegúrate de configurar las siguientes variables**:

    ```dotenv
    APP_NAME="Laravel Weather API"
    APP_ENV=local
    APP_KEY= # Se generará después
    APP_DEBUG=true
    APP_URL=http://localhost

    LOG_CHANNEL=stack
    LOG_DEPRECATIONS_CHANNEL=null
    LOG_LEVEL=debug

    # Configuración de Base de Datos para Sail (MySQL por defecto)
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel_weather_api # Puedes cambiar el nombre si lo deseas
    DB_USERNAME=sail
    DB_PASSWORD=password # Esta es la contraseña por defecto de Sail

    BROADCAST_DRIVER=log
    CACHE_DRIVER=file
    FILESYSTEM_DISK=local
    QUEUE_CONNECTION=sync
    SESSION_DRIVER=file
    SESSION_LIFETIME=120

    MEMCACHED_HOST=127.0.0.1

    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379

    MAIL_MAILER=smtp
    MAIL_HOST=mailpit
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS="hello@example.com"
    MAIL_FROM_NAME="${APP_NAME}"

    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    AWS_DEFAULT_REGION=us-east-1
    AWS_BUCKET=
    AWS_USE_PATH_STYLE_ENDPOINT=false

    PUSHER_APP_ID=
    PUSHER_APP_KEY=
    PUSHER_APP_SECRET=
    PUSHER_HOST=
    PUSHER_PORT=443
    PUSHER_SCHEME=https
    PUSHER_APP_CLUSTER=mt1

    VITE_APP_NAME="${APP_NAME}"
    VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    VITE_PUSHER_HOST="${PUSHER_HOST}"
    VITE_PUSHER_PORT="${PUSHER_PORT}"
    VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
    VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

    # IMPORTANTE: Configura tu API Key de WeatherAPI.com
    WEATHER_API_KEY=TU_API_KEY_AQUI_DE_WEATHERAPI
    WEATHER_API_BASE_URL=http://api.weatherapi.com/v1/current.json # O la URL que uses
    ```
    **Nota:** La base de datos (`DB_DATABASE`) se creará automáticamente por Sail si no existe.

3.  **Iniciar los Contenedores de Sail**
    Esto iniciará el servidor web, la base de datos MySQL, y otros servicios que hayas configurado con Sail.
    ```bash
    ./vendor/bin/sail up -d
    ```
    *(La primera vez, Docker descargará las imágenes necesarias, lo que puede tardar unos minutos).*

4.  **Generar Clave de Aplicación Laravel**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

5.  **Ejecutar Migraciones de Base de Datos**
    Esto creará las tablas necesarias en tu base de datos.
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

6.  **(Opcional pero Recomendado) Ejecutar Seeders**
    Esto poblará la base de datos con datos iniciales (ej. usuarios de prueba).
    ```bash
    ./vendor/bin/sail artisan db:seed
    ```

¡Listo! La API debería estar accesible en `http://localhost` (o el puerto que tu Docker exponga para el servicio web, usualmente el puerto 80 mapeado a tu localhost).

## Endpoints Principales de la API

Todos los endpoints están prefijados con `/api/v1`.

**Autenticación (No requieren token):**

*   `POST /api/v1/register` - Registrar un nuevo usuario.
    *   Body (JSON): `{"email": "test@gmail.com","first_name": "User","last_name": "Test","password": "password","password_confirmation": "password"}`
*   `POST /api/v1/login` - Iniciar sesión.
    *   Body (JSON): `{"email": "test@gmail.com", "password": "password"}`
    *   Respuesta incluye un `access_token` (Bearer token).

**Endpoints Protegidos (Requieren `Authorization: Bearer <TOKEN>` en la cabecera):**

*   `POST /api/v1/logout` - Cerrar sesión (invalida el token actual).
*   `GET /api/v1/user` - Obtener detalles del usuario autenticado.
*   `GET /api/v1/weather?city={nombre_ciudad}` - Obtener datos del clima para una ciudad.
    *   Ej: `/api/v1/weather?city=Yaguaraparo`
*   `GET /api/v1/favorites` - Listar ciudades favoritas del usuario.
*   `POST /api/v1/favorites` - Añadir una ciudad a favoritos.
    *   Body (JSON): `{"city_name": "Carupano"}`
*   `DELETE /api/v1/favorites/{favoriteCityId}` - Eliminar una ciudad de favoritos.
    *   Ej: `/api/v1/favorites/1`
*   `GET /api/v1/history?limit={opcional_limite}` - Listar historial de búsquedas de clima del usuario.
    *   Ej: `/api/v1/history` (por defecto muestra 10)
    *   Ej: `/api/v1/history?limit=5`

## Ejecución de Pruebas

Para ejecutar la suite completa de pruebas (Feature y Unit):
```bash
./vendor/bin/sail artisan test

```
Para ejecutar solo los Feature tests:
```bash
./vendor/bin/sail test --testsuite=Feature
```
Para ejecutar solo los Unit tests:
```bash
./vendor/bin/sail test --testsuite=Unit
```