# SGSSi-Proyecto - Aplicación de Gestión de Juegos

Integrantes: Lou marine Gomez, David Mieguez, Diego Pomares e Iván Salazar

Aplicación web desarrollada con PHP y MySQL para gestionar un catálogo de juegos con sistema de autenticación de usuarios.

## Requisitos

- Docker
- Docker Compose

## Pasos para Lanzar el Proyecto

### 1. Clonar el repositorio

```bash
git clone https://github.com/davidmiguez/SGSSi-Proyecto.git
cd SGSSi-Proyecto
```

### 2. Construir las imágenes

```bash
bash build
```

### 3. Iniciar los contenedores

```bash
docker compose up
```

O en segundo plano:

```bash
docker compose up -d
```

### 4. Acceder a la aplicación

Abrir en el navegador:

```
http://localhost:81
```

### 5. Iniciar sesión

Usar las credenciales de prueba:
- **Usuario:** Juan
- **Contraseña:** 123

O registrarse como nuevo usuario desde el enlace en la página de login.

## Comandos Útiles

### Detener la aplicación

```bash
docker compose down
```

### Ver logs en tiempo real

```bash
docker compose logs -f
```

Ver logs solo del servidor web:

```bash
docker compose logs -f web
```

Ver logs solo de la base de datos:

```bash
docker compose logs -f db
```

### Reiniciar los contenedores

```bash
docker compose restart
```

### Resetear la base de datos

⚠️ **Advertencia:** Esto eliminará todos los datos.

```bash
docker compose down
sudo rm -rf mysql
docker compose up
```

### Acceder a la base de datos

```bash
docker compose exec db mysql -u root -proot database
```

Comandos útiles dentro de MySQL:

```sql
SHOW TABLES;
SELECT * FROM usuarios;
SELECT * FROM items;
EXIT;
```

### Reconstruir las imágenes

Si has modificado el Dockerfile o archivos de configuración:

```bash
docker compose down
docker compose build --no-cache
docker compose up
```

## Solución de Problemas

**Puerto 81 ocupado:**
Cambiar el puerto en `docker-compose.yml` (línea `81:80` por `8080:80`)

**Permisos denegados:**
```bash
sudo usermod -aG docker $USER
# Cerrar y volver a iniciar sesión
```

**Error de conexión a BD:**
```bash
docker compose restart
```

