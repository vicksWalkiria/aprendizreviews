<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Review {
    
    public static function get_all($filters = array()) {
        global $wpdb;
        $tabla_resenas = $wpdb->prefix . 'reseñas';
        $tabla_productos = $wpdb->prefix . 'productos_servicios';
        
        $where = "WHERE 1=1";
        
        if (!empty($filters['product_id'])) {
            $where .= $wpdb->prepare(" AND r.producto_servicio_id = %d", $filters['product_id']);
        }
        
        if (isset($filters['validated']) && $filters['validated'] >= 0) {
            $where .= $wpdb->prepare(" AND r.validado = %d", $filters['validated']);
        }
        
        return $wpdb->get_results("
            SELECT r.*, p.nombre as producto_nombre 
            FROM $tabla_resenas r 
            LEFT JOIN $tabla_productos p ON r.producto_servicio_id = p.id 
            $where 
            ORDER BY r.fecha DESC
        ");
    }
    
    public static function get_by_product($product_id, $validated_only = true, $limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'reseñas';
        
        $where = "WHERE producto_servicio_id = %d";
        if ($validated_only) {
            $where .= " AND validado = 1";
        }
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table $where ORDER BY fecha DESC LIMIT %d",
            $product_id, $limit
        ));
    }
    
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'reseñas';
        
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    public static function create($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'reseñas';
        
        return $wpdb->insert($table, $data);
    }
    
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'reseñas';
        
        return $wpdb->update($table, $data, array('id' => $id));
    }
    
    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'reseñas';
        
        return $wpdb->delete($table, array('id' => $id));
    }
    
    public static function validate_bulk($ids) {
        global $wpdb;
        $table = $wpdb->prefix . 'reseñas';
        
        if (empty($ids)) return false;
        
        $ids_str = implode(',', array_map('intval', $ids));
        return $wpdb->query("UPDATE $table SET validado = 1 WHERE id IN ($ids_str)");
    }
    
    public static function get_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'reseñas';
        
        return array(
            'total' => $wpdb->get_var("SELECT COUNT(*) FROM $table"),
            'validated' => $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE validado = 1"),
            'pending' => $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE validado = 0")
        );
    }
}
