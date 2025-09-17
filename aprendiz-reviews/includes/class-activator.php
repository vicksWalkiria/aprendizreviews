<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Activator {

    public static function activate() {
        self::create_tables();
        self::insert_default_product();
    }

    private static function create_tables() {
        global $wpdb;

        // Crear tabla de productos
        $tabla_productos = $wpdb->prefix . 'productos_servicios';
        $charset_collate = $wpdb->get_charset_collate();

        $sql_productos = "CREATE TABLE $tabla_productos (
            id INT(11) NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            tipo ENUM('Product', 'LocalBusiness', 'Organization') NOT NULL DEFAULT 'Product',
            shortcode VARCHAR(50) UNIQUE NOT NULL,
            descripcion TEXT,
            url VARCHAR(255),
            imagen_url VARCHAR(255),
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            activo TINYINT(1) DEFAULT 1,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Crear tabla de reseñas
        $tabla_resenas = $wpdb->prefix . 'reseñas';

        $sql_resenas = "CREATE TABLE $tabla_resenas (
            id INT(11) NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            texto TEXT NOT NULL,
            valoracion TINYINT(1) NOT NULL,
            fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
            validado TINYINT(1) DEFAULT 0,
            avatar_url VARCHAR(255) DEFAULT NULL,
            producto_servicio_id INT(11) DEFAULT 1,
            PRIMARY KEY (id),
            KEY idx_producto_servicio (producto_servicio_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_productos);
        dbDelta($sql_resenas);
    }

    private static function insert_default_product() {
        global $wpdb;
        $tabla_productos = $wpdb->prefix . 'productos_servicios';
        
        // Insertar producto por defecto si no existe
        $existe_default = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_productos WHERE id = 1");
        if ($existe_default == 0) {
            $wpdb->insert($tabla_productos, [
                'id' => 1,
                'nombre' => 'General',
                'tipo' => 'Product',
                'shortcode' => 'reviews_general',
                'descripcion' => 'Reseñas generales del sitio web'
            ]);
        }
    }
}
