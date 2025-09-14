<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Import_Controller {
    
    public function display_import_page() {
        $message = '';
        $message_type = '';
        $woocommerce_products = array();
        
        // Verificar si WooCommerce está activo
        if (!class_exists('WooCommerce')) {
            $message = 'WooCommerce no está instalado o activado.';
            $message_type = 'error';
        } else {
            // Obtener productos de WooCommerce
            $woocommerce_products = $this->get_woocommerce_products();
        }
        
        // Procesar importación
        if ($_POST && isset($_POST['import_selected']) && !empty($_POST['selected_products'])) {
            $result = $this->import_selected_products($_POST['selected_products']);
            $message = $result['message'];
            $message_type = $result['type'];
            
            // Actualizar lista tras importar
            $woocommerce_products = $this->get_woocommerce_products();
        }
        
        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'admin/partials/import-products.php';
    }
    
    private function get_woocommerce_products() {
        $products = array();
        $wc_products = wc_get_products(array(
            'status' => 'publish',
            'limit' => -1
        ));
        
        foreach ($wc_products as $wc_product) {
            $suggested_shortcode = 'reviews_' . sanitize_title($wc_product->get_name());
            $already_exists = Aprendiz_Reviews_Product::get_by_shortcode($suggested_shortcode);
            
            $products[] = array(
                'id' => $wc_product->get_id(),
                'name' => $wc_product->get_name(),
                'price' => $wc_product->get_price(),
                'image' => wp_get_attachment_image_url($wc_product->get_image_id(), 'thumbnail'),
                'description' => wp_trim_words($wc_product->get_short_description(), 15),
                'suggested_shortcode' => $suggested_shortcode,
                'already_exists' => $already_exists ? true : false,
                'url' => $wc_product->get_permalink()
            );
        }
        
        return $products;
    }
    
    private function import_selected_products($selected_products) {
        $imported_count = 0;
        $errors = array();
        
        foreach ($selected_products as $product_id) {
            $wc_product = wc_get_product($product_id);
            
            if (!$wc_product) {
                continue;
            }
            
            $shortcode = 'reviews_' . sanitize_title($wc_product->get_name());
            
            // Verificar si ya existe
            $existing = Aprendiz_Reviews_Product::get_by_shortcode($shortcode);
            if ($existing) {
                continue; // Ya existe, saltar
            }
            
            // Crear producto
            $data = array(
                'nombre' => $wc_product->get_name(),
                'shortcode' => $shortcode,
                'tipo' => 'Product',
                'descripcion' => $wc_product->get_short_description() ?: 'Producto importado de WooCommerce',
                'url' => $wc_product->get_permalink(),
                'imagen_url' => wp_get_attachment_image_url($wc_product->get_image_id(), 'medium'),
                'activo' => 1
            );
            
            $result = Aprendiz_Reviews_Product::create($data);
            
            if ($result !== false) {
                $imported_count++;
            } else {
                $errors[] = $wc_product->get_name();
            }
        }
        
        $message = "$imported_count productos importados correctamente.";
        if (!empty($errors)) {
            $message .= " Errores en: " . implode(', ', array_slice($errors, 0, 3));
        }
        
        return array(
            'message' => $message,
            'type' => $imported_count > 0 ? 'success' : 'error'
        );
    }
}
