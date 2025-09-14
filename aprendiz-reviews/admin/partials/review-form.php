<?php
if (!defined('ABSPATH')) {
    exit;
}

$is_edit = !empty($review);
$page_title = $is_edit ? '✏️ Editar Reseña' : '➕ Añadir Reseña';
?>

<div class="wrap">
    <h1><?php echo $page_title; ?></h1>
    
    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <?php if ($is_edit): ?>
            <input type="hidden" name="editar_id" value="<?php echo $review->id; ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="nombre">Nombre *</label>
                </th>
                <td>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="<?php echo esc_attr($review->nombre ?? ''); ?>" 
                           required 
                           style="width: 100%;"
                           placeholder="Nombre del cliente">
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="valoracion">Valoración *</label>
                </th>
                <td>
                    <select id="valoracion" name="valoracion" required>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php selected($review->valoracion ?? 5, $i); ?>>
                                <?php echo str_repeat('⭐', $i) . " ($i/5)"; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="texto">Texto de la Reseña *</label>
                </th>
                <td>
                    <textarea id="texto" 
                              name="texto" 
                              rows="4" 
                              required 
                              style="width: 100%;"
                              placeholder="Escribe aquí el comentario de la reseña..."><?php echo esc_textarea($review->texto ?? ''); ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="fecha">Fecha de Reseña *</label>
                </th>
                <td>
                    <input type="date" 
                           id="fecha" 
                           name="fecha" 
                           value="<?php echo esc_attr(isset($review->fecha) ? date('Y-m-d', strtotime($review->fecha)) : date('Y-m-d')); ?>" 
                           required>
                    <p class="description">Fecha en la que se hizo la reseña (se mantendrá la hora actual).</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="producto_servicio_id">Producto/Servicio *</label>
                </th>
                <td>
                    <select id="producto_servicio_id" name="producto_servicio_id" required>
                        <?php foreach ($products as $producto): ?>
                            <option value="<?php echo $producto->id; ?>" <?php selected($review->producto_servicio_id ?? 1, $producto->id); ?>>
                                <?php echo esc_html($producto->nombre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Producto o servicio al que pertenece esta reseña.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="avatar_url">Avatar</label>
                </th>
                <td>
                    <input type="text" 
                           id="avatar_url" 
                           name="avatar_url" 
                           value="<?php echo esc_attr($review->avatar_url ?? ''); ?>" 
                           style="width: 60%;" 
                           readonly 
                           placeholder="URL del avatar">
                    <input type="button" 
                           id="upload_avatar_button" 
                           class="button" 
                           value="Elegir imagen">
                    <input type="button" 
                           id="remove_avatar_button" 
                           class="button button-secondary" 
                           value="Quitar"
                           style="display: <?php echo !empty($review->avatar_url) ? 'inline-block' : 'none'; ?>;">
                    
                    <div id="avatar_preview" style="margin-top: 10px;">
                        <?php if (!empty($review->avatar_url)): ?>
                            <img src="<?php echo esc_url($review->avatar_url); ?>" 
                                 style="max-width: 80px; border-radius: 50%; border: 2px solid #ddd;">
                        <?php endif; ?>
                    </div>
                    <p class="description">Imagen del perfil del cliente (opcional).</p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" 
                   class="button-primary" 
                   value="<?php echo $is_edit ? 'Actualizar reseña' : 'Guardar reseña'; ?>">
            
            <a href="<?php echo admin_url('admin.php?page=gestionar-resenas'); ?>" 
               class="button button-secondary">Cancelar</a>
        </p>
    </form>
    
    <?php if ($is_edit): ?>
        <div class="postbox" style="margin-top: 20px;">
            <h3 style="padding: 10px 15px; margin: 0; background: #f1f1f1;">ℹ️ Información de la Reseña</h3>
            <div style="padding: 15px;">
                <p><strong>ID:</strong> <?php echo $review->id; ?></p>
                <p><strong>Fecha de creación:</strong> <?php echo date('d/m/Y H:i', strtotime($review->fecha)); ?></p>
                <p><strong>Estado:</strong> 
                    <?php if ($review->validado): ?>
                        <span style="color: #46b450;">✅ Validada (visible públicamente)</span>
                    <?php else: ?>
                        <span style="color: #ffba00;">⏳ Pendiente de validación</span>
                    <?php endif; ?>
                </p>
                <p><strong>Producto asociado:</strong> 
                    <?php 
                    $producto_nombre = '';
                    foreach ($products as $p) {
                        if ($p->id == $review->producto_servicio_id) {
                            $producto_nombre = $p->nombre;
                            break;
                        }
                    }
                    echo esc_html($producto_nombre);
                    ?>
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>
