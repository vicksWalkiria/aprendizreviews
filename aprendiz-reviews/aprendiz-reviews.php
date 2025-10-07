<?php
/**
 * Plugin Name: Aprendiz Reviews
 * Plugin URI: https://aprendizdeseo.top/plugin-reviews
 * Description: Plugin de carrusel de reseñas desarrollado por Aprendiz de SEO
 * Version: 1.7.0
 * Author: Aprendiz de SEO
 * Author URI: https://aprendizdeseo.top
 * Text Domain: aprendiz-reviews
 * Domain Path: /languages
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('APRENDIZ_REVIEWS_VERSION', '1.7.0');
define('APRENDIZ_REVIEWS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('APRENDIZ_REVIEWS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('APRENDIZ_REVIEWS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Cargar traducciones del plugin
 */
function aprendiz_reviews_load_textdomain() {
    load_plugin_textdomain('aprendiz-reviews', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'aprendiz_reviews_load_textdomain');

// Cargar clase principal
require_once APRENDIZ_REVIEWS_PLUGIN_PATH . 'includes/class-aprendiz-reviews.php';

// Hooks de activación/desactivación
register_activation_hook(__FILE__, array('Aprendiz_Reviews_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Aprendiz_Reviews_Deactivator', 'deactivate'));

// Inicializar plugin
function run_aprendiz_reviews() {
    $plugin = new Aprendiz_Reviews();
    $plugin->run();
}
run_aprendiz_reviews();
