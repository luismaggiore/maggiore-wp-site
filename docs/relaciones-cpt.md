# 📘 Relaciones entre CPTs — Theme Maggiore

## 🎯 Objetivo

Definir y documentar la lógica de interconexión entre los Custom Post Types (CPTs) del sitio de Maggiore Marketing, asegurando:

- Coherencia editorial y semántica
- Relaciones claras y no redundantes
- Automatización donde corresponde
- Escalabilidad futura

---

## 🧩 Custom Post Types (CPTs)

1. **Clientes** (`mg_cliente`)
2. **Casos de Éxito** (`mg_caso_exito`)
3. **Portafolio** (`mg_portafolio`)
4. **Equipo** (`mg_equipo`)
5. **Servicios** (`mg_servicio`)

---

## 🧠 Principios generales

- No existe un CPT “maestro”.
- Los **Servicios** y el **Equipo** no gestionan relaciones manuales.
- Las relaciones se definen desde:
  - `caso_de_exito`
  - `portafolio`
  - parcialmente desde `cliente`
- La información nunca se duplica innecesariamente.
- Las relaciones inversas se obtienen por consultas dinámicas.
- Todo servicio debe existir antes de poder ser asignado.

---

## 🟩 CPT: Clientes (`mg_cliente`)

### Campos personalizados

- Logo (imagen destacada)
- Descripción
- **Servicios contratados** (multi-select → `mg_servicio`)

### No tiene

- ❌ Miembros del equipo asignados directamente
- ❌ Portafolios asignados manualmente

### Relaciones automáticas

- Se muestran automáticamente:
  - Casos de éxito asociados
  - Portafolios asociados

### Auto-actualización

Cuando se guarda:

- Un **caso de éxito**
- Un **portafolio**

Si incluyen:

- Servicios que el cliente no tenía  
  → se agregan automáticamente a “Servicios contratados”.

---

## 🟨 CPT: Casos de Éxito (`mg_caso_exito`)

### Campos personalizados

- Cliente (select → `mg_cliente`) **obligatorio**
- Servicios involucrados (multi-select → `mg_servicio`)
- Miembros del equipo involucrados (multi-select → `mg_equipo`)
- Relato / storytelling
- Testimonio (opcional)
- Imagen destacada

### Lógica

- Representa un contenido editorial destacado.
- Puede existir sin portafolio visible.
- No lista portafolios directamente.

### Efecto al guardar

- Los servicios seleccionados se agregan al cliente si no estaban.
- Los miembros del equipo quedan relacionados indirectamente al cliente.

---

## 🟪 CPT: Portafolio (`mg_portafolio`)

### Campos personalizados

- Cliente (select → `mg_cliente`) **obligatorio**
- Caso de Éxito (select → `mg_caso_exito`) opcional
- Servicio asociado (select → `mg_servicio`) **obligatorio**
- Miembros del equipo participantes (multi-select → `mg_equipo`)
- Galería / multimedia
- Descripción del proyecto

### Lógica

- Todo portafolio pertenece a un cliente.
- Puede o no estar asociado a un caso de éxito.
- Es el principal punto de cruce operativo.

### Auto-asociaciones al guardar

- El servicio se agrega al cliente si no existía.
- El cliente queda relacionado al portafolio.
- Los miembros del equipo quedan relacionados al cliente y proyecto (por consulta).

---

## 🟦 CPT: Equipo (`mg_equipo`)

### Campos personalizados

- Cargo
- Área (ej: Creatividad)
- Subárea (ej: Audiovisual)
- Bio
- Foto (imagen destacada)

### No se edita manualmente

- ❌ Clientes
- ❌ Casos de éxito
- ❌ Portafolios

### Relaciones dinámicas

Se muestran automáticamente:

- Portafolios donde participó
- Casos de éxito donde participó
- Clientes para los que ha trabajado

---

## 🟥 CPT: Servicios (`mg_servicio`)

### Campos personalizados

- Área (texto, semántico)
- Descripción
- Icono (opcional)

### Características

- Se crean primero.
- No gestionan relaciones manuales.
- El área es solo coherencia semántica (por ahora).

### Relaciones dinámicas

- Clientes que lo contrataron
- Casos de éxito donde se utilizó
- Portafolios asociados

---

## 🔁 Reglas de Auto-Asociación

| Acción                    | Resultado                                   |
| ------------------------- | ------------------------------------------- |
| Guardar caso de éxito     | Servicios se agregan al cliente             |
| Guardar portafolio        | Servicios se agregan al cliente             |
| Portafolio con cliente    | Cliente muestra el proyecto automáticamente |
| Equipo en caso/portafolio | Equipo muestra clientes y proyectos         |
| Cliente                   | Nunca asigna equipo directamente            |

---

## 🧭 Visualización en Frontend

### Página de Cliente

- Datos del cliente
- Cards de casos de éxito (resumen + servicios)
- Cards de todos los portafolios del cliente
- Listado final de servicios contratados

### Página de Caso de Éxito

- Relato completo
- Servicios involucrados
- Equipo participante
- (opcional) proyectos relacionados

### Página de Portafolio

- Proyecto
- Cliente
- Caso de éxito (si existe)
- Servicio
- Equipo

### Página de Equipo

- Bio y rol
- Proyectos trabajados
- Clientes asociados
- Casos de éxito donde participó

---

## 🚀 Futuro (no implementado aún)

- Convertir “Área” en taxonomía compartida
- Crear páginas por área (`/area/creatividad`)
- Filtros cruzados por área / subárea
- Relación visual automática servicio ↔ equipo

---

## 📁 Ubicación recomendada
