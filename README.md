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

## Características de Seguridad

### Sistema de Bloqueo por Intentos Fallidos

La aplicación incluye protección contra ataques de fuerza bruta con **doble capa de seguridad**:

#### 1. Bloqueo por IP (Protección Global)
- **Protege contra usuarios inexistentes:** Incluso si intentas con usuarios que no existen
- **5 intentos máximos por IP:** Cualquier IP que falle 5 veces se bloquea
- **Bloqueo de 1 minuto:** 60 segundos de espera obligatoria
- **Previene enumeración de usuarios:** No puedes saber si un usuario existe o no

#### 2. Bloqueo por Usuario (Protección de Cuenta)
- **5 intentos máximos por cuenta:** Si el usuario existe, se cuenta aparte
- **Bloqueo de 1 minuto:** Protección adicional para la cuenta específica
- **Contador visible:** Muestra cuántos intentos quedan
- **Reseteo al login exitoso:** Se limpian todos los contadores

#### Cómo Funciona
1. Cualquier intento fallido incrementa contador de IP
2. Si el usuario existe, también incrementa su contador
3. Al 5º fallo de la IP: Bloqueo por IP (no importa el usuario)
4. Al 5º fallo del usuario: Bloqueo adicional de la cuenta
5. Login exitoso: Resetea ambos contadores

**Ejemplo:**
- Intentas 5 veces con usuario "admin" (no existe) → IP bloqueada
- Intentas 5 veces con usuario "Juan" (existe, contraseña mal) → IP bloqueada + cuenta bloqueada
- Login correcto → Todo se resetea

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

