<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Product {
    
    public static function get_all($active_only = true) {
        global $wpdb;
        $table = $wpdb->prefix . 'productos_servicios';
        $where = $active_only ? 'WHERE activo = 1' : '';
        
        return $wpdb->get_results("SELECT * FROM $table $where ORDER BY fecha_creacion DESC");
    }
    
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'productos_servicios';
        
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    public static function get_by_shortcode($shortcode) {
        global $wpdb;
        $table = $wpdb->prefix . 'productos_servicios';
        
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE shortcode = %s AND activo = 1", $shortcode));
    }
    
    public static function create($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'productos_servicios';
        
        return $wpdb->insert($table, $data);
    }
    
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'productos_servicios';
        
        return $wpdb->update($table, $data, array('id' => $id));
    }
    
    public static function deactivate($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'productos_servicios';
        
        return $wpdb->update($table, array('activo' => 0), array('id' => $id));
    }
    
    public static function count_reviews($product_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'reseñas';
        
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE producto_servicio_id = %d", $product_id));
    }
}
