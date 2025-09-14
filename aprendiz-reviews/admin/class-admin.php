<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Admin {
    
    private $plugin_name;
    private $version;
    private $product_controller;
    private $review_controller;
    
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->product_controller = new Aprendiz_Reviews_Product_Controller();
        $this->review_controller = new Aprendiz_Reviews_Review_Controller();
    }
    
    public function enqueue_styles($hook) {
        $allowed_pages = array(
            'toplevel_page_aprendiz-reviews',
            'aprendiz-reviews_page_gestionar-productos',
            'aprendiz-reviews_page_añadir-producto',
            'aprendiz-reviews_page_gestionar-resenas',
            'aprendiz-reviews_page_añadir-resena'
        );
        
        if (!in_array($hook, $allowed_pages)) return;
        
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            APRENDIZ_REVIEWS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    public function enqueue_scripts($hook) {
        $allowed_pages = array(
            'aprendiz-reviews_page_añadir-resena',
            'toplevel_page_aprendiz-reviews',
            'aprendiz-reviews_page_añadir-producto'
        );
        
        if (!in_array($hook, $allowed_pages)) return;
        
        wp_enqueue_media();
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            APRENDIZ_REVIEWS_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }
    
    public function add_plugin_admin_menu() {
        // Menú principal
        add_menu_page(
            'Aprendiz Reviews',
            'Aprendiz Reviews', 
            'manage_options', 
            'aprendiz-reviews', 
            array($this, 'display_dashboard'),
            'dashicons-star-filled'
        );
        
        // Submenús
        add_submenu_page(
            'aprendiz-reviews',
            'Dashboard',
            'Dashboard', 
            'manage_options', 
            'aprendiz-reviews'
        );
        
        add_submenu_page(
            'aprendiz-reviews',
            'Productos/Servicios',
            'Productos/Servicios', 
            'manage_options', 
            'gestionar-productos', 
            array($this->product_controller, 'display_list')
        );
        
        add_submenu_page(
            'aprendiz-reviews',
            'Añadir Producto',
            'Añadir Producto', 
            'manage_options', 
            'añadir-producto', 
            array($this->product_controller, 'display_form')
        );
        
        add_submenu_page(
            'aprendiz-reviews',
            'Gestionar Reseñas',
            'Gestionar Reseñas', 
            'manage_options', 
            'gestionar-resenas', 
            array($this->review_controller, 'display_list')
        );
        
        add_submenu_page(
            'aprendiz-reviews',
            'Añadir Reseña',
            'Añadir Reseña', 
            'manage_options', 
            'añadir-resena', 
            array($this->review_controller, 'display_form')
        );
    }
    
    public function display_dashboard() {
        $product_stats = array(
            'total' => count(Aprendiz_Reviews_Product::get_all()),
            'active' => count(Aprendiz_Reviews_Product::get_all(true))
        );
        
        $review_stats = Aprendiz_Reviews_Review::get_stats();
        $products = Aprendiz_Reviews_Product::get_all();
        $migration_date = get_option('cr_fecha_migracion', false);
        
        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'admin/partials/dashboard.php';
    }
    
    public function migrate_data() {
        // Verificar si ya se ejecutó la migración
        if (get_option('cr_migracion_completada', false)) {
            return;
        }
        
        global $wpdb;
        $tabla_resenas = $wpdb->prefix . 'reseñas';
        $tabla_productos = $wpdb->prefix . 'productos_servicios';
        
        // Verificar si existe la columna producto_servicio_id
        $columna_existe = $wpdb->get_results("SHOW COLUMNS FROM $tabla_resenas LIKE 'producto_servicio_id'");
        
        if (empty($columna_existe)) {
            // Añadir la columna si no existe
            $wpdb->query("ALTER TABLE $tabla_resenas ADD COLUMN producto_servicio_id INT(11) DEFAULT 1");
            $wpdb->query("ALTER TABLE $tabla_resenas ADD KEY idx_producto_servicio (producto_servicio_id)");
        }
        
        // Asignar todas las reseñas existentes al producto por defecto
        $wpdb->query("UPDATE $tabla_resenas SET producto_servicio_id = 1 WHERE producto_servicio_id IS NULL OR producto_servicio_id = 0");
        
        // Marcar migración como completada
        update_option('cr_migracion_completada', true);
        update_option('cr_fecha_migracion', current_time('mysql'));
    }
}
