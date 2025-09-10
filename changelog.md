# 📦 Changelog - Aprendiz Reviews

## [1.3] - 2025-09-10
### Añadido
- **Sistema completo de Productos/Servicios**: Gestión independiente de múltiples productos con shortcodes específicos.
  - Nueva tabla `productos_servicios` para almacenar información de cada producto/servicio.
  - Menú "Gestionar Productos/Servicios" con listado, edición y desactivación.
  - Formulario "Añadir Producto/Servicio" con campos: nombre, shortcode, tipo de schema, descripción, URL e imagen.
- **Shortcodes dinámicos**: Cada producto genera su propio shortcode (ej: `[reviews_general]`, `[reviews_mi_producto]`).
- **Schema JSON-LD optimizado**: Generación automática de structured data específico para cada tipo:
  - Soporte para `Product`, `LocalBusiness` y `Organization`.
  - Eliminado `Service` (no compatible con Google Rich Results).
  - Schema unificado con `aggregateRating` y array de `review` en un solo bloque.
- **Dashboard mejorado**: Estadísticas por producto y listado de shortcodes disponibles.
- **Sistema de filtros avanzado**: En "Gestionar Reseñas" con filtrado por producto y estado de validación.
- **Migración automática**: Las reseñas existentes se asocian automáticamente al producto "General".
- **Validación masiva**: Selección múltiple de reseñas para validar en lote.

### Mejorado
- **Formulario de reseñas**: Añadido selector desplegable para elegir producto/servicio de destino.
- **Gestión de reseñas**: Interfaz mejorada con información del producto asociado y filtros dinámicos.
- **Media uploader**: Corregido el funcionamiento del botón "Elegir imagen" en todas las páginas admin.
- **Estructura de menús**: Reorganización completa con menú principal y submenús específicos.
- **Swiper.js**: Optimizado para múltiples carruseles en la misma página.

### Corregido
- **Compatibilidad con Google**: Schema estructurado según especificaciones oficiales de Google Rich Results.
- **Errores de validación**: Eliminados warnings en Schema.org Structured Data Testing Tool.
- **JavaScript conflicts**: Botón de galería de medios funciona correctamente en formularios de admin.
- **Base de datos**: Índices optimizados y relaciones FK para mejor rendimiento.

### Técnico
- **Versión**: 1.3
- **Compatibilidad**: WordPress 5.0+
- **Base de datos**: Nueva tabla `wp_productos_servicios` con migración automática
- **Shortcodes**: Sistema dinámico con registro automático basado en productos activos

## [1.2] - 2025-05-30
### Añadido
- Opción en ajustes para definir los **segundos entre scrolls automáticos** del carrusel (autoplay delay).
- Aplicación dinámica del valor al inicializar el carrusel Swiper.
- Valor por defecto establecido en `3 segundos`.
- Campo para seleccionar **fecha de reseña** al crear una nueva, con la fecha actual precargada.
- Funcionalidad para **editar reseñas existentes** desde el listado de administración.
  - Botón ✏️ "Editar" junto a cada reseña.
  - Formulario de edición con campos precargados (nombre, texto, valoración, avatar y fecha).
- Sustituida la opción de tipo `Service` en el esquema de datos estructurados, ya que no se permiten fragmentos de reseñas.
