<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Auto_Shortcode_Controller {
    
    // Propiedad estática para guardar las referencias de los closures (no estrictamente
    // necesaria para esta solución de limpieza, pero buena práctica si necesitaras remove_action en el mismo request).
    private static $added_hooks = array();
    
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
        
        // Procesar limpieza completa
        if (isset($_POST['clean_all_hooks'])) {
            // No se necesita nonce para una limpieza, pero podrías añadir uno por seguridad.
            $this->clean_all_hooks();
            $message = 'Todos los hooks han sido limpiados correctamente.';
            $message_type = 'success';
            $products_with_reviews = $this->get_products_with_reviews(); // Recargar
        }
        
        // Procesar inserción automática
        if (isset($_POST['insert_shortcodes']) && isset($_POST['selected_products']) && !empty($_POST['selected_products'])) {
            // Verificar nonce
            if (!wp_verify_nonce($_POST['_wpnonce'], 'insert_shortcodes_nonce')) {
                wp_die('Error de seguridad.');
            }
            
            // Primero limpiar hooks existentes para evitar duplicados y cambiar de posición
            $this->clean_all_hooks();
            
            $result = $this->insert_shortcodes_automatically($_POST['selected_products'], $_POST['hook_position']);
            $message = $result['message'];
            $message_type = $result['type'];
            
            // Recargar productos después de la inserción
            $products_with_reviews = $this->get_products_with_reviews();
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
                        'already_inserted' => $this->shortcode_already_inserted($our_product->shortcode),
                        'wc_product_id' => $wc_product->get_id()
                    );
                }
            }
        }
        
        return $products;
    }
    
    private function find_woocommerce_product($our_product) {
        // Buscar por nombre exacto
        $wc_products = wc_get_products(array(
            'name' => $our_product->nombre,
            'status' => 'publish',
            'limit' => 1
        ));
        
        if (!empty($wc_products)) {
            return $wc_products[0];
        }
        
        // Buscar por slug
        $slug = sanitize_title($our_product->nombre);
        $wc_products = wc_get_products(array(
            'slug' => $slug,
            'status' => 'publish',
            'limit' => 1
        ));
        
        if (!empty($wc_products)) {
            return $wc_products[0];
        }
        
        return null;
    }

    private function shortcode_already_inserted($shortcode) {
        $saved_hooks = get_option('aprendiz_reviews_auto_hooks', array());
        
        foreach ($saved_hooks as $hook_data) {
            if (isset($hook_data['shortcode']) && $hook_data['shortcode'] === $shortcode) {
                return true;
            }
        }
        
        return false;
    }
    
    private function insert_shortcodes_automatically($selected_products, $hook_position) {
        $inserted_count = 0;
        $errors = array();
        $new_hooks = array();
        
        foreach ($selected_products as $our_product_id) {
            $our_product = Aprendiz_Reviews_Product::get_by_id($our_product_id);
            if (!$our_product) {
                continue;
            }
            
            // Buscar el producto WooCommerce correspondiente
            $wc_product = $this->find_woocommerce_product($our_product);
            if (!$wc_product) {
                $errors[] = $our_product->nombre . ' (no encontrado en WC)';
                continue;
            }
            
            $shortcode = $our_product->shortcode;
            $wc_product_id = $wc_product->get_id();
            
            // Crear hook data con el ID del producto WooCommerce
            $hook_data = $this->get_hook_data_for_position($shortcode, $hook_position, $wc_product_id);
            
            if ($hook_data) {
                $new_hooks[] = $hook_data;
                $inserted_count++;
            } else {
                $errors[] = $our_product->nombre . ' (posición inválida)';
            }
        }
        
        // Guardar todos los hooks de una vez
        if (!empty($new_hooks)) {
            update_option('aprendiz_reviews_auto_hooks', $new_hooks);
        }
        
        $message = "$inserted_count shortcodes insertados correctamente.";
        if (!empty($errors)) {
            $message .= " Errores: " . implode(', ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " y " . (count($errors) - 3) . " más...";
            }
        }
        
        return array(
            'message' => $message,
            'type' => $inserted_count > 0 ? 'success' : 'error'
        );
    }
    
    private function get_hook_data_for_position($shortcode, $position, $wc_product_id) {
        $hook_configs = array(
            
            // 🏷️ DESPUÉS DEL TÍTULO - Hook específico que se ejecuta después del título
            'after_title' => array(
                'hook_name' => 'woocommerce_template_single_title',
                'priority' => 15  // Después de que se renderice el título
            ),
            
            // 💰 DESPUÉS DEL PRECIO - Hook específico después del precio
            'after_price' => array(
                'hook_name' => 'woocommerce_template_single_price', 
                'priority' => 15  // Después de que se renderice el precio
            ),
            
            // 📄 DESPUÉS DE LA DESCRIPCIÓN CORTA - Hook específico
            'after_description' => array(
                'hook_name' => 'woocommerce_template_single_excerpt',
                'priority' => 15  // Después de que se renderice la descripción
            ),
            
            // 🛒 ANTES DEL FORMULARIO - Hook específico antes del botón
            'before_add_to_cart' => array(
                'hook_name' => 'woocommerce_before_add_to_cart_form',
                'priority' => 10
            ),
            
            // 🛒 DESPUÉS DEL FORMULARIO - Hook específico después del botón
            'after_add_to_cart' => array(
                'hook_name' => 'woocommerce_after_add_to_cart_form',
                'priority' => 10
            ),
            
            // 📋 DESPUÉS DE META (categorías, etiquetas) - Hook específico
            'after_meta' => array(
                'hook_name' => 'woocommerce_template_single_meta',
                'priority' => 15
            ),
            
            // ⬇️ DESPUÉS DEL RESUMEN COMPLETO
            'after_summary' => array(
                'hook_name' => 'woocommerce_after_single_product_summary',
                'priority' => 5
            ),
            
            // 📋 DESPUÉS DE LAS PESTAÑAS - Hook específico 
            'after_tabs' => array(
                'hook_name' => 'woocommerce_product_after_tabs',
                'priority' => 10
            ),
            
            // 🎯 ANTES DE TODO EL PRODUCTO - Hook genérico muy temprano
            'before_product' => array(
                'hook_name' => 'woocommerce_before_single_product',
                'priority' => 10
            ),
            
            // 🎯 DESPUÉS DE TODO EL PRODUCTO - Hook genérico muy tarde
            'after_product' => array(
                'hook_name' => 'woocommerce_after_single_product', 
                'priority' => 10
            )
        );
        
        if (!isset($hook_configs[$position])) {
            return false;
        }
        
        $config = $hook_configs[$position];
        
        return array(
            'shortcode' => $shortcode,
            'wc_product_id' => $wc_product_id,
            'hook_name' => $config['hook_name'],
            'priority' => $config['priority'],
            'position' => $position
        );
    }



    
    /**
     * Limpia TODOS nuestros hooks eliminando la opción de configuración.
     * Esto es más robusto que intentar usar remove_action con callbacks dinámicos.
     */
    private function clean_all_hooks() {
        // Simplemente borra la opción que contiene la lista de hooks a cargar.
        // En el siguiente request, init_saved_hooks() no cargará nada.
        delete_option('aprendiz_reviews_auto_hooks');
    }
    
    /**
     * Carga y registra todos los hooks guardados.
     * Usa Closures para un manejo seguro de variables y evita eval().
     */
    public static function init_saved_hooks() {
        // Prevenir doble ejecución
        if (defined('APRENDIZ_REVIEWS_HOOKS_LOADED')) {
            return;
        }
        define('APRENDIZ_REVIEWS_HOOKS_LOADED', true);
        
        $saved_hooks = get_option('aprendiz_reviews_auto_hooks', array());
        
        foreach ($saved_hooks as $hook_data) {
            if (!is_array($hook_data) || !isset($hook_data['shortcode']) || !isset($hook_data['wc_product_id'])) {
                continue;
            }
            
            $shortcode = $hook_data['shortcode'];
            $wc_product_id = $hook_data['wc_product_id'];
            $hook_name = $hook_data['hook_name'];
            $priority = $hook_data['priority'];
            
            // Creamos un Closure que encapsula el shortcode y el ID del producto.
            // Esto asegura que la función tenga acceso a sus valores correctos.
            $callback = function() use ($shortcode, $wc_product_id) {
                global $product;
                // Verificar que estamos en la página del producto correcto
                if (is_product() && $product && $product->get_id() == $wc_product_id) {
                    echo '<div class="aprendiz-reviews-auto-wrapper">';
                    echo do_shortcode("[$shortcode]");
                    echo '</div>';
                }
            };
            
            // Agregamos el hook usando el Closure
            add_action($hook_name, $callback, $priority);
            
            // Guardamos la referencia por si fuera necesario, aunque la limpieza
            // ahora se hace borrando la opción completa.
            self::$added_hooks[] = array(
                'hook_name' => $hook_name,
                'callback'  => $callback, 
                'priority'  => $priority
            );
        }
    }
}

// Ejecutar hooks guardados en el momento adecuado. Prioridad 20 para asegurar que WC esté cargado.
add_action('init', array('Aprendiz_Reviews_Auto_Shortcode_Controller', 'init_saved_hooks'), 20);