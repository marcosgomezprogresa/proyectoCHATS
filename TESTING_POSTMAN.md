# Guía Rápida - Testing en Postman

## Pasos para Probar la API

### 1. Registrar un Usuario Nuevo

**Endpoint:** `POST /api/usuarios`

```json
{
  "email": "prueba@example.com",
  "password": "password123",
  "nombre": "Usuario Prueba"
}
```

**Respuesta Esperada:** 201 Created
```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "user_token": "usr_tok_XXXXX...",
    "usuario": {
      "email": "prueba@example.com",
      "nombre": "Usuario Prueba",
      "estado": "online",
      "fecha_registro": "2024-01-16T14:30:00Z"
    }
  }
}
```

**Guardar:** El token en una variable de Postman llamada `TOKEN` (sin el prefijo `usr_tok_`)

---

### 2. Login

**Endpoint:** `POST /api/login`

```json
{
  "usuario": "prueba@example.com",
  "password": "password123"
}
```

**Respuesta Esperada:** 200 OK
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user_token": "usr_tok_XXXXX...",
    "user_profile": {
      "email": "prueba@example.com",
      "nombre": "Usuario Prueba",
      "estado": "online"
    }
  }
}
```

---

### 3. Obtener Mi Perfil

**Endpoint:** `GET /api/usuarios/perfil`

**Header:** `Authorization: Bearer {{TOKEN}}`

**Respuesta Esperada:** 200 OK
```json
{
  "success": true,
  "message": "Perfil obtenido exitosamente",
  "data": {
    "usuario_id": 1,
    "email": "prueba@example.com",
    "nombre": "Usuario Prueba",
    "estado": "online",
    "avatar_url": null,
    "ultima_actividad": "2024-01-16T14:30:00Z",
    "puedo_chatear": true
  }
}
```

---

### 4. Actualizar Perfil

**Endpoint:** `PATCH /api/usuarios/{id}`

**Header:** `Authorization: Bearer {{TOKEN}}`

```json
{
  "nombre": "Usuario Nuevo Nombre",
  "estado": "ocupado"
}
```

---

### 5. Bloquear un Usuario

Primero registra otro usuario para obtener su ID, luego:

**Endpoint:** `POST /api/admin/usuarios/{tu_id}/bloquear`

**Header:** `Authorization: Bearer {{TOKEN}}`

```json
{
  "usuario_bloqueado_id": 2
}
```

---

### 6. Ver Bloqueos

**Endpoint:** `GET /api/admin/usuarios/{id}/bloqueos`

**Header:** `Authorization: Bearer {{TOKEN}}`

---

## Errores Comunes y Soluciones

| Error | Causa | Solución |
|-------|-------|----------|
| 401 Unauthorized | Token no válido o no enviado | Verificar que el token está en el header con formato `Bearer <token>` |
| 400 Bad Request | Faltan campos | Verificar que se envían todos los campos requeridos |
| 404 Not Found | Usuario o recurso no existe | Verificar que el ID es correcto |
| 422 Unprocessable Entity | Datos inválidos (ej: email duplicado) | Verificar formato y valores |

## Variables de Postman Recomendadas

```
TOKEN = <token_del_usuario_sin_prefijo>
ADMIN_TOKEN = <token_del_admin_sin_prefijo>
BASE_URL = http://localhost
```

## Importar Colección en Postman

1. En Postman: `File` → `Import`
2. Seleccionar archivo `postman_collection.json`
3. Los endpoints estarán organizados por categorías
4. Reemplazar `{{TOKEN}}` con tu token real

## Flujo Típico de Testing

1. **Registrar Usuario** → Copiar token
2. **Login** → Verificar token
3. **Obtener Perfil** → Ver datos
4. **Actualizar Perfil** → Cambiar nombre
5. **Obtener Perfil Nuevamente** → Verificar cambios
6. **Bloquear Usuario** → Crear bloqueo
7. **Ver Bloqueos** → Verificar que aparece
8. **Logout** → Cerrar sesión

## URLs Base

- **Desarrollo Local:** `http://localhost/proyectoChats/public/api`
- **Producción:** Será configurado por el profesor

## Notas Importantes

- Todos los tokens incluyen el prefijo `usr_tok_` en la respuesta
- Al usar en el header `Authorization`, usar solo la parte sin el prefijo
- Las fechas están en formato ISO 8601
- Los enumerados tienen valores específicos (validados en el backend)
