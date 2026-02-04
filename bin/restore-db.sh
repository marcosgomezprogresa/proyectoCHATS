#!/bin/bash

# Script para restaurar la base de datos
# Uso: ./bin/restore-db.sh

set -e

echo "=========================================="
echo "   Restaurar Base de Datos - Chat App"
echo "=========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para imprimir con color
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# 1. Eliminar base de datos actual
echo ""
echo "Paso 1: Eliminando base de datos actual..."
php bin/console doctrine:database:drop --force --if-exists --env=prod 2>/dev/null || php bin/console doctrine:database:drop --force --if-exists 2>/dev/null || true
print_status "Base de datos eliminada"

# 2. Crear nueva base de datos
echo ""
echo "Paso 2: Creando nueva base de datos..."
php bin/console doctrine:database:create --env=prod 2>/dev/null || php bin/console doctrine:database:create 2>/dev/null
print_status "Base de datos creada"

# 3. Ejecutar migraciones
echo ""
echo "Paso 3: Ejecutando migraciones..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod 2>/dev/null || php bin/console doctrine:migrations:migrate --no-interaction 2>/dev/null
print_status "Migraciones ejecutadas"

# 4. Cargar fixtures (datos de prueba)
echo ""
echo "Paso 4: Cargando datos de prueba..."
php bin/console doctrine:fixtures:load --no-interaction --env=prod 2>/dev/null || php bin/console doctrine:fixtures:load --no-interaction 2>/dev/null
print_status "Datos de prueba cargados"

echo ""
echo "=========================================="
echo -e "${GREEN}✓ Base de datos restaurada exitosamente${NC}"
echo "=========================================="
echo ""
echo "Usuarios de prueba disponibles:"
echo "  Email: admin@chat.com"
echo "  Password: admin123"
echo ""
