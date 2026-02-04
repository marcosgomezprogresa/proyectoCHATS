# 📋 ENTREGA - PROYECTO CHAT

## Datos de Entrega

**Estudiante:** [Tu nombre]  
**Fecha:** Febrero 4, 2026  
**Proyecto:** Chat en Tiempo Real con Geolocalización  
**Versión:** 1.0.0

---

## 🌐 Aplicación Desplegada

### URL Pública
```
https://proyectochats-production.up.railway.app
```

### Credenciales de Prueba
```
Email:    admin@chat.com
Password: admin123
```

### Documentación de Endpoints
```
https://proyectochats-production.up.railway.app/endpoints
```

---

## 📚 Documentación Disponible

### En el Repositorio (GitHub)
1. **README.md** - Instrucciones de instalación y uso
2. **DEPLOYMENT.md** - Guía paso a paso de despliegue
3. **.env.example** - Variables de entorno requeridas
4. **TESTING_POSTMAN.md** - Colección Postman para testing

### En la Aplicación Web
- **/endpoints** - Documentación interactiva de 20+ endpoints
- **/home** - Página principal (requiere login)

---

## ✨ Características Implementadas

### 1. Autenticación
- ✅ Login con email y contraseña
- ✅ Registro de nuevos usuarios
- ✅ Tokens JWT con expiración
- ✅ Logout/invalidación de sesión

### 2. Chats
- ✅ Chat General (público para todos)
- ✅ Chats Privados (entre usuarios cercanos)
- ✅ Historial de mensajes
- ✅ Paginación de mensajes

### 3. Geolocalización
- ✅ Detección de ubicación del usuario
- ✅ Validación de distancia (máximo 5km)
- ✅ Lista de usuarios cercanos
- ✅ Cálculo de distancia en km

### 4. Administración
- ✅ Panel de admin (API)
- ✅ Gestión de usuarios
- ✅ Gestión de chats
- ✅ Sistema de bloqueos

### 5. Seguridad
- ✅ Encriptación de contraseñas (bcrypt)
- ✅ JWT authentication
- ✅ CSRF protection
- ✅ Control de acceso por roles

### 6. Base de Datos
- ✅ MySQL 8.0
- ✅ 7 tablas principales
- ✅ 3 migraciones automáticas
- ✅ Fixtures de datos de prueba

---

## 🏗️ Arquitectura Técnica

### Stack Tecnológico
- **Backend:** Symfony 7.2 (PHP 8.2+)
- **Base de Datos:** MySQL 8.0
- **Frontend:** Twig + JavaScript
- **Build:** Webpack Encore
- **Hosting:** Railway.app

### Estructura de Carpetas
```
proyectoChats/
├── src/
│   ├── Controller/
│   │   ├── HomeController.php        (Rutas web)
│   │   ├── SecurityController.php    (Login/Logout)
│   │   └── Api/                      (Endpoints REST)
│   │       ├── AuthApiController.php
│   │       ├── ChatApiController.php
│   │       ├── UsuarioApiController.php
│   │       └── AdminApiController.php
│   ├── Entity/                       (Modelos de datos)
│   ├── Repository/                   (Acceso a datos)
│   ├── Service/                      (Lógica de negocio)
│   └── Enum/                         (Enumeraciones)
├── config/                           (Configuración)
├── templates/                        (Vistas)
├── migrations/                       (Migraciones BD)
└── public/                           (Punto de entrada)
```

---

## 🔌 API REST - 20+ Endpoints

### Autenticación
- `POST /api/auth/login` - Login
- `POST /api/usuarios` - Registrar
- `POST /api/logout` - Logout

### Chat General
- `GET /api/general?page=1` - Ver mensajes
- `POST /api/general` - Enviar mensaje

### Chat Privado
- `POST /api/privado` - Crear/acceder
- `POST /api/mensaje` - Enviar mensaje
- `POST /api/privado/salir` - Abandonar chat

