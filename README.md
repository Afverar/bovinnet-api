# BovinNet API - Servicios web REST

Evidencia: **GA7-220501096-AA5-EV03** — Diseno y desarrollo de
servicios web, segun el componente formativo "Construccion de API".

## Descripcion

API REST en PHP 8 (sin framework, PSR-12) que expone los servicios
necesarios para que el frontend de BovinNet (HTML/CSS/JS) y su futura
app movil consuman la informacion de usuarios y animales sin acceder
directamente a la base de datos.

## Servicios expuestos

| Metodo | Ruta | Descripcion |
|---|---|---|
| POST | /api/auth/login | Autenticar un usuario |
| GET | /api/animales | Listar todos los animales |
| GET | /api/animales/{id} | Consultar un animal |
| POST | /api/animales | Registrar un animal |
| PUT | /api/animales/{id} | Actualizar un animal |
| DELETE | /api/animales/{id} | Eliminar un animal |

Ver `API_DOCUMENTATION.md` para el detalle de cada servicio (formato
de peticion, respuesta y codigos de estado HTTP).

## Artefactos de referencia usados

- Modelo relacional y script de base de datos (GA6-220501096).
- Estandar de codificacion PHP/PSR-12 (GA7-220501096-AA1-EV02).
- Modulo de consola/servlets previos (GA7-220501096-AA2-EV01/EV02),
  de donde se reutiliza la logica de negocio de `Animal`, ahora
  expuesta como servicios HTTP en lugar de una interfaz de consola o
  de formularios servidos por Servlets.

## Estructura del proyecto

```
proyecto/
├── composer.json
├── API_DOCUMENTATION.md
├── README.md
├── public/
│   ├── index.php        (enrutador / punto de entrada de la API)
│   └── .htaccess         (redirige todas las rutas a index.php)
└── src/
    ├── Config/Conexion.php
    ├── Model/Animal.php
    ├── Repository/{AnimalRepository,UsuarioRepository}.php
    └── Controller/{AnimalController,AuthController}.php
```

## Requisitos para ejecutar

- PHP 8.0 o superior con la extension PDO MySQL habilitada.
- Servidor Apache (con `mod_rewrite`) o el servidor embebido de PHP.
- Base de datos `bovinnet` creada con el script de la evidencia
  GA6-220501096-AA2-EV02/EV03.
- Composer (opcional, para regenerar el autoload si se agregan clases).

## Como ejecutar en local

Con el servidor embebido de PHP, desde la carpeta `public/`:

```
php -S localhost:8000
```

Y luego probar, por ejemplo:

```
curl http://localhost:8000/api/animales
```

## Control de versiones

Proyecto versionado con Git. Ver `enlace_repositorio.txt` para la
direccion del repositorio remoto.
