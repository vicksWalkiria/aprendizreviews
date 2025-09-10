# Aprendiz Reviews

**Versión actual: 1.3**
Un carrusel de reseñas avanzado y escalable para WordPress, creado por [Aprendiz de SEO](https://aprendizdeseo.top/).

![Captura del plugin Aprendiz Reviews](https://aprendizdeseo.top/wp-content/uploads/2025/05/plugin-reviews.jpg)

## 📝 Descripción

**Aprendiz Reviews** es un plugin profesional y escalable que te permite:

- **Gestión multi-producto**: Crear múltiples productos/servicios con shortcodes independientes
- **Schema optimizado**: JSON-LD compatible con Google Rich Results (Product, LocalBusiness, Organization)
- **Validación avanzada**: Sistema de aprobación con filtros por producto y estado
- **Dashboard profesional**: Estadísticas completas y gestión centralizada
- **Shortcodes dinámicos**: Cada producto genera su propio shortcode único
- **Migración automática**: Actualización transparente desde versiones anteriores

Ideal para negocios, profesionales y webs que necesiten mostrar testimonios organizados por productos/servicios sin depender de plataformas externas.

## ⚙️ Instalación

1. Sube la carpeta del plugin a `/wp-content/plugins/aprendiz_reviews/`.
2. Activa el plugin desde el panel de administración de WordPress.
3. Accede al menú **Aprendiz Reviews** en el admin para comenzar.
4. Crea productos/servicios en **"Añadir Producto/Servicio"**.
5. Añade reseñas en **"Añadir Reseña"** y asígnalas a productos específicos.
6. Usa los shortcodes generados (ej: `[reviews_general]`, `[reviews_mi_producto]`).

## 🎯 Funcionalidades principales

### **Sistema multi-producto**

- Gestión independiente de productos/servicios
- Shortcodes únicos para cada producto
- Schema JSON-LD específico por tipo


### **Interface administrativa**

- Dashboard con estadísticas y shortcodes disponibles
- Formularios de productos/servicios con campos completos
- Gestión de reseñas con filtros avanzados
- Validación masiva de reseñas pendientes


### **SEO optimizado**

- Schema estructurado compatible con Google Rich Results
- Soporte para Product, LocalBusiness, Organization
- AggregateRating y Reviews integrados correctamente


## ❓ Preguntas frecuentes

### ¿Puedo tener reseñas para diferentes productos?

Sí. La versión 1.3 incluye un sistema completo de productos/servicios. Cada uno tiene su propio shortcode y muestra solo sus reseñas específicas.

### ¿Puedo enviar reseñas desde el frontend?

No por ahora. Actualmente solo los administradores pueden añadir reseñas desde el backend, aunque es fácilmente ampliable.

### ¿Puedo personalizar el diseño?

Sí. El plugin incluye estilos base que puedes sobrescribir con tu CSS en el tema activo.

### ¿Qué pasa con mis reseñas actuales al actualizar?

La migración es automática. Todas las reseñas existentes se asignan al producto "General" con shortcode `[reviews_general]`.

## 📸 Capturas

1. **Dashboard**: Estadísticas y shortcodes disponibles
2. **Gestionar Productos/Servicios**: Listado con edición y desactivación
3. **Añadir Producto/Servicio**: Formulario completo con schema types
4. **Gestionar Reseñas**: Filtros por producto y validación masiva
5. **Formulario de reseñas**: Selector de producto/servicio de destino
6. **Carrusel frontend**: Reseñas específicas por producto

## 🧪 Compatibilidad

- **Requiere WordPress:** 5.0 o superior
- **Testado hasta:** 6.5
- **Licencia:** [GPLv2 o superior](https://www.gnu.org/licenses/gpl-2.0.html)
- **Schema support**: Product, LocalBusiness, Organization
- **Compatible con**: Google Rich Results, Schema.org validation


## 🚀 Changelog

### [1.3] - 2025-09-10

#### Añadido

- **Sistema multi-producto**: Gestión completa de productos/servicios independientes
- **Shortcodes dinámicos**: Cada producto genera su shortcode único
- **Schema JSON-LD optimizado**: Compatible con Google Rich Results
- **Dashboard profesional**: Estadísticas y listado de shortcodes
- **Filtros avanzados**: Gestión de reseñas por producto y estado
- **Migración automática**: Asignación transparente de reseñas existentes
- **Validación masiva**: Aprobación múltiple de reseñas pendientes


#### Corregido

- **Media uploader**: Botón "Elegir imagen" funciona correctamente
- **Schema validation**: Eliminados errores en Google Structured Data Testing
- **JavaScript conflicts**: Optimización de carga de dependencias
- **Database performance**: Índices y relaciones optimizadas


### [1.2] - 2025-05-30

#### Añadido

- Opción en ajustes para definir segundos entre scrolls automáticos
- Campo para seleccionar fecha de reseña al crear una nueva
- Funcionalidad para editar reseñas existentes desde listado
- Eliminación de tipo `Service` por incompatibilidad con Google Rich Results


## 💬 Soporte y comunidad

¿Tienes sugerencias, errores o simplemente quieres mejorar el plugin?

Únete al canal de Telegram 👉 [https://t.me/+mo0aLMYaE6s4ZDc0](https://t.me/+mo0aLMYaE6s4ZDc0)

## 🛠️ Para desarrolladores

### Shortcodes dinámicos

```php
[reviews_general]        // Producto por defecto
[reviews_mi_producto]    // Producto personalizado
[reviews_servicio_seo]   // Otro producto personalizado
```


### Schema types soportados

- **Product**: Para productos físicos o digitales
- **LocalBusiness**: Para negocios locales
- **Organization**: Para organizaciones y empresas


### Hooks disponibles

```php
// Personalizar schema antes de mostrar
add_filter('aprendiz_reviews_schema', 'mi_funcion_schema', 10, 2);

// Modificar reseñas antes de mostrar  
add_filter('aprendiz_reviews_items', 'mi_funcion_resenas', 10, 2);
```


## ❤️ Créditos

Desarrollado por [Aprendiz de SEO](https://aprendizdeseo.top/)
Apóyame aquí: [https://aprendizdeseo.top/](https://aprendizdeseo.top/)

***

**Convierte tu web en una máquina de generar confianza con reseñas organizadas y optimizadas para SEO** ⭐⭐⭐⭐⭐
<span style="display:none">[^1]</span>

<div style="text-align: center">⁂</div>

[^1]: https://aprendizdeseo.top

