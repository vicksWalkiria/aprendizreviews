<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Deactivator {

    public static function deactivate() {
        // Limpiar scheduled events si los hay
        wp_clear_scheduled_hook('aprendiz_reviews_cleanup');
        
        // Limpiar opciones temporales
        delete_option('aprendiz_reviews_temp_data');
    }
}
