<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Review_Controller {
    
    public function display_list() {
        $message = '';
        $message_type = '';
        
        // Procesar validaciones masivas
        if (isset($_POST['validar_resenas']) && !empty($_POST['resenas_validar'])) {
            $ids = array_map('intval', $_POST['resenas_validar']);
            $result = Aprendiz_Reviews_Review::validate_bulk($ids);
            
            if ($result !== false) {
                $message = count($ids) . ' reseñas validadas.';
                $message_type = 'success';
            } else {
                $message = 'Error al validar reseñas.';
                $message_type = 'error';
            }
        }
        
        // Eliminar reseña
        if (isset($_GET['eliminar_id'])) {
            $id = intval($_GET['eliminar_id']);
            $result = Aprendiz_Reviews_Review::delete($id);
            
            if ($result !== false) {
                $message = 'Reseña eliminada.';
                $message_type = 'success';
            } else {
                $message = 'Error al eliminar reseña.';
                $message_type = 'error';
            }
        }
        
        // Obtener filtros
        $filters = array(
            'product_id' => isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0,
            'validated' => isset($_GET['validado']) ? intval($_GET['validado']) : -1
        );
        
        $reviews = Aprendiz_Reviews_Review::get_all($filters);
        $products = Aprendiz_Reviews_Product::get_all();
        
        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'admin/partials/review-list.php';
    }
    
    public function display_form() {
        $review_id = isset($_GET['editar_id']) ? intval($_GET['editar_id']) : 0;
        $review = $review_id ? Aprendiz_Reviews_Review::get_by_id($review_id) : null;
        $message = '';
        $message_type = '';
        
        if ($_POST) {
            $result = $this->handle_form_submission();
            $message = $result['message'];
            $message_type = $result['type'];
            
            // Refrescar datos si fue edición
            if ($review_id && $result['success']) {
                $review = Aprendiz_Reviews_Review::get_by_id($review_id);
            }
        }
        
        $products = Aprendiz_Reviews_Product::get_all();
        
        include APRENDIZ_REVIEWS_PLUGIN_PATH . 'admin/partials/review-form.php';
    }
    
    private function handle_form_submission() {
        $fecha_input = sanitize_text_field($_POST['fecha']);
        $fecha_final = date('Y-m-d H:i:s', strtotime($fecha_input . ' ' . date('H:i:s')));

        $data = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'valoracion' => intval($_POST['valoracion']),
            'texto' => sanitize_textarea_field($_POST['texto']),
            'avatar_url' => esc_url_raw($_POST['avatar_url']),
            'validado' => 1,
            'fecha' => $fecha_final,
            'producto_servicio_id' => intval($_POST['producto_servicio_id'])
        );

        if (!empty($_POST['editar_id'])) {
            $result = Aprendiz_Reviews_Review::update(intval($_POST['editar_id']), $data);
            return array(
                'success' => $result !== false,
                'message' => $result !== false ? 'Reseña actualizada.' : 'Error al actualizar reseña.',
                'type' => $result !== false ? 'success' : 'error'
            );
        } else {
            $result = Aprendiz_Reviews_Review::create($data);
            return array(
                'success' => $result !== false,
                'message' => $result !== false ? 'Reseña guardada.' : 'Error al guardar reseña.',
                'type' => $result !== false ? 'success' : 'error'
            );
        }
    }
}
