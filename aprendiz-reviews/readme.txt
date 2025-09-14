=== Aprendiz Reviews ===
Contributors: aprendizdeseo
Donate link: https://aprendizdeseo.top/
Tags: reviews, testimonials, carousel, schema, seo, frontend-form, ajax, multi-product
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Un carrusel de reseñas avanzado y escalable para WordPress con formulario frontend, gestión multi-producto y schema SEO optimizado.

== Description ==

**Aprendiz Reviews** es un plugin profesional y escalable que te permite gestionar testimonios y reseñas de forma completa y optimizada para SEO.

= 🎯 Características Principales =

* **Gestión multi-producto**: Crea múltiples productos/servicios con shortcodes independientes
* **Formulario frontend**: Shortcode `[reviews_form]` para capturar reseñas desde el frontend
* **Schema optimizado**: JSON-LD compatible con Google Rich Results (Product, LocalBusiness, Organization)  
* **Validación avanzada**: Sistema de aprobación con filtros por producto y estado
* **Dashboard profesional**: Estadísticas completas y gestión centralizada
* **Shortcodes dinámicos**: Cada producto genera su propio shortcode único
* **Notificaciones automáticas**: Email instantáneo al administrador con nuevas reseñas
* **Sistema AJAX**: Envío sin recargar página con validación en tiempo real

= 🚀 Formulario Frontend =

La versión 1.5 incluye un formulario completo para capturar reseñas:

* **Sistema de estrellas interactivo** con efectos hover y click
* **Envío por AJAX** sin recargar página  
* **Validación frontend y backend** en tiempo real
* **Notificación automática** por email al administrador
* **Diseño responsive** compatible con todos los temas
* **Seguridad robusta** con nonces de WordPress

Uso: `[reviews_form]` o `[reviews_form titulo="Tu mensaje personalizado"]`

= 📊 SEO Optimizado =

* Schema estructurado compatible con **Google Rich Results**
* Soporte para Product, LocalBusiness, Organization
* AggregateRating y Reviews integrados correctamente
* Eliminado el tipo "Service" (no compatible con Rich Snippets)

= 🎛️ Gestión Avanzada =

* **Interface administrativa** mejorada con dashboard y estadísticas
* **Filtros avanzados** por producto y estado de validación
* **Validación masiva** de reseñas pendientes
* **Media uploader** integrado para avatares
* **Migración automática** desde versiones anteriores

Ideal para negocios, profesionales y webs que necesiten mostrar testimonios organizados por productos/servicios sin depender de plataformas externas.

== Installation ==

1. Sube la carpeta del plugin a `/wp-content/plugins/aprendiz-reviews/`
2. Activa el plugin desde el panel de administración de WordPress
3. Ve a **Aprendiz Reviews** en el menú del admin
4. Crea productos/servicios en **"Añadir Producto/Servicio"**
5. Añade reseñas manualmente o permite que los usuarios las envíen con `[reviews_form]`
6. Usa los shortcodes generados (ej: `[reviews_general]`, `[reviews_mi_producto]`)

== Frequently Asked Questions ==

= ¿Puedo tener reseñas para diferentes productos? =

Sí. El plugin incluye un sistema completo de productos/servicios. Cada uno tiene su propio shortcode y muestra solo sus reseñas específicas.

= ¿Los usuarios pueden enviar reseñas desde el frontend? =

¡Sí! Desde la versión 1.5 incluye el shortcode `[reviews_form]` que permite a los usuarios enviar reseñas directamente desde el frontend.

= ¿Las reseñas del frontend se publican automáticamente? =

No. Las reseñas enviadas desde el frontend se guardan como "Pendientes" para que el administrador las revise y valide manualmente.

= ¿Puedo personalizar el diseño? =

Sí. El plugin incluye estilos base que puedes sobrescribir con tu CSS en el tema activo.

= ¿Qué pasa con mis reseñas al actualizar? =

La migración es automática. Todas las reseñas existentes se asignan al producto "General" con shortcode `[reviews_general]`.

= ¿Es compatible con Google Rich Results? =

Sí. El plugin genera schema JSON-LD válido compatible con Google Rich Results para productos, negocios locales y organizaciones.

== Screenshots ==

1. Dashboard con estadísticas y shortcodes disponibles
2. Gestión de productos/servicios con listado y edición  
3. Formulario para añadir/editar productos con tipos de schema
4. Gestión de reseñas con filtros avanzados y validación masiva
5. Formulario de reseñas del administrador con media uploader
6. Carrusel frontend mostrando reseñas específicas por producto
7. Formulario frontend para usuarios con sistema de estrellas
8. Mensaje de agradecimiento tras enviar reseña desde frontend

== Changelog ==

= 1.5 - 2025-09-14 =
**Arquitectura completamente reorganizada y optimizada**

