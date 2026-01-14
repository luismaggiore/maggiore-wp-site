# 📘 Documentación Técnica – Proyecto Maggiore

## 1. Visión General del Proyecto

El proyecto **Maggiore** es un sitio WordPress desarrollado como **theme completamente personalizado**, orientado a una agencia de marketing digital que necesita:

- Un sistema de contenidos altamente interconectado.
- Construcción de reputación tanto para la agencia como para sus miembros.
- Navegación semántica entre clientes, servicios, proyectos, casos reales y personas.
- Soporte multilenguaje (Español principal, Inglés y Portugués).
- Backend editorial optimizado (sin depender de plugins de campos personalizados).

El sitio no se comporta como un WordPress tradicional de “páginas y posts”, sino como un **sistema editorial y reputacional**.

---

## 2. Custom Post Types (CPTs)

### 2.1 Cliente (`mg_cliente`)

**Propósito:** Representa empresas reales que han contratado servicios.

**Campos principales:**

- Logo (featured image)
- Descripción
- Servicios contratados (manual)
- Servicios contratados (automático, desde casos y portafolio)

**Relaciones:**

- Tiene muchos Casos de Éxito
- Tiene muchos Portafolios
- Tiene muchos Servicios

---

### 2.2 Caso de Éxito (`mg_caso_exito`)

**Propósito:** Relato estratégico de un proyecto exitoso (puede no tener entregables públicos).

**Campos principales:**

- Cliente (obligatorio)
- Servicios involucrados
- Miembros del equipo participantes

**Relaciones:**

- Pertenece a un Cliente
- Tiene muchos Servicios
- Tiene muchos Miembros del equipo
- Puede tener Portafolios asociados

---

### 2.3 Portafolio (`mg_portafolio`)

**Propósito:** Entregable concreto (visual, técnico o creativo).

**Campos principales:**

- Cliente (obligatorio)
- Servicio(s) aplicado(s)
- Caso de Éxito (opcional)
- Miembros del equipo participantes

**Relaciones:**

- Pertenece a un Cliente
- Pertenece a uno o más Servicios
- Puede pertenecer a un Caso de Éxito
- Tiene muchos Miembros del equipo

---

### 2.4 Equipo (`mg_equipo`)

**Propósito:** Personas reales que trabajan en la agencia.

**Campos principales:**

- Cargo
- Área
- Subárea
- Foto

**Relaciones (automáticas):**

- Casos de Éxito en los que participó
- Portafolios en los que participó
- Entradas de blog que escribió

> ⚠️ Importante:  
> El equipo **NO** se relaciona directamente con clientes.  
> Toda relación con clientes es indirecta vía Casos o Portafolio.

---

### 2.5 Servicio (`mg_servicio`)

**Propósito:** Oferta estructurada de la agencia.

**Campos principales:**

- Descripción
- Área a la que pertenece

**Relaciones:**

- Pertenece a un Área
- Es contratado por Clientes (automático)
- Es aplicado en Portafolios

---

### 2.6 Área (`mg_area`)

**Propósito:** Agrupación organizacional y estratégica (Creatividad, Performance, Data, etc.).

**Campos principales:**

- Director (opcional, miembro del equipo)
- Miembros del área (múltiples)

**Relaciones:**

- Tiene muchos Miembros del equipo
- Tiene muchos Servicios

> Nota:  
> Se decidió **NO mostrar servicios ni portafolios en el single del área** para evitar complejidad y fragilidad técnica.

---

### 2.7 Blog (`post`)

**Propósito:** Construcción de reputación, expertise y autoridad.

**Campos personalizados:**

- Autor del artículo (miembro del equipo, no usuario WP)

**Relaciones:**

- Cada post puede estar vinculado a un Miembro del equipo.
- El blog refuerza la reputación del equipo y viceversa.

---

## 3. Sistema de Relaciones Automáticas

Archivo clave:
/inc/helpers/auto-relations.php

### Principios:

- Ningún CPT es “master”.
- Las relaciones se propagan automáticamente al guardar contenido.
- Se evita duplicación manual.
- Se respeta el idioma (Polylang).

### Ejemplos:

- Si un Portafolio se asocia a un Servicio → el Cliente recibe ese Servicio automáticamente.
- Si un Portafolio tiene miembros → el Caso de Éxito hereda esos miembros.
- Si un Caso tiene miembros → esos miembros ven el caso en su perfil.
- Si un Post tiene autor → el miembro ve el post en su perfil.

---

## 4. Multilenguaje (Polylang Free)

### Estrategia:

- Cada CPT tiene su versión por idioma.
- Las relaciones **no cruzan idiomas**.
- Se usan helpers como:
  - `pll_get_post()`
  - `pll_get_post_language()`

### Reglas:

- Un post en español solo se relaciona con entidades en español.
- En frontend, siempre se traduce el ID antes de mostrar un link.

---

## 5. Templates Implementados

### Singles

- `single-mg_cliente.php`
- `single-mg_caso_exito.php`
- `single-mg_portafolio.php`
- `single-mg_equipo.php`
- `single-mg_servicio.php`
- `single-mg_area.php`
- `single.php` (blog)

Cada single:

- Muestra sus relaciones relevantes.
- Permite navegación cruzada.
- Usa cards coherentes.

---

### Archives

- `archive.php`
- `category.php`

Incluyen:

- Cards reutilizables
- Autor real del blog (miembro del equipo)
- Paginación

---

## 6. Metaboxes

Ubicación:
/inc/metaboxes/

Características:

- Modularizados por CPT
- Sin ACF
- Selectores con confirmación visual
- Separación entre:
  - Campos manuales
  - Campos automáticos (solo lectura)

Ejemplo:

- Autor del blog (`blog-autor.php`)
- Servicios del cliente
- Miembros del caso de éxito

---

## 7. Componentización (Pendiente)

### Template Parts

Se deben crear para mantener DRY:

/template-parts/cards/

card-cliente.php

card-caso-exito.php

card-portafolio.php

card-servicio.php

card-equipo.php

/template-parts/loops/

loop-caso-exito.php

loop-portafolio.php

loop-servicio.php

Beneficios:

- Coherencia visual
- Cambios rápidos
- Menos errores

---

## 8. Pendientes por Desarrollar

### Archivos de archivo con filtros

- `archive-mg_caso_exito.php`
- `archive-mg_portafolio.php`
- `archive-mg_servicio.php`

Con filtros por:

- Cliente
- Servicio
- Área
- Miembro del equipo

---

### Backend Editorial Avanzado

Objetivo: mejorar UX del admin.

Ideas:

- Ocultar editor y título donde no corresponda
- Reordenar metaboxes
- Crear UI jerarquizada por CPT
- Reemplazar editor por formularios semánticos

---

### Página “Quiénes Somos”

- Listado por áreas
- Director + equipo
- Preparada para multilenguaje

---

### Formulario de Contacto

- Servicios de interés
- Guardar como CPT o enviar email
- Integración futura con CRM

---

## 9. Filosofía del Sistema

- WordPress como **framework**, no como CMS básico.
- Contenido semántico, no duplicado.
- Relaciones reales, no taxonomías forzadas.
- Backend pensado para editores no técnicos.
- Frontend pensado para exploración y reputación.

---

## 10. Estado Actual

✅ Sistema funcional  
✅ Relaciones estables  
✅ Multilenguaje operativo  
🟡 Falta UX avanzada y archives  
🟡 Falta componentización  
🟡 Falta documentación visual (diagramas)

---

**Fin del documento.**
