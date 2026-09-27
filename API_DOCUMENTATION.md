# Documentacion de servicios - API BovinNet

Evidencia **GA7-220501096-AA5-EV03**. Todos los servicios responden en
formato `application/json`. La URL base, una vez desplegado el
proyecto en un servidor local, es:

```
http://localhost/bovinnet-api/public
```

---

## 1. POST /api/auth/login

Autentica a un usuario del sistema.

**Cuerpo de la peticion (JSON):**
```json
{
  "cedula": "1032456789",
  "contrasena": "claveDelUsuario"
}
```

**Respuesta 200 OK (credenciales validas):**
```json
{
  "token": "6f1c9a2e4b8d4f7a9c0e1b2d3f4a5b6c",
  "usuario": {
    "idUsuario": 1,
    "nombres": "Andres",
    "apellidos": "Vera",
    "idRol": 2
  }
}
```

**Respuesta 401 Unauthorized:** `{"mensaje": "Credenciales invalidas."}`
**Respuesta 400 Bad Request:** `{"mensaje": "Debe enviar cedula y contrasena."}`

---

## 2. GET /api/animales

Lista todos los animales registrados.

**Respuesta 200 OK:**
```json
[
  { "idAnimal": 1, "arete": "0248", "nombre": "Lucero", "sexo": "Hembra", "fechaNacimiento": "2022-03-10", "idLote": 2 },
  { "idAnimal": 2, "arete": "0312", "nombre": "Canela", "sexo": "Hembra", "fechaNacimiento": "2020-07-22", "idLote": 1 }
]
```

---

## 3. GET /api/animales/{id}

Consulta un animal especifico por su identificador.

**Ejemplo:** `GET /api/animales/1`

**Respuesta 200 OK:**
```json
{ "idAnimal": 1, "arete": "0248", "nombre": "Lucero", "sexo": "Hembra", "fechaNacimiento": "2022-03-10", "idLote": 2 }
```

**Respuesta 404 Not Found:** `{"mensaje": "No existe un animal con ese id."}`

---

## 4. POST /api/animales

Registra un nuevo animal.

**Cuerpo de la peticion (JSON):**
```json
{
  "arete": "0512",
  "nombre": "Estrella",
  "sexo": "Hembra",
  "fechaNacimiento": "2026-09-01",
  "idLote": 3
}
```

**Respuesta 201 Created:** el objeto del animal ya creado, incluyendo su `idAnimal`.
**Respuesta 400 Bad Request:** cuando falta `arete`, `sexo` o `idLote`, o el `sexo` no es "Hembra"/"Macho".

---

## 5. PUT /api/animales/{id}

Actualiza los datos de un animal existente. Recibe el mismo formato de
cuerpo que el servicio de creacion.

**Ejemplo:** `PUT /api/animales/5`

**Respuesta 200 OK:** el objeto del animal ya actualizado.
**Respuesta 404 Not Found:** si el id no existe.
**Respuesta 400 Bad Request:** si faltan campos obligatorios.

---

## 6. DELETE /api/animales/{id}

Elimina un animal del sistema.

**Ejemplo:** `DELETE /api/animales/5`

**Respuesta 200 OK:** `{"mensaje": "Animal eliminado correctamente."}`
**Respuesta 404 Not Found:** `{"mensaje": "No existe un animal con ese id."}`

---

## Codigos de estado HTTP usados en toda la API

| Codigo | Significado en esta API |
|---|---|
| 200 | Operacion exitosa (consulta, actualizacion o eliminacion). |
| 201 | Recurso creado exitosamente (alta de un animal). |
| 400 | La peticion no trae los datos obligatorios o son invalidos. |
| 401 | Credenciales de autenticacion incorrectas. |
| 404 | El recurso solicitado (o el id) no existe. |
| 500 | Error interno no controlado (por ejemplo, falla de conexion a la base de datos). |

## Artefactos de referencia usados para el diseno de estos servicios

- **Modelo relacional** de BovinNet (GA6-220501096-AA1-EV01): tablas
  `animal` y `usuario`.
- **Historias de usuario**: registro/consulta/edicion/baja de animales,
  e inicio de sesion de usuarios del sistema.
- **Estandar de codificacion PHP/PSR-12** (GA7-220501096-AA1-EV02),
  aplicado en la nomenclatura de clases, metodos y namespaces.