### Usuarios
- `GET /api/home` - Dashboard principal
- `GET /api/usuarios/perfil` - Mi perfil
- `GET /api/usuarios/{id}` - Ver perfil otro usuario
- `PATCH /api/usuarios/{id}` - Actualizar perfil
- `DELETE /api/usuarios/{id}` - Eliminar usuario

### Bloqueos
- `POST /api/admin/usuarios/{id}/bloquear` - Bloquear usuario
- `DELETE /api/admin/usuarios/{id}/desbloquear/{id}` - Desbloquear
- `GET /api/admin/usuarios/{id}/bloqueos` - Ver bloqueados

### Administración
- `GET /api/admin/usuarios` - Listar usuarios
- `GET /api/admin/chats` - Listar chats
- `GET /api/admin/chats/{id}/miembros` - Ver miembros
- `DELETE /api/admin/chats/{id}` - Eliminar chat

---

## 🗄️ Base de Datos

### Tablas
1. **User** - Usuarios del sistema
2. **Chat** - Chats (general/privados)
3. **UsuarioChat** - Relación usuario-chat
4. **Mensaje** - Mensajes enviados
5. **Bloqueo** - Usuarios bloqueados
6. **Invitacion** - Invitaciones a grupos
7. **doctrine_migration_versions** - Control de versiones

### Relaciones
- Usuario → 1:N Mensajes
- Usuario → M:N Chats (mediante UsuarioChat)
- Usuario → 1:N Bloqueos

---

## 🚀 Cómo Desplegar el Proyecto

### En tu máquina local
```bash
git clone https://github.com/tu-usuario/proyectoChats.git
cd proyectoChats
composer install
npm install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
npm run build
php -S localhost:8000 -t public/
```

### En servidor público (como se hizo)
1. Push a GitHub
2. Conectar con Railway/Render
3. Configurar DATABASE_URL y APP_SECRET
4. Auto-deploy en cada push

---

## 📊 Flujo de Uso

1. **Registro/Login** → Autenticarse con email y contraseña
2. **Home** → Ver chats activos y usuarios cercanos
3. **Chat General** → Enviar mensajes públicos
4. **Chat Privado** → Iniciar conversación con usuario cercano
5. **Bloqueos** → Bloquear usuarios si es necesario
6. **Admin** → Gestionar usuarios y chats (solo admin)

---

## 🔍 Testing

### Con Postman
1. Importar colección desde `postman_collection.json`
2. Obtener token con `/api/auth/login`
3. Usar token en Authorization header
4. Ejecutar requests de prueba

### Manualmente
1. Ir a https://proyectochats-production.up.railway.app
2. Hacer login
3. Ver `/endpoints` para documentación interactiva
4. Usar navegador DevTools para ver requests

---

## 📝 Requisitos Cumplidos

Según especificación de clase:

- ✅ **Aplicación CHAT funcionando** - Implementado y desplegado
- ✅ **Documentación de endpoints** - Página /endpoints con 20+ endpoints documentados
- ✅ **Subida a servidor público** - Desplegado en Railway.app
- ✅ **Base de datos restaurable** - Scripts y migraciones automáticas
- ✅ **Configuración .env** - .env.example con variables necesarias
- ✅ **Repositorio GitHub** - Código disponible en GitHub

---

## 🔗 Enlaces Importantes

| Recurso | URL |
|---------|-----|
| Aplicación | https://proyectochats-production.up.railway.app |
| Documentación API | https://proyectochats-production.up.railway.app/endpoints |
| GitHub | https://github.com/tu-usuario/proyectoChats |
| Railway Dashboard | https://railway.app |

---

## 📞 Contacto

Para soporte o preguntas sobre el proyecto:
- Email: [tu-email@ejemplo.com]
- GitHub Issues: https://github.com/tu-usuario/proyectoChats/issues

---

## 📄 Versión
**v1.0.0** - Febrero 2026

Proyecto completado según especificaciones de clase.
