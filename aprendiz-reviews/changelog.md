# 📦 Changelog - Aprendiz Reviews

## [1.5] - 2025-09-14

### 🏗️ Arquitectura Completamente Reorganizada

**Reestructuración completa del plugin siguiendo patrones MVC y mejores prácticas de WordPress:**

#### Añadido
- **Estructura MVC profesional**: Separación completa entre Modelos, Vistas y Controladores
  - `models/`: Gestión de datos y operaciones de base de datos (Product, Review, Database)
  - `controllers/`: Lógica de negocio (Product Controller, Review Controller, AJAX Controller)  
  - `admin/partials/`: Vistas del panel de administración organizadas por funcionalidad
  - `public/partials/`: Vistas del frontend separadas por contexto
  - `templates/`: Templates reutilizables para shortcodes y emails

- **Sistema de carga inteligente**: 
  - Clase `Loader` centralizada para gestión de hooks y filtros
  - Carga condicional de assets CSS/JS solo en páginas que los necesitan
  - Detección automática de shortcodes para optimizar rendimiento

- **Clases especializadas**:
  - `Aprendiz_Reviews_Activator`: Instalación controlada con creación de tablas y migración
  - `Aprendiz_Reviews_Deactivator`: Limpieza temporal al desactivar plugin
  - `Aprendiz_Reviews_Admin`: Gestión completa del panel de administración  
  - `Aprendiz_Reviews_Public`: Funcionalidad frontend y shortcodes

- **Modelos de datos robustos**:
  - `Aprendiz_Reviews_Product`: CRUD completo para productos/servicios
  - `Aprendiz_Reviews_Review`: Gestión avanzada de reseñas con filtros y estadísticas
  - Métodos estáticos para consultas optimizadas y reutilización

#### Mejorado
- **Rendimiento**: Assets se cargan únicamente cuando el contenido los requiere
- **Mantenibilidad**: Código organizado en responsabilidades específicas  
- **Escalabilidad**: Arquitectura preparada para futuras funcionalidades
- **Seguridad**: Validación y sanitización centralizadas en controladores
- **Debugging**: Estructura clara facilita identificación y resolución de errores

#### Técnico
- **Autoloading optimizado**: Dependencias cargadas bajo demanda
- **Hooks organizados**: Separación entre admin y public hooks
- **Constantes definidas**: Paths y URLs centralizados para fácil mantenimiento
- **Compatibilidad**: Mantiene retrocompatibilidad total con versiones anteriores
- **PSR Standards**: Nombres de clases y métodos siguiendo estándares PHP

---

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

---

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

---

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

### Mejorado
- **Interface de administración**: Listado de reseñas con enlaces de edición directa.
- **Formulario de reseñas**: Validación mejorada y precarga de datos en modo edición.
- **Carrusel**: Configuración de autoplay personalizable desde el admin.

### Corregido
- **Schema compatibility**: Eliminado tipo Service no compatible con Rich Snippets.
- **Date handling**: Gestión correcta de fechas en formularios de edición.
- **Swiper configuration**: Aplicación correcta del delay de autoplay configurado.

---

## [1.1] - 2025-04-15

### Añadido
- **Media uploader** integrado para selección de avatares desde la biblioteca de medios.
- **Sistema de validación** manual de reseñas antes de mostrarlas públicamente.
- **Configuración de schema type**: Selector entre Product y LocalBusiness en ajustes.
- **Campos adicionales** en formulario de reseñas: avatar y estado de validación.

### Mejorado
- **Interface administrativa**: Diseño más intuitivo y organizado.
- **Gestión de reseñas**: Listado con opciones de validación y edición.
- **Compatibilidad**: Mejor integración con diferentes temas de WordPress.

### Corregido
- **Avatar display**: Visualización correcta de imágenes en carrusel.
- **Schema output**: Generación válida de structured data.
- **Admin styles**: Estilos consistentes en panel de administración.

---

## [1.0] - 2025-03-01

### Añadido
- **Versión inicial** del plugin Aprendiz Reviews.
- **Carrusel básico** de reseñas con integración de Swiper.js.
- **Panel de administración** para gestión de reseñas.
- **Shortcode** `[reviews]` para mostrar carrusel en frontend.
- **Schema.org básico** para SEO con tipo Product.
- **Campos básicos**: nombre, texto, valoración (1-5 estrellas), fecha.

### Técnico
- **Framework**: WordPress 5.0+
- **Frontend**: Swiper.js para carrusel responsive
- **Backend**: Panel admin nativo de WordPress
- **Base de datos**: Tabla personalizada para almacenar reseñas
- **SEO**: Schema básico compatible con motores de búsqueda

---

## 📋 Notas de Migración

### De 1.4 a 1.5
- **Automática**: El plugin detecta la migración y reorganiza automáticamente los archivos.
- **Compatibilidad**: Mantiene total retrocompatibilidad con shortcodes y datos existentes.
- **Rendimiento**: Mejora significativa en velocidad de carga con nueva arquitectura.

### De 1.3 a 1.4  
- **Migración automática**: Datos existentes se mantienen intactos.
- **Nuevas funcionalidades**: Formulario frontend disponible inmediatamente tras actualización.

### De 1.2 a 1.3
- **Migración de datos**: Reseñas existentes se asignan automáticamente al producto "General".
- **Nuevos shortcodes**: Los shortcodes existentes siguen funcionando, se añaden nuevos dinámicos.

---

## 🔄 Próximas Versiones

### En consideración
- **Integración con reseñas de WooCommerce**
- **Generación de reseñas con IA**
