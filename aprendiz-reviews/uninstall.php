<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://aprendizdeseo.top/
 * @since      1.4.0
 *
 * @package    Aprendiz_Reviews
 */
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
// Verificar que el usuario tiene los permisos correctos para borrar plugins.
// 'delete_plugins' es más específico y correcto que 'activate_plugins' para esta acción.
if (!current_user_can('delete_plugins')) {
    return;
}
/**
 * Limpiar datos del plugin al desinstalar
 * 
 * Solo se ejecuta si el usuario específicamente desinstala el plugin,
 * NO cuando lo desactiva.
 */
// Preguntar si eliminar datos (mediante opción)
$delete_data = get_option('aprendiz_reviews_delete_data_on_uninstall', false);
if ($delete_data) {
    global $wpdb;

    // Eliminar tablas
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    // Se recomienda usar 'resenas' en lugar de 'reseñas' para evitar problemas de codificación en la base de datos.
    $tabla_resenas = $wpdb->prefix . 'resenas';

    $wpdb->query("DROP TABLE IF EXISTS $tabla_resenas");
    $wpdb->query("DROP TABLE IF EXISTS $tabla_productos");

    // Eliminar opciones
    $options_to_delete = array(
        'cr_schema_type',
        'cr_schema_name',
        'cr_scroll_delay',
        'cr_schema_address',
        'cr_migracion_completada',
        'cr_fecha_migracion',
        'aprendiz_reviews_version',
        'aprendiz_reviews_db_version',
        'aprendiz_reviews_delete_data_on_uninstall'
    );

    foreach ($options_to_delete as $option) {
        delete_option($option);
    }

    // Limpiar scheduled events
    wp_clear_scheduled_hook('aprendiz_reviews_cleanup');
    wp_clear_scheduled_hook('aprendiz_reviews_daily_maintenance');

    // Limpiar transients
    delete_transient('aprendiz_reviews_stats');
    delete_transient('aprendiz_reviews_products_cache');

    // Limpiar user meta relacionada (si la hubiera)
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'aprendiz_reviews_%'");
} else {
    // Solo limpiar datos temporales, mantener configuración
    delete_transient('aprendiz_reviews_stats');
    delete_transient('aprendiz_reviews_products_cache');
    wp_clear_scheduled_hook('aprendiz_reviews_cleanup');
}
// Log de desinstalación (opcional)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Aprendiz Reviews: Plugin uninstalled ' . ($delete_data ? 'with data deletion' : 'keeping data'));
}
