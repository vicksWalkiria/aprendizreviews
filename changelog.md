# 📦 Changelog - Aprendiz Reviews

## [1.4] - 2025-09-13

### Añadido
- **Formulario frontend de reseñas**: Nuevo shortcode `[reviews_form]` para capturar reseñas directamente desde el frontend
  - Campos: Nombre, valoración con estrellas interactivas (1-5), selector de producto/servicio, texto de reseña
  - Sistema de estrellas visual con efectos hover y click para mejor experiencia de usuario
  - Título personalizable: `[reviews_form titulo="Tu mensaje personalizado"]`
- **Envío automático por email**: Notificación instantánea al administrador (vicks630@gmail.com) con:
  - Información completa de la reseña (nombre, producto, valoración con estrellas visuales, comentario)
  - Fecha y hora del envío
  - Enlace directo al panel de administración para validación rápida
  - Headers HTML personalizados con branding del sitio web
- **Procesamiento AJAX avanzado**: Envío de formularios sin recargar página con:
  - Validación frontend y backend en tiempo real
  - Feedback visual durante el envío ("Enviando...")
  - Mensajes de error y éxito dinámicos
  - Sistema de seguridad con nonces de WordPress
- **Interfaz de usuario mejorada**: 
  - Diseño responsive y profesional del formulario
  - Animaciones suaves entre formulario y mensaje de agradecimiento
  - Contenedor con sombras y bordes redondeados
  - Compatibilidad visual con todos los temas de WordPress

### Mejorado
- **Gestión de reseñas**: Las reseñas del frontend se guardan automáticamente como "Pendientes" para revisión manual
- **Base de datos**: Integración perfecta con el sistema existente de productos/servicios
- **Rendimiento**: Scripts y estilos se cargan únicamente en páginas que contienen el shortcode `[reviews_form]`
- **Accesibilidad**: Labels apropiados y navegación por teclado en el sistema de estrellas

### Técnico
- **Nuevo endpoint AJAX**: `submit_review_frontend` para procesamiento seguro de formularios
- **Detección automática de shortcode**: Carga condicional de recursos frontend
- **Compatibilidad**: jQuery y funciones nativas de WordPress (wp_mail, wp_verify_nonce)
- **Hooks implementados**: 
  - `wp_ajax_submit_review_frontend` (usuarios logueados)
  - `wp_ajax_nopriv_submit_review_frontend` (usuarios anónimos)
- **Validación robusta**: Sanitización completa de datos de entrada y verificación de productos activos

### Seguridad
- **Protección CSRF**: Sistema de nonces único por sesión
- **Sanitización de datos**: Todos los campos procesados con funciones nativas de WordPress
- **Validación de productos**: Verificación de existencia y estado activo antes del guardado
- **Rate limiting**: Protección natural contra spam mediante validación backend


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
