# CRUD de Usuarios - Laravel & MySQL Local

Sistema de gestión de usuarios con autenticación integrada para Administradores, cifrado de contraseñas, validación estricta de campos y un buscador en tiempo real optimizado para entornos locales.

## Requisitos Previos
- PHP >= 8.2 (con extensiones `php-mysql`, `php-curl`, `php-mbstring`, `php-xml`)
- Composer instalado
- MySQL Server 8.0+ / 9.0+

## Instrucciones de Instalación Local

1. **Clonar el repositorio y acceder a la carpeta:**
   ```bash
   git clone <URL_DE_TU_REPOSITORIO>
   cd mi-crud-user
   ```

2. **Instalar las dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar la Base de Datos:**
   Abre el archivo `.env` y edita las siguientes credenciales para que apunten a tu servidor local de MySQL:
   ```env
   DB_HOST=127.0.0.1
   DB_DATABASE=laravel_db
   DB_USERNAME=andres
   DB_PASSWORD=TuContraseñaAqui
   ```

5. **Ejecutar las Migraciones:**
   Crea la estructura de tablas necesaria en tu base de datos:
   ```bash
   php artisan migrate
   ```

6. **Crear el Usuario Administrador Inicial:**
   Accede a la consola interactiva para insertar el usuario necesario para el Login:
   ```bash
   php artisan tinker
   ```
   Dentro de Tinker, ejecuta el siguiente comando:
   ```php
   \App\Models\Cliente::create(['nombres'=>'Andres', 'apellidos'=>'Admin', 'correo'=>'admin@correo.com', 'cargo'=>'Director', 'tipo_usuario'=>'Administrador', 'password'=>\Illuminate\Support\Facades\Hash::make('123456')]);
   ```
   Escribe `exit` para salir.

7. **Ejecutar el Servidor Local:**
   ```bash
   php artisan serve --port=8002
   ```
   Accede desde tu navegador a: `127.0.0`

## Pruebas Automatizadas
Si deseas comprobar el estado del sistema, ejecuta:
```bash
php artisan test
```
