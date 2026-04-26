# Guía de Instalación Nativa — American School Backend

Guía para levantar el proyecto Laravel **sin Docker**, directamente en tu sistema operativo.

---

## Requisitos del Sistema

### PHP 8.2 o superior

Con las siguientes extensiones habilitadas:

| Extensión | Obligatoria | Propósito |
|-----------|-------------|-----------|
| `pdo` | Sí | Capa de abstracción de base de datos |
| `pdo_pgsql` | Sí (PostgreSQL) | Driver para PostgreSQL |
| `pdo_sqlite` | Sí (SQLite) | Driver para SQLite (alternativa local) |
| `mbstring` | Sí | Manejo de cadenas multibyte |
| `zip` | Sí | Descompresión de paquetes Composer |
| `bcmath` | Sí | Operaciones matemáticas de precisión |
| `curl` | Sí | Peticiones HTTP externas |
| `openssl` | Sí | Cifrado y sesiones |
| `tokenizer` | Sí | Análisis de código PHP |
| `ctype` | Sí | Validación de tipos de caracteres |
| `xml` | Sí | Procesamiento XML |
| `fileinfo` | Sí | Detección de tipos MIME |
| `intl` | Recomendada | Internacionalización |

### Otras dependencias

| Herramienta | Versión mínima | Propósito |
|-------------|----------------|-----------|
| **Composer** | 2.x | Gestor de dependencias PHP |
| **PostgreSQL** | 14+ | Base de datos (opción recomendada) |
| **Node.js** | 18+ | Compilación de assets frontend |
| **npm** | 9+ | Gestor de paquetes JavaScript |
| **Git** | cualquiera | Control de versiones |

> **Alternativa de BD:** Puedes usar SQLite para desarrollo local sin instalar PostgreSQL. Ver paso 4B.

---

## Instalación por Sistema Operativo

### Ubuntu / Debian

```bash
# 1. Actualizar paquetes
sudo apt update && sudo apt upgrade -y

# 2. Instalar PHP 8.2 y extensiones
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-fpm \
  php8.2-pdo php8.2-pgsql php8.2-sqlite3 \
  php8.2-mbstring php8.2-zip php8.2-bcmath \
  php8.2-curl php8.2-xml php8.2-fileinfo \
  php8.2-tokenizer php8.2-ctype php8.2-intl \
  php8.2-openssl unzip curl git

# 3. Verificar instalación de PHP
php -v

# 4. Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version

# 5. Instalar PostgreSQL (si usas opción A de base de datos)
sudo apt install -y postgresql postgresql-contrib
sudo systemctl start postgresql
sudo systemctl enable postgresql

# 6. Instalar Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v
```

### macOS (con Homebrew)

```bash
# 1. Instalar Homebrew si no lo tienes
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. Instalar PHP 8.2
brew install php@8.2
echo 'export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
php -v

# 3. Instalar Composer
brew install composer
composer --version

# 4. Instalar PostgreSQL (si usas opción A)
brew install postgresql@16
brew services start postgresql@16

# 5. Instalar Node.js
brew install node@18
node -v && npm -v
```

### Windows (con Laragon — recomendado)

> **Laragon** es la forma más sencilla en Windows. Incluye PHP, PostgreSQL, Nginx y más.

1. Descarga **Laragon Full** desde [laragon.org](https://laragon.org/download/)
2. Instala con todas las opciones por defecto
3. Inicia Laragon → clic en **Start All**
4. Verifica desde la terminal de Laragon: `php -v`, `composer -v`, `node -v`

---

## Configuración del Proyecto

### Paso 1 — Clonar el repositorio

```bash
git clone <URL_DEL_REPOSITORIO> american-school-backend
cd american-school-backend
```

### Paso 2 — Instalar dependencias PHP

```bash
composer install
```

### Paso 3 — Configurar el archivo de entorno

```bash
# Copia la plantilla de desarrollo
cp .env.dev.example .env

# Genera la clave de la aplicación
php artisan key:generate
```

Edita el archivo `.env` con tus datos reales (ver paso 4).

### Paso 4 — Configurar la base de datos

#### Opción A — PostgreSQL (recomendado, igual a producción)

```bash
# Entra a psql
sudo -u postgres psql

# Dentro de psql, crea la base de datos y el usuario
CREATE DATABASE american_school_dev;
CREATE USER american_user WITH PASSWORD 'tu_password';
GRANT ALL PRIVILEGES ON DATABASE american_school_dev TO american_user;
\q
```

Actualiza tu `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=american_school_dev
DB_USERNAME=american_user
DB_PASSWORD=tu_password
```

#### Opción B — SQLite (sin instalar PostgreSQL)

Crea el archivo de base de datos y actualiza el `.env`:

```bash
touch database/database.sqlite
```

```env
DB_CONNECTION=sqlite
# DB_DATABASE acepta ruta absoluta o deja vacío para usar database/database.sqlite
```

### Paso 5 — Ejecutar migraciones

```bash
php artisan migrate
```

### Paso 6 — Configurar el correo (Resend)

1. Crea una cuenta en [resend.com](https://resend.com)
2. Genera una API key desde el dashboard
3. Actualiza tu `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=2525
MAIL_USERNAME=resend
MAIL_PASSWORD=re_xxxxxxxxxxxxxxxxxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="onboarding@resend.dev"

NOTIFICATION_EMAIL=tu-correo@gmail.com
```

> En desarrollo puedes cambiar `MAIL_MAILER=log` para que los correos se guarden en `storage/logs/laravel.log` sin enviarlos realmente.

### Paso 7 — Compilar assets frontend

```bash
npm install
npm run build
```

### Paso 8 — Levantar el servidor de desarrollo

```bash
php artisan serve
```

La API estará disponible en: **http://localhost:8000**

---

## Comandos útiles del día a día

```bash
# Levantar servidor
php artisan serve

# Limpiar caché de configuración (obligatorio tras editar .env)
php artisan config:clear
php artisan cache:clear

# Crear una nueva migración
php artisan make:migration nombre_de_la_migracion

# Revertir y volver a correr migraciones (¡borra datos!)
php artisan migrate:fresh

# Ver rutas registradas
php artisan route:list

# Ejecutar el worker de colas (si usas jobs/queues)
php artisan queue:work

# Ejecutar tests
php artisan test
```

---

## Resolución de problemas

### `php: command not found`
PHP no está en el PATH. Verifica la instalación: `which php` o agrega al PATH según tu sistema.

### Error de extensión faltante (e.g., `pdo_pgsql`)
Instala la extensión correspondiente. En Ubuntu: `sudo apt install php8.2-pgsql`

### `SQLSTATE[08006] could not connect to server`
- Verifica que PostgreSQL esté corriendo: `sudo systemctl status postgresql`
- Confirma host, puerto, usuario y contraseña en el `.env`

### Error de permisos en `storage/` o `bootstrap/cache/`
```bash
chmod -R 775 storage bootstrap/cache
```

### `php artisan` no encuentra el `.env`
Asegúrate de haber copiado `.env.dev.example` a `.env` y de haber corrido `php artisan key:generate`.

---

## Estructura relevante del proyecto

```
backend/
├── app/
│   ├── Http/Controllers/   # Controladores de la API
│   ├── Mail/               # Clases de correo (Resend)
│   └── Models/             # Modelos Eloquent
├── database/
│   └── migrations/         # Migraciones de base de datos
├── routes/
│   └── api.php             # Endpoints de la API
├── .env.dev.example        # Plantilla de entorno para desarrollo local
├── .env.example            # Plantilla de entorno para producción
└── INSTALL.md              # Esta guía
```
