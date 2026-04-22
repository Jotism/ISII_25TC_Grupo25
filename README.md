# Anexo B – Manual de Instalación y Configuración del Sistema

## 1. Introducción

El presente documento describe los pasos necesarios para la instalación, configuración y puesta en marcha del sistema **GestAcad**, desarrollado como una aplicación web para la gestión académica.

## 2. Objetivo

Brindar una guía clara para que cualquier usuario con conocimientos básicos pueda configurar el entorno y ejecutar el sistema correctamente.

## 3. Requisitos Previos

### Software necesario

* PHP 8.x
* Servidor local (por ejemplo: XAMPP o similar)
* Git (para clonar el repositorio)
* Navegador web (Chrome, Edge, etc.)
* Acceso a internet (para conexión con Supabase)

### Extensiones de PHP requeridas

Es necesario habilitar las siguientes extensiones en el archivo `php.ini`:

* `extension=pgsql`
* `extension=pdo_pgsql`
* `extension=mbstring`
* `extension=intl`

Luego de activarlas, reiniciar el servidor Apache.

---

## 4. Instalación del Sistema

### Paso 1: Clonar el repositorio

Abrir una terminal y ejecutar:

```bash
git clone <URL_DEL_REPOSITORIO>
```

Luego ingresar a la carpeta del proyecto:

```bash
cd nombre-del-proyecto
```

---

### Paso 2: Configurar variables de entorno

Copiar el archivo `.env` provisto en el Drive del proyecto dentro de la raíz del sistema.

Este archivo contiene la configuración de conexión a la base de datos (Supabase).

Verificar que tenga datos similares a:

```env
database.default.hostname = aws-1-sa-east-1.pooler.supabase.com
database.default.database = postgres
database.default.username = TU_USUARIO
database.default.password = TU_PASSWORD
database.default.DBDriver = Postgre
database.default.port = 5432
```

---

### Paso 3: Configuración de CodeIgniter

Verificar en el archivo:

```
app/Config/App.php
```

Que la URL base sea:

```php
public string $baseURL = 'http://localhost:8080/';
```

---

### Paso 4: Ejecutar el servidor

Desde la raíz del proyecto ejecutar:

```bash
php spark serve
```

El sistema quedará disponible en:

```
http://localhost:8080
```

---

## 5. Uso del Sistema

### Registro de usuario

* Acceder a `/auth/registro`
* Completar los datos requeridos
* El sistema registra automáticamente como **Alumno**

### Inicio de sesión

* Acceder a `/auth/login`
* Ingresar email y contraseña

### Roles

* Los roles se gestionan directamente desde la base de datos:

  * `1 = Admin`
  * `2 = Alumno`

---

## 6. Consideraciones

* La base de datos se encuentra alojada en Supabase (PostgreSQL).
* La asignación de carrera a un alumno se realiza manualmente desde la base de datos.
* El sistema utiliza arquitectura MVC (Modelo-Vista-Controlador).
* No requiere instalación de base de datos local.

---

## 7. Solución de Problemas

### Error de conexión a base de datos

* Verificar que el archivo `.env` esté correctamente configurado
* Confirmar que las extensiones `pgsql` y `pdo_pgsql` estén habilitadas

### Pantalla en blanco o error general

* Revisar logs en:

```
writable/logs/
```

### Error de encoding (utf8mb4)

* Asegurarse de usar PostgreSQL (no MySQL)
* Configurar correctamente el driver en `.env`

---

## 8. Estado del Sistema

El sistema se encuentra en una versión inicial que incluye:

* Registro de usuarios
* Inicio de sesión
* Consulta de materias disponibles