* **Added**: Estructura MVC profesional con separación de responsabilidades
* **Added**: Modelos independientes (Product, Review) para gestión de datos
* **Added**: Controladores específicos (Product, Review, AJAX) para lógica de negocio  
* **Added**: Sistema de vistas (partials) organizadas por contexto admin/frontend
* **Added**: Carga condicional de assets CSS/JS solo cuando es necesario
* **Added**: Clase Loader para gestión centralizada de hooks y filtros
* **Added**: Activator/Deactivator para instalación y limpieza controlada
* **Improved**: Rendimiento optimizado con carga selectiva de recursos
* **Improved**: Mantenibilidad del código con arquitectura escalable
* **Improved**: Seguridad reforzada con validación centralizada
* **Fixed**: Separación completa de lógica presentacional y de negocio

= 1.4 - 2025-09-13 =
* **Added**: Formulario frontend con shortcode `[reviews_form]`
* **Added**: Sistema de estrellas interactivo con efectos hover/click
* **Added**: Envío AJAX sin recargar página con validación tiempo real
* **Added**: Notificación automática por email al administrador
* **Added**: Título personalizable en formulario frontend
* **Added**: Mensajes de agradecimiento con animaciones
* **Improved**: UX optimizada con feedback visual durante envío
* **Improved**: Seguridad con nonces de WordPress y validación robusta
* **Fixed**: Reseñas frontend se guardan como "Pendientes" para revisión

= 1.3 - 2025-09-10 =
* **Added**: Sistema completo de productos/servicios multi-producto
* **Added**: Shortcodes dinámicos específicos por producto  
* **Added**: Schema JSON-LD optimizado (Product, LocalBusiness, Organization)
* **Added**: Dashboard con estadísticas y shortcodes disponibles
* **Added**: Filtros avanzados en gestión de reseñas
* **Added**: Validación masiva de reseñas pendientes
* **Added**: Migración automática de datos existentes
* **Improved**: Formularios admin con selector de producto/servicio
* **Fixed**: Schema estructurado compatible con Google Rich Results
* **Removed**: Tipo "Service" (no compatible con Rich Snippets)

= 1.2 - 2025-05-30 =
* **Added**: Control de segundos entre scrolls automáticos del carrusel
* **Added**: Campo fecha personalizable al crear reseñas
* **Added**: Funcionalidad completa de edición de reseñas existentes
* **Improved**: Formulario admin con campos precargados para edición
* **Fixed**: Aplicación dinámica del delay de autoplay en Swiper

= 1.1 - 2025-04-15 =
* **Added**: Media uploader integrado para avatares
* **Added**: Validación manual de reseñas antes de mostrar públicamente
* **Added**: Configuración de schema type (Product/LocalBusiness)
* **Improved**: Interface administrativa más intuitiva
* **Fixed**: Compatibilidad mejorada con temas de WordPress

= 1.0 - 2025-03-01 =
* Versión inicial del plugin
* Carrusel básico de reseñas con Swiper.js
* Gestión básica en panel de administración
* Schema.org básico para SEO

== Upgrade Notice ==

= 1.5 =
Arquitectura completamente reorganizada. Migración automática incluida. Backup recomendado antes de actualizar.

= 1.4 = 
Nueva funcionalidad de formulario frontend. Los usuarios pueden enviar reseñas directamente desde tu web.

= 1.3 =
Sistema multi-producto añadido. Migración automática de datos existentes al producto "General".

== Advanced Usage ==

= Shortcodes Disponibles =

* `[reviews_general]` - Reseñas del producto por defecto
* `[reviews_mi_producto]` - Reseñas de producto personalizado  
* `[reviews_form]` - Formulario básico para usuarios
* `[reviews_form titulo="Mensaje personalizado"]` - Formulario con título personalizado

= Hooks para Desarrolladores =

`
// Personalizar schema antes de mostrar
add_filter('aprendiz_reviews_schema', 'mi_funcion_schema', 10, 2);

// Modificar reseñas antes de mostrar  
add_filter('aprendiz_reviews_items', 'mi_funcion_resenas', 10, 2);

// Personalizar email de notificación
add_filter('aprendiz_reviews_notification_email', 'mi_email_personalizado', 10, 3);
`

= Personalización CSS =

El plugin carga estilos base que puedes sobrescribir:

`
.reviews-swiper .swiper-slide {
    /* Personalizar tarjetas de reseñas */
}

.reviews-form-container {
    /* Personalizar formulario frontend */  
}
`

== Support ==

* **Documentación**: [aprendizdeseo.top](https://aprendizdeseo.top/)
* **Telegram**: [Canal de soporte](https://t.me/+mo0aLMYaE6s4ZDc0)
* **Email**: vicks630@gmail.com

Desarrollado con ❤️ por [Aprendiz de SEO](https://aprendizdeseo.top/)
