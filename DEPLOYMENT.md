# Guía de Despliegue - Aplicación Chat

## 📋 Resumen

Esta guía te muestra cómo desplegar la aplicación Chat en un servidor público gratuito.

## 🚀 Opción 1: Despliegue en Render.com (RECOMENDADO)

### Paso 1: Preparar el repositorio GitHub

```bash
# Ir al directorio del proyecto
cd proyectoChats

# Inicializar git (si no está hecho)
git init
git add .
git commit -m "Initial commit: Chat app ready for deployment"

# Crear repositorio en GitHub
# Ir a https://github.com/new
# Crear repositorio llamado "proyectoChats"

# Subir el código
git remote add origin https://github.com/TU-USUARIO/proyectoChats.git
git branch -M main
git push -u origin main
```

### Paso 2: Crear servicio en Render.com

1. Ir a https://render.com
2. Hacer clic en "New +" → "Web Service"
3. Conectar con GitHub (autorizar acceso)
4. Seleccionar repositorio `proyectoChats`

### Paso 3: Configurar el servicio

En el formulario de Render, completar:

**Name:** `chat-app` (o el nombre que prefieras)

**Environment:** `php`

**Build Command:**
```bash
composer install && npm install && npm run build
```

**Start Command:**
```bash
php bin/console doctrine:migrations:migrate --no-interaction && php -S 0.0.0.0:${PORT:-8080} -t public -r public/index.php
```

**Environment Variables:**
```
APP_ENV=production
APP_SECRET=<Generar una clave segura>
DEFAULT_URI=https://chat-app.onrender.com
DATABASE_URL=mysql://usuario:contraseña@host:3306/chat_app?serverVersion=8.0&charset=utf8mb4
JWT_SECRET=<Generar una clave segura>
```

### Paso 4: Configurar Base de Datos

**Opción A: PlanetScale (RECOMENDADO - Gratuito)**

1. Ir a https://planetscale.com
2. Crear cuenta
3. Crear base de datos nueva
4. Copiar la connection string (formato MySQL)
5. Pegar en `DATABASE_URL` de Render

**Opción B: Elephant SQL (PostgreSQL)**
1. Ir a https://www.elephantsql.com
2. Crear instancia gratuita
3. Copiar connection string
4. Adaptar a MySQL si es necesario

### Paso 5: Deploy

1. Hacer clic en "Create Web Service" en Render
2. Esperar a que complete el build (5-10 minutos)
3. Cuando esté listo, verás la URL pública

## 🚀 Opción 2: Despliegue en Railway.app

### Paso 1: Preparar repositorio GitHub (igual que arriba)

### Paso 2: Crear proyecto en Railway

1. Ir a https://railway.app
2. Hacer clic en "New Project"
3. Seleccionar "Deploy from GitHub repo"
4. Conectar con GitHub
5. Seleccionar `proyectoChats`

### Paso 3: Configurar variables de entorno

En el dashboard de Railway:

```
APP_ENV=production
APP_SECRET=<generar>
DEFAULT_URI=https://tu-app.railway.app
DATABASE_URL=mysql://...
JWT_SECRET=<generar>
```

Railway detectará automáticamente que es PHP y ejecutará el Procfile.

## 🚀 Opción 3: Despliegue en Heroku (Requiere tarjeta de crédito)

### Paso 1: Instalación de Heroku CLI

```bash
# En Windows (PowerShell):
choco install heroku-cli

# En Mac:
brew tap heroku/brew && brew install heroku

# En Linux:
curl https://cli-assets.heroku.com/install.sh | sh
```

### Paso 2: Login y crear app

```bash
heroku login
heroku create chat-app-tuusuario
```

### Paso 3: Agregar base de datos

```bash
heroku addons:create cleardb:ignite
```

### Paso 4: Deploy

```bash
git push heroku main
```

## 📱 Verificar que funciona

Después del despliegue:

1. Ir a la URL pública de tu app
2. Hacer login con:
   - Email: `admin@chat.com`
   - Contraseña: `admin123`

3. Acceder a `/endpoints` para ver documentación de API

## 🔧 Comandos útiles

```bash
# Ver logs en Render
# (desde el dashboard de Render)

# Ver logs en Railway
railway logs

# Ver logs en Heroku
heroku logs --tail

# Ejecutar migraciones manualmente
heroku run php bin/console doctrine:migrations:migrate

# Ver variables de entorno
heroku config
```

## 🚨 Solución de problemas

### Error: "Database connection failed"
- Verificar que `DATABASE_URL` esté correctamente configurado
- Verificar credenciales de base de datos
- Ejecutar manualmente: `php bin/console doctrine:migrations:migrate`

### Error: "Port already in use"
- Railway y Render manejan los puertos automáticamente
- No modificar el puerto en el Start Command

### Archivos no se suben correctamente
- Verificar `.gitignore` no excluye archivos necesarios
- Hacer `git add -f archivo` si es necesario forzar

### Base de datos vacía
- Ejecutar fixtures: `php bin/console doctrine:fixtures:load`
- O crear datos manualmente a través de la API

## 📊 Monitoreo

- **Render**: Panel de Render muestra logs, métricas
- **Railway**: Dashboard en tiempo real
- **Heroku**: `heroku metrics` para métricas

## 🔐 Seguridad en Producción

Asegúrate de:
- [ ] Cambiar `APP_SECRET` a algo único
- [ ] Usar contraseña fuerte en BD
- [ ] No compartir credenciales en Git (usar .gitignore)
- [ ] Configurar HTTPS (automático en Render/Railway)
- [ ] Revisar logs regularmente

## 📞 URLs Importantes

- **Render**: https://render.com
- **Railway**: https://railway.app  
- **Heroku**: https://www.heroku.com
- **PlanetScale**: https://planetscale.com

---

¿Preguntas? Revisa la documentación oficial de cada plataforma.
