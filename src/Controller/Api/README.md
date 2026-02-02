# API Endpoints - Documentación Técnica

## Estructura del Proyecto API

Todos los endpoints están organizados en controladores API dedicados bajo `src/Controller/Api/`:

### Controladores Disponibles

#### 1. **AuthApiController.php**
- **POST /api/login** - Autenticación de usuario
- **POST /api/logout** - Cierre de sesión

#### 2. **UsuarioApiController.php** (CRUD de Usuarios)
- **POST /api/usuarios** - Registrar nuevo usuario (C - Create)
- **GET /api/usuarios/perfil** - Obtener mi perfil (R - Read)
- **GET /api/usuarios/{id}** - Ver perfil de otro usuario (R - Read)
- **PATCH /api/usuarios/{id}** - Actualizar usuario (U - Update)
- **DELETE /api/usuarios/{id}** - Eliminar usuario (D - Delete)

#### 3. **ChatApiController.php** (Gestión de Chats y Mensajes)
- **GET /api/home** - Información de inicio tras login
- **GET /api/general** - Obtener chat general (zona)
- **POST /api/general** - Enviar mensaje al chat general
- **POST /api/privado** - Crear/acceder a chat privado
- **POST /api/privado/salir** - Abandonar chat privado
- **POST /api/privado/cambiar-chat** - Cambiar chat activo
- **POST /api/mensaje** - Enviar mensaje a un chat
- **POST /api/invitar** - Invitar usuario a grupo
- **GET /api/actualizar** - Polling de novedades
- **GET /api/perfil** - Obtener perfil público
- **PATCH /api/perfil** - Actualizar perfil

#### 4. **AdminApiController.php** (Administración)
- **GET /api/admin/usuarios** - Listar todos los usuarios
- **GET /api/admin/usuarios/{id}/bloqueos** - Ver bloqueos de usuario
- **POST /api/admin/usuarios/{id}/bloquear** - Bloquear usuario
- **DELETE /api/admin/usuarios/{id}/desbloquear/{bloqueado_id}** - Desbloquear usuario
- **GET /api/admin/chats** - Listar todos los chats
- **GET /api/admin/chats/{id}/miembros** - Ver miembros de chat
- **DELETE /api/admin/chats/{id}/miembros/{usuario_id}** - Expulsar miembro
- **DELETE /api/admin/chats/{id}** - Eliminar chat

## Autenticación

Todos los endpoints (excepto login) requieren:
```
Header: Authorization
Valor: Bearer <token>
```

Donde `<token>` es el token devuelto en el login sin el prefijo `usr_tok_`.

## Estructura de Respuestas

Todas las respuestas JSON siguen este formato estándar:

### Respuesta Exitosa
```json
{
  "success": true,
  "message": "Descripción de la acción",
  "data": {
    // Datos específicos del endpoint
  }
}
```

### Respuesta de Error
```json
{
  "success": false,
  "message": "Descripción del error"
}
```

## Códigos HTTP

- **200** - OK (Solicitud exitosa)
- **201** - Created (Recurso creado)
- **400** - Bad Request (Faltan datos o son inválidos)
- **401** - Unauthorized (No autenticado)
- **403** - Forbidden (No autorizado)
- **404** - Not Found (Recurso no encontrado)
- **422** - Unprocessable Entity (Datos inválidos)
- **500** - Internal Server Error (Error del servidor)

## Métodos Auxiliares Compartidos

Cada controlador incluye métodos privados reutilizables:
- `getAuthenticatedUser()` - Obtiene usuario desde el token
- `extractTokenFromHeader()` - Extrae token del header Authorization
- `isAdmin()` - Verifica si es administrador

## Ejemplos de Uso en Postman

### 1. Registrar Usuario
```
POST /api/usuarios
Body (JSON):
{
  "email": "usuario@example.com",
  "password": "password123",
  "nombre": "Juan"
}
```

### 2. Login
```
POST /api/login
Body (JSON):
{
  "usuario": "usuario@example.com",
  "password": "password123"
}
```

Respuesta:
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user_token": "usr_tok_abc123...",
    "user_profile": {
      "email": "usuario@example.com",
      "nombre": "Juan",
      "estado": "online"
    }
  }
}
```

### 3. Obtener Mi Perfil (Autenticado)
```
GET /api/usuarios/perfil
Header: Authorization: Bearer abc123...
```

### 4. Bloquear Usuario
```
POST /api/admin/usuarios/5/bloquear
Header: Authorization: Bearer abc123...
Body (JSON):
{
  "usuario_bloqueado_id": 5
}
```

## Validaciones Incluidas

- ✅ Validación de campos requeridos
- ✅ Validación de tipos de datos
- ✅ Validación de permisos (solo propietario puede modificar su perfil)
- ✅ Validación de autenticación (token)
- ✅ Validación de enums (estados, tipos de chat)
- ✅ Prevención de auto-bloqueo
- ✅ Verificación de membresía en chats

## Características de Seguridad

1. **Autenticación por Token** - Cada usuario tiene un token único
2. **Autorización** - Solo propietarios pueden modificar sus datos
3. **Enums** - Validación de valores permitidos
4. **Manejo de Errores** - Respuestas consistentes para cada tipo de error

## Notas Importantes

- Los tokens incluyen el prefijo `usr_tok_` en la respuesta pero se usan sin el prefijo en el header
- Las fechas se devuelven en formato ISO 8601 (Y-m-d\TH:i:s\Z)
- El estado del usuario puede ser: `online`, `offline`, `ocupado`
- El tipo de chat puede ser: `general`, `privado`, `grupal`
