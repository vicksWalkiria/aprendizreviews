<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Product_Controller {

    public function display_list() {
        // Procesar eliminaciones
        if (isset($_GET['eliminar_id'])) {
            $id = intval($_GET['eliminar_id']);
            if ($id !== 1) { // No permitir eliminar el producto por defecto
                Aprendiz_Reviews_Product::deactivate($id);
                $message = __('producto desactivado.', 'aprendiz-reviews');
                $message_type = 'success';
            } else {
                $message = __('No se puede eliminar el producto por defecto.', 'aprendiz-reviews');
                $message_type = 'error';
            }
        }

        $products = Aprendiz_Reviews_Product::get_all(false); // Incluir inactivos

        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'admin/partials/product-list.php';
    }

    public function display_form() {
        $product_id = isset($_GET['editar_id']) ? intval($_GET['editar_id']) : 0;
        $product = $product_id ? Aprendiz_Reviews_Product::get_by_id($product_id) : null;
        $message = '';
        $message_type = '';

        if ($_POST) {
            $result = $this->handle_form_submission();
            $message = $result['message'];
            $message_type = $result['type'];

            // Refrescar datos si fue edición
            if ($product_id && $result['success']) {
                $product = Aprendiz_Reviews_Product::get_by_id($product_id);
            }
        }

        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'admin/partials/product-form.php';
    }

    private function handle_form_submission() {
        $nombre = sanitize_text_field($_POST['nombre']);
        $shortcode = sanitize_text_field($_POST['shortcode']);
        $tipo = sanitize_text_field($_POST['tipo']);
        $descripcion = sanitize_textarea_field($_POST['descripcion']);
        $url = esc_url_raw($_POST['url']);
        $imagen_url = esc_url_raw($_POST['imagen_url']);

        // Validar shortcode único
        if (!empty($_POST['editar_id'])) {
            $existing = $this->check_shortcode_exists($shortcode, intval($_POST['editar_id']));
        } else {
            $existing = $this->check_shortcode_exists($shortcode);
        }

        if ($existing) {
            return array(
                'success' => false,
                'message' => __('El shortcode ya existe. Elige otro.', 'aprendiz-reviews'),
                'type' => 'error'
            );
        }

        $data = array(
            'nombre' => $nombre,
            'shortcode' => $shortcode,
            'tipo' => $tipo,
            'descripcion' => $descripcion,
            'url' => $url,
            'imagen_url' => $imagen_url,
            'activo' => 1
        );

        if (!empty($_POST['editar_id'])) {
            $result = Aprendiz_Reviews_Product::update(intval($_POST['editar_id']), $data);
            return array(
                'success' => $result !== false,
                'message' => $result !== false ? __('producto actualizado.', 'aprendiz-reviews') : __('Error al actualizar.', 'aprendiz-reviews'),
                'type' => $result !== false ? 'success' : 'error'
            );
        } else {
            $result = Aprendiz_Reviews_Product::create($data);
            return array(
                'success' => $result !== false,
                'message' => $result !== false ? __('producto creado.', 'aprendiz-reviews') : __('Error al crear.', 'aprendiz-reviews'),
                'type' => $result !== false ? 'success' : 'error'
            );
        }
    }

    private function check_shortcode_exists($shortcode, $exclude_id = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'productos_servicios';

        $query = "SELECT COUNT(*) FROM $table WHERE shortcode = %s";
        $params = array($shortcode);

        if ($exclude_id > 0) {
            $query .= " AND id != %d";
            $params[] = $exclude_id;
        }

        return $wpdb->get_var($wpdb->prepare($query, $params)) > 0;
    }
}