<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🎯 Insertar Shortcodes Automáticamente</h1>
    
    <p>Inserta automáticamente shortcodes de reseñas en productos WooCommerce que ya tengan reseñas validadas.</p>
    
    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!class_exists('WooCommerce')): ?>
        <div class="notice notice-error">
            <p><strong>WooCommerce no encontrado:</strong> Esta funcionalidad requiere WooCommerce.</p>
        </div>
    
    <?php elseif (empty($products_with_reviews)): ?>
        <div class="notice notice-info">
            <p>No se encontraron productos con reseñas validadas. 
               <a href="<?php echo admin_url('admin.php?page=gestionar-resenas'); ?>">Validar reseñas</a> primero.
            </p>
        </div>
    
    <?php else: ?>
        <form method="post">
            <!-- Selector de posición -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 style="padding: 15px;">📍 ¿Dónde quieres mostrar las reseñas?</h2>
                <div style="padding: 0 15px 15px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_title" required style="margin-right: 8px;">
                            <strong>🏷️ Después del título</strong>
                            <br><small style="color: #666;">Justo debajo del nombre del producto</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_price" required style="margin-right: 8px;">
                            <strong>💰 Después del precio</strong>
                            <br><small style="color: #666;">Debajo del precio del producto</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_description" required style="margin-right: 8px;">
                            <strong>📄 Después de la descripción corta</strong>
                            <br><small style="color: #666;">Debajo de la descripción breve</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_add_to_cart" required style="margin-right: 8px;">
                            <strong>🛒 Después del botón de comprar</strong>
                            <br><small style="color: #666;">Debajo del botón "Añadir al carrito"</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="in_tabs" required style="margin-right: 8px;">
                            <strong>📋 En las pestañas</strong>
                            <br><small style="color: #666;">Dentro de la pestaña de descripción</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_summary" required style="margin-right: 8px;">
                            <strong>⬇️ Después de todo el resumen</strong>
                            <br><small style="color: #666;">Debajo de toda la información del producto</small>
                        </label>
                        
                    </div>
                </div>
            </div>
            
            <!-- Lista de productos -->
            <div class="postbox">
                <h2 style="padding: 15px;">📦 Productos con Reseñas (<?php echo count($products_with_reviews); ?>)</h2>
                <div style="padding: 0 15px 15px;">
                    
                    <div style="margin-bottom: 15px;">
                        <label>
                            <input type="checkbox" id="select-all"> <strong>Seleccionar todos</strong>
                        </label>
                        <span style="margin-left: 20px; color: #666;">
                            <?php 
                            $available = count(array_filter($products_with_reviews, function($p) { return !$p['already_inserted']; }));
                            echo "($available disponibles para insertar)";
                            ?>
                        </span>
                    </div>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Insertar</th>
                                <th style="width: 60px;">Imagen</th>
                                <th>Producto WooCommerce</th>
                                <th style="width: 80px;">Reseñas</th>
                                <th style="width: 180px;">Shortcode</th>
                                <th style="width: 100px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products_with_reviews as $product): ?>
                                <tr <?php echo $product['already_inserted'] ? 'style="opacity: 0.5;"' : ''; ?>>
                                    <td>
                                        <?php if (!$product['already_inserted']): ?>
                                            <input type="checkbox" name="selected_products[]" value="<?php echo $product['our_product']->id; ?>">                                        <?php else: ?>
                                            <span style="color: #46b450;">✅</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $image = wp_get_attachment_image_url($product['wc_product']->get_image_id(), 'thumbnail');
                                        if ($image): 
                                        ?>
                                            <img src="<?php echo esc_url($image); ?>" 
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">📦</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($product['wc_product']->get_name()); ?></strong>
                                        <br><small style="color: #666;">
                                            <a href="<?php echo get_edit_post_link($product['wc_product']->get_id()); ?>" target="_blank">
                                                Editar producto ↗
                                            </a>
                                        </small>
                                    </td>
                                    <td>
                                        <span style="background: #46b450; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                            <?php echo $product['review_count']; ?> reseñas
                                        </span>
                                    </td>
                                    <td>
                                        <code>[<?php echo esc_html($product['shortcode']); ?>]</code>
                                    </td>
                                    <td>
                                        <?php if ($product['already_inserted']): ?>
                                            <span style="color: #46b450; font-weight: bold;">✅ Insertado</span>
                                        <?php else: ?>
                                            <span style="color: #999;">➕ Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="insert_shortcodes" class="button button-primary" value="🎯 Insertar Shortcodes en Posición Seleccionada">
                    </p>
                    
                    <div style="background: #f0f8ff; padding: 15px; border-radius: 6px; border-left: 4px solid #0073aa; margin-top: 15px;">
                        <h4 style="margin-top: 0;">💡 ¿Cómo funciona?</h4>
                        <ol style="margin-bottom: 0;">
                            <li><strong>Selecciona la posición</strong> donde quieres que aparezcan las reseñas</li>
                            <li><strong>Marca los productos</strong> en los que quieres insertar el shortcode</li>
                            <li><strong>Haz click en "Insertar"</strong> y se añadirán automáticamente</li>
                        </ol>
                        <p style="margin-bottom: 0;"><small><strong>Nota:</strong> Los shortcodes se insertarán usando hooks de WooCommerce o directamente en el contenido según la posición elegida.</small></p>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_products[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Highlight selected position
document.querySelectorAll('input[name="hook_position"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Reset all borders
        document.querySelectorAll('label').forEach(label => {
            if (label.querySelector('input[name="hook_position"]')) {
                label.style.borderColor = '#ddd';
                label.style.backgroundColor = 'white';
            }
        });
        
        // Highlight selected
        this.closest('label').style.borderColor = '#0073aa';
        this.closest('label').style.backgroundColor = '#f0f8ff';
    });
});
</script>
