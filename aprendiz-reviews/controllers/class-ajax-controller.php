<?php
if (!defined('ABSPATH')) {
    exit;
}

class Aprendiz_Reviews_Ajax_Controller {
    
    public function handle_frontend_review() {
        // Verificar nonce de seguridad
        if (!wp_verify_nonce($_POST['nonce'], 'review_form_nonce')) {
            wp_send_json_error('Error de seguridad');
            return;
        }
        
        // Validar datos
        $nombre = sanitize_text_field($_POST['nombre']);
        $valoracion = intval($_POST['valoracion']);
        $producto_id = intval($_POST['producto_servicio_id']);
        $texto = sanitize_textarea_field($_POST['texto']);
        
        if (empty($nombre) || empty($texto) || $valoracion < 1 || $valoracion > 5 || $producto_id < 1) {
            wp_send_json_error('Por favor completa todos los campos correctamente');
            return;
        }
        
        // Obtener info del producto
        $producto = Aprendiz_Reviews_Product::get_by_id($producto_id);
        
        if (!$producto || !$producto->activo) {
            wp_send_json_error('Producto no válido');
            return;
        }
        
        // Guardar en base de datos (sin validar automáticamente)
        $data = array(
            'nombre' => $nombre,
            'valoracion' => $valoracion,
            'texto' => $texto,
            'producto_servicio_id' => $producto_id,
            'validado' => 0, // Sin validar hasta que se apruebe
            'fecha' => current_time('mysql')
        );
        
        $resultado = Aprendiz_Reviews_Review::create($data);
        
        if (!$resultado) {
            wp_send_json_error('Error al guardar la reseña');
            return;
        }
        
        // Enviar email de notificación
        $email_sent = $this->send_notification_email($nombre, $producto->nombre, $valoracion, $texto);
        
        if ($email_sent) {
            wp_send_json_success('Reseña enviada correctamente. ¡Gracias!');
        } else {
            wp_send_json_success('Reseña guardada (pero hubo un problema enviando el email de notificación)');
        }
    }
    
    private function send_notification_email($nombre, $producto_nombre, $valoracion, $texto) {
        $subject = '📝 Nueva reseña recibida - ' . get_bloginfo('name');
        $estrellas = str_repeat('⭐', $valoracion);
        
        $message = "
        <h2>Nueva reseña recibida</h2>
        <hr>
        <strong>Nombre:</strong> {$nombre}<br>
        <strong>Producto:</strong> {$producto_nombre}<br>
        <strong>Valoración:</strong> {$estrellas} ({$valoracion}/5)<br>
        <strong>Fecha:</strong> " . date('d/m/Y H:i') . "<br>
        <br>
        <strong>Comentario:</strong><br>
        <p style='background:#f9f9f9;padding:15px;border-left:4px solid #0073aa;'>{$texto}</p>
        <br>
        <a href='" . admin_url('admin.php?page=gestionar-resenas') . "' style='background:#0073aa;color:white;padding:10px 15px;text-decoration:none;border-radius:3px;'>Ver en el panel de administración</a>
        ";
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <no-reply@' . $_SERVER['HTTP_HOST'] . '>'
        );
        
        return wp_mail(get_option('admin_email'), $subject, $message, $headers);
    }
}
