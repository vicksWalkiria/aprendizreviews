<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Auto_Shortcode_Controller {
    
    public function display_auto_shortcode_page() {
        $message = '';
        $message_type = '';
        $products_with_reviews = array();
        
        if (!class_exists('WooCommerce')) {
            $message = 'WooCommerce no está instalado o activado.';
            $message_type = 'error';
        } else {
            $products_with_reviews = $this->get_products_with_reviews();
        }
        
        // Procesar inserción automática
        if ($_POST && isset($_POST['insert_shortcodes']) && !empty($_POST['selected_products'])) {
            $result = $this->insert_shortcodes_automatically($_POST['selected_products'], $_POST['hook_position']);
            $message = $result['message'];
            $message_type = $result['type'];
        }
        
        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'admin/partials/auto-shortcode.php';
    }
    
    private function get_products_with_reviews() {
        $products = array();
        
        // Obtener productos de Aprendiz Reviews que tienen reseñas
        $our_products = Aprendiz_Reviews_Product::get_all();
        
        foreach ($our_products as $our_product) {
            $review_count = Aprendiz_Reviews_Product::count_reviews($our_product->id);
            
            if ($review_count > 0) {
                // Buscar el producto WooCommerce correspondiente
                $wc_product = $this->find_woocommerce_product($our_product);
                
                if ($wc_product) {
                    $products[] = array(
                        'our_product' => $our_product,
                        'wc_product' => $wc_product,
                        'review_count' => $review_count,
                        'shortcode' => $our_product->shortcode,
                        'already_inserted' => $this->shortcode_already_inserted($wc_product, $our_product->shortcode),
                        'wc_product_id' => $wc_product->get_id()
                    );
                }
            }
        }
        
        return $products;
    }
    
    private function find_woocommerce_product($our_product) {
        // 1. Buscar por nombre exacto
        $wc_products = wc_get_products(array(
            'name' => $our_product->nombre,
            'status' => 'publish',
            'limit' => 1
        ));
        
        if (!empty($wc_products)) {
            return $wc_products[0];
        }
        
        // 2. Buscar por slug generado desde el nombre
        $slug = sanitize_title($our_product->nombre);
        $wc_products = wc_get_products(array(
            'slug' => $slug,
            'status' => 'publish',
            'limit' => 1
        ));
        
        if (!empty($wc_products)) {
            return $wc_products[0];
        }
        
        // 3. Buscar por nombre parcial (más flexible)
        $wc_products = wc_get_products(array(
            'status' => 'publish',
            'limit' => -1
        ));
        
        foreach ($wc_products as $wc_product) {
            // Comparar nombres normalizados
            $our_name = strtolower(trim($our_product->nombre));
            $wc_name = strtolower(trim($wc_product->get_name()));
            
            if ($our_name === $wc_name) {
                return $wc_product;
            }
            
            // Buscar coincidencia parcial
            if (strpos($wc_name, $our_name) !== false || strpos($our_name, $wc_name) !== false) {
                return $wc_product;
            }
        }
        
        return null;
    }

    private function shortcode_already_inserted($wc_product, $shortcode) {
        $content = $wc_product->get_description();
        $short_description = $wc_product->get_short_description();
        
        return (strpos($content, "[$shortcode]") !== false || strpos($short_description, "[$shortcode]") !== false);
    }
    
    private function insert_shortcodes_automatically($selected_products, $hook_position) {
        $inserted_count = 0;
        $errors = array();
        
        foreach ($selected_products as $our_product_id) {
            // Buscar nuestro producto
            $our_product = Aprendiz_Reviews_Product::get_by_id($our_product_id);
            if (!$our_product) {
                continue;
            }
            
            // Buscar el producto WooCommerce correspondiente
            $wc_product = $this->find_woocommerce_product($our_product);
            if (!$wc_product) {
                $errors[] = "No se encontró producto WC para: " . $our_product->nombre;
                continue;
            }
            
            $shortcode = $our_product->shortcode;
            
            // Verificar si ya está insertado
            if ($this->shortcode_already_inserted($wc_product, $shortcode)) {
                continue;
            }
            
            // Insertar según la posición elegida
            $success = $this->insert_shortcode_at_position($wc_product, $shortcode, $hook_position);
            
            if ($success) {
                $inserted_count++;
            } else {
                $errors[] = $wc_product->get_name();
            }
        }
        
        $message = "$inserted_count shortcodes insertados correctamente.";
        if (!empty($errors)) {
            $message .= " Errores: " . implode(', ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " y " . (count($errors) - 3) . " más.";
            }
        }
        
        return array(
            'message' => $message,
            'type' => $inserted_count > 0 ? 'success' : 'error'
        );
    }
    
    private function insert_shortcode_at_position($wc_product, $shortcode, $position) {
        switch ($position) {
            case 'after_title':
                return $this->add_hook_function($shortcode, 'woocommerce_single_product_summary', 6);
                
            case 'after_price':
                return $this->add_hook_function($shortcode, 'woocommerce_single_product_summary', 11);
                
            case 'after_description':
                return $this->add_hook_function($shortcode, 'woocommerce_single_product_summary', 21);
                
            case 'after_add_to_cart':
                return $this->add_hook_function($shortcode, 'woocommerce_single_product_summary', 31);
                
            case 'in_tabs':
                return $this->add_to_product_tabs($wc_product, $shortcode);
                
            case 'after_summary':
                return $this->add_hook_function($shortcode, 'woocommerce_after_single_product_summary', 15);
                
            default:
                // Insertar directamente en la descripción como fallback
                return $this->add_to_description($wc_product, $shortcode);
        }
    }
    
    private function add_hook_function($shortcode, $hook_name, $priority) {
        $function_name = 'aprendiz_reviews_display_' . str_replace(['reviews_', '-'], ['', '_'], $shortcode);
        
        // Crear función dinámica para el hook
        $hook_code = "
        if (!function_exists('{$function_name}')) {
            function {$function_name}() {
                global \$product;
                if (is_product()) {
                    echo do_shortcode('[{$shortcode}]');
                }
            }
            add_action('{$hook_name}', '{$function_name}', {$priority});
        }";
        
        // Guardar en base de datos para que persista
        $existing_hooks = get_option('aprendiz_reviews_auto_hooks', array());
        $existing_hooks[$function_name] = array(
            'shortcode' => $shortcode,
            'hook_name' => $hook_name,
            'priority' => $priority,
            'function_name' => $function_name
        );
        update_option('aprendiz_reviews_auto_hooks', $existing_hooks);
        
        // Ejecutar inmediatamente
        eval($hook_code);
        
        return true;
    }
    
    private function add_to_description($wc_product, $shortcode) {
        $current_description = $wc_product->get_description();
        
        // Verificar si ya está el shortcode
        if (strpos($current_description, "[{$shortcode}]") !== false) {
            return false; // Ya existe
        }
        
        $new_description = $current_description . "\n\n[{$shortcode}]";
        
        $wc_product->set_description($new_description);
        $result = $wc_product->save();
        
        return $result ? true : false;
    }
    
    private function add_to_product_tabs($wc_product, $shortcode) {
        // Para las pestañas, añadiremos al final de la descripción principal
        return $this->add_to_description($wc_product, $shortcode);
    }
    
    // Método estático para cargar hooks guardados
    public static function init_saved_hooks() {
        $saved_hooks = get_option('aprendiz_reviews_auto_hooks', array());
        
        foreach ($saved_hooks as $hook_data) {
            if (is_array($hook_data) && isset($hook_data['function_name'])) {
                $function_name = $hook_data['function_name'];
                $shortcode = $hook_data['shortcode'];
                $hook_name = $hook_data['hook_name'];
                $priority = $hook_data['priority'];
                
                if (!function_exists($function_name)) {
                    $hook_code = "
                    function {$function_name}() {
                        global \$product;
                        if (is_product()) {
                            echo do_shortcode('[{$shortcode}]');
                        }
                    }";
                    
                    eval($hook_code);
                    add_action($hook_name, $function_name, $priority);
                }
            }
        }
    }
}

// Ejecutar hooks guardados al cargar WordPress
add_action('init', array('Aprendiz_Reviews_Auto_Shortcode_Controller', 'init_saved_hooks'));
