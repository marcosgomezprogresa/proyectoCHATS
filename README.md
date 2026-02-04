# Chat en Tiempo Real - Aplicación Symfony

Aplicación de chat geolocalizado desarrollada en Symfony 7.2 con soporte para chats privados, chat general y geolocalización de usuarios basada en proximidad.

## 🌐 Versión en Línea

**Aplicación desplegada:** https://proyectochats-production.up.railway.app

**Usuario de prueba:**
- Email: `admin@chat.com`
- Contraseña: `admin123`

**Documentación de API:** https://proyectochats-production.up.railway.app/endpoints

## 📋 Características

- **Autenticación JWT**: Sistema seguro de tokens JWT
- **Chats Privados**: Comunicación entre usuarios cercanos
- **Chat General**: Sala de chat pública para todos los usuarios
- **Geolocalización**: Detección de usuarios dentro de 5km
- **Bloques de Usuarios**: Bloquear usuarios no deseados
- **Panel de Administración**: API de admin para gestión del sistema
- **Base de Datos**: MySQL 8.0
- **API RESTful**: Endpoints completamente documentados

## 🚀 Despliegue Rápido

### Opción 1: En tu máquina local

#### Requisitos
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js 16+ (para webpack)

#### Pasos de instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/proyectoChats.git
cd proyectoChats
```

2. **Instalar dependencias**
```bash
composer install
npm install
```

3. **Configurar base de datos**
```bash
# Editar el archivo .env con tus datos de MySQL
# DATABASE_URL="mysql://usuario:contraseña@localhost:3306/proyectochats"

# Crear base de datos
php bin/console doctrine:database:create

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate

# Cargar datos de prueba (opcional)
php bin/console doctrine:fixtures:load
```

4. **Compilar assets**
```bash
npm run build
```

5. **Servir la aplicación**
```bash
php -S localhost:8000 -t public/
```

Accede a: `http://localhost:8000`

### Opción 2: Despliegue en servidor público (Render.com)

#### Paso 1: Preparar el proyecto para Heroku/Render

1. Crear `.env.production` con configuración de base de datos (ver abajo)
2. Asegurarse de que el `.gitignore` está correctamente configurado
3. Hacer push a GitHub

#### Paso 2: Crear cuenta en Render.com

1. Ir a https://render.com
2. Conectar con GitHub
3. Crear nuevo servicio "Web Service"
4. Seleccionar el repositorio `proyectoChats`

#### Paso 3: Configurar Render

**Build Command:**
```bash
composer install && npm install && npm run build && php bin/console doctrine:migrations:migrate
```

**Start Command:**
```bash
php -S 0.0.0.0:$PORT -t public/
```

**Environment Variables:**
```
APP_ENV=production
APP_SECRET=tu-secret-generado
DATABASE_URL=mysql://usuario:contraseña@host:3306/basedatos
DEFAULT_URI=https://tu-dominio.onrender.com
```

#### Paso 4: Crear base de datos MySQL

Recomendado: **PlanetScale** (compatible con MySQL)
1. Crear cuenta en https://planetscale.com
2. Crear base de datos
3. Obtener connection string
4. Usar en `DATABASE_URL` en Render

## 📚 Documentación de API

### Ver Endpoints Completos

Accede a: `/endpoints` (requiere estar autenticado en la interfaz)

### Autenticación

Todos los endpoints excepto login y registro requieren el header:
```
Authorization: Bearer <token-jwt>
```

### Endpoints Principales

#### Login
```
POST /api/auth/login
```

#### Registrar Usuario
```
POST /api/usuarios
```

#### Home (datos resumen)
```
GET /api/home
Headers: Authorization: Bearer <token>
```

#### Chat General
```
GET /api/general?page=1&limit=20
POST /api/general
Headers: Authorization: Bearer <token>
```

#### Chat Privado
```
POST /api/privado
Body: { "user_id_destino": 5 }
Headers: Authorization: Bearer <token>
```

#### Enviar Mensaje
```
POST /api/mensaje
Body: { "chat_token": "...", "mensaje": "..." }
Headers: Authorization: Bearer <token>
```

#### Admin - Listar Usuarios
```
GET /api/admin/usuarios
Headers: Authorization: Bearer <token-admin>
```

Para documentación completa de todos los 20+ endpoints, consulta la página de endpoints en la aplicación.

## 🗄️ Base de Datos

### Tablas Principales

- **User**: Usuarios del sistema
- **Chat**: Chats (general y privados)
- **UsuarioChat**: Relación usuario-chat
- **Mensaje**: Mensajes en los chats
- **Bloqueo**: Usuarios bloqueados
- **Invitacion**: Invitaciones a grupos
- **Migraciones**: Control de versiones de BD

### Restaurar Base de Datos

```bash
# Crear base de datos fresca
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create

# Ejecutar todas las migraciones
php bin/console doctrine:migrations:migrate

# Cargar datos de prueba
php bin/console doctrine:fixtures:load
```

## 🔧 Variables de Entorno

### Variables Requeridas en .env

```dotenv
###> Aplicación ###
APP_ENV=prod
APP_SECRET=tu-clave-secreta-aqui (generar con: symfony console secrets:generate-key)
DEFAULT_URI=https://tu-dominio.com

###> Base de Datos ###
# Formato: mysql://usuario:contraseña@host:puerto/basedatos
DATABASE_URL="mysql://root:password@localhost:3306/proyectochats?serverVersion=8.0&charset=utf8mb4"

###> JWT ###
JWT_SECRET=tu-secreto-jwt
JWT_ALGORITHM=HS256
JWT_EXPIRATION=3600
```

## 🧪 Testing

La colección de Postman incluye ejemplos de todas las API:

```bash
# Ver documentación Postman
cat TESTING_POSTMAN.md
```

## 📁 Estructura del Proyecto

```
proyectoChats/
├── src/
│   ├── Controller/          # Controladores (web y API)
│   ├── Entity/              # Entidades Doctrine
│   ├── Repository/          # Repositorios de datos
│   ├── Service/             # Servicios de negocio
│   └── Enum/                # Enumeraciones
├── config/                  # Configuración Symfony
├── templates/               # Vistas Twig
├── migrations/              # Migraciones de BD
├── public/                  # Punto de entrada web
├── assets/                  # Recursos JS/CSS
└── var/                     # Archivos generados
```

## 🔐 Seguridad

- Tokens JWT con expiración
- Encriptación de contraseñas
- Validación de geolocalización (5km)
- Control de acceso basado en roles (admin/user)
- CSRF protection

## 📞 Soporte

Para reportar errores o sugerencias, crea un issue en el repositorio de GitHub.

## 📄 Licencia

Proyecto educativo para clase.

---

**Versión:** 1.0.0  
**Última actualización:** Febrero 2026
