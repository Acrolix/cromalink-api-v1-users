# Documentación de Endpoints

### API de Usuarios

## Autenticación

### Login
- **URL:** `/api/auth/login`
- **Método:** `POST`
- **Descripción:** Inicia sesión en el sistema.
- **Parámetros:**
  - `email` (string): Correo electrónico del usuario.
  - `password` (string): Contraseña del usuario.

### Registro
- **URL:** `/api/auth/register`
- **Método:** `POST`
- **Descripción:** Registra un nuevo usuario en el sistema.
- **Parámetros:**
  - `name` (string): Nombre del usuario.
  - `email` (string): Correo electrónico del usuario.
  - `password` (string): Contraseña del usuario.

### Refresh Token
- **URL:** `/api/auth/refresh_token`
- **Método:** `POST`
- **Descripción:** Refresca el token de autenticación.
- **Parámetros:**
  - `refresh_token` (string): Token de refresco.

### Logout
- **URL:** `/api/auth/logout`
- **Método:** `POST`
- **Descripción:** Cierra la sesión del usuario.
- **Middleware:** `oauth`

### Verificación
- **URL:** `/api/auth/verify`
- **Método:** `GET`
- **Descripción:** Verifica el estado de autenticación del usuario.
- **Middleware:** `oauth`

## Usuarios

### Obtener Perfil de Usuario
- **URL:** `/api/users/me`
- **Método:** `GET`
- **Descripción:** Obtiene la información del perfil del usuario autenticado.
- **Middleware:** `oauth`

### Actualizar Perfil de Usuario
- **URL:** `/api/users`
- **Método:** `PUT`
- **Descripción:** Actualiza la información del perfil del usuario.
- **Middleware:** `oauth`
- **Parámetros:**
  - `name` (string): Nombre del usuario.
  - `email` (string): Correo electrónico del usuario.

### Deshabilitar Usuario
- **URL:** `/api/users/{id}/disable`
- **Método:** `PUT`
- **Descripción:** Deshabilita un usuario específico.
- **Middleware:** `oauth`
- **Parámetros:**
  - `id` (int): ID del usuario.

## Países

### Listar Países
- **URL:** `/api/countries`
- **Método:** `GET`
- **Descripción:** Obtiene una lista de todos los países.

### Obtener Información de un País
- **URL:** `/api/countries/{code}`
- **Método:** `GET`
- **Descripción:** Obtiene la información de un país específico.
- **Parámetros:**
  - `code`: ID del país.