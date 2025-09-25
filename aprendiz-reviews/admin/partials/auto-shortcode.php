<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🎯 Insertar Shortcodes Automáticamente</h1>
    
    <p>Inserta automáticamente shortcodes de reseñas en productos WooCommerce que ya tengan reseñas validadas.</p>
    
    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>
    
    <!-- Aviso importante sobre cambios de posición -->
    <?php 
    $saved_hooks = get_option('aprendiz_reviews_auto_hooks', array());
    if (!empty($saved_hooks)): 
    ?>
        <div style="margin-bottom: 20px; padding: 15px; background: #e7f3ff; border: 1px solid #3582c4; border-radius: 5px;">
            <h3 style="margin-top: 0; color: #3582c4;">ℹ️ Shortcodes ya insertados</h3>
            <p><strong>¿Quieres cambiar la posición de las reseñas?</strong></p>
            <p>Para mover los shortcodes a una nueva posición, primero debes <strong>limpiar todos los hooks</strong> y luego volver a insertarlos en la nueva posición deseada.</p>
            <p style="color: #666; font-size: 13px;">
                <strong>Posición actual:</strong> 
                <?php 
                $current_positions = array_unique(array_column($saved_hooks, 'position'));
                $position_labels = array(
                    'after_title' => 'Después del título',
                    'after_price' => 'Después del precio',
                    'after_description' => 'Después de la descripción',
                    'after_add_to_cart' => 'Después del botón de comprar',
                    'after_summary' => 'Después del resumen'
                );
                foreach ($current_positions as $pos) {
                    echo isset($position_labels[$pos]) ? $position_labels[$pos] : $pos;
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <!-- Botón para limpiar TODOS los hooks -->
    <div style="margin-bottom: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
        <h3 style="margin-top: 0;">🧹 Limpieza de Hooks</h3>
        <p>Si ves carruseles duplicados o quieres cambiar la posición, usa este botón para limpiar completamente todos los hooks:</p>
        <form method="post" style="display: inline;">
            <input type="hidden" name="clean_all_hooks" value="1">
            <input type="submit" class="button button-secondary" value="🧹 Limpiar TODOS los Hooks" onclick="return confirm('¿Estás seguro? Esto removerá todos los shortcodes insertados automáticamente. Tendrás que volver a insertarlos.');">
        </form>
        <?php if (!empty($saved_hooks)): ?>
            <p style="color: #856404; margin-top: 10px; font-size: 13px;">
                <strong>Actualmente tienes <?php echo count($saved_hooks); ?> shortcode(s) insertado(s).</strong>
            </p>
        <?php endif; ?>
    </div>
    
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
        <form method="post" id="shortcode-form">
            <?php wp_nonce_field('insert_shortcodes_nonce'); ?>
            
            <!-- Selector de posición -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 style="padding: 15px;">📍 ¿Dónde quieres mostrar las reseñas?</h2>
                <div style="padding: 0 15px 15px;">
                    <?php if (!empty($saved_hooks)): ?>
                        <div style="background: #ffebee; border: 1px solid #f44336; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                            <strong>⚠️ Nota:</strong> Ya tienes shortcodes insertados. Si seleccionas una posición diferente, primero se limpiarán todos los hooks existentes.
                        </div>
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
    
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="before_product" required style="margin-right: 8px;">
                            <strong>🏁 Antes de todo el producto</strong>
                            <br><small style="color: #666;">Al inicio de la página, antes de cualquier contenido</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_before_single_product</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_title" required style="margin-right: 8px;">
                            <strong>🏷️ Después del título</strong>
                            <br><small style="color: #666;">Justo debajo del nombre del producto</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_template_single_title</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_price" required style="margin-right: 8px;">
                            <strong>💰 Después del precio</strong>
                            <br><small style="color: #666;">Debajo del precio del producto</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_template_single_price</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_description" required style="margin-right: 8px;">
                            <strong>📄 Después de la descripción corta</strong>
                            <br><small style="color: #666;">Debajo de la descripción breve</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_template_single_excerpt</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="before_add_to_cart" required style="margin-right: 8px;">
                            <strong>🛒 Antes del botón de comprar</strong>
                            <br><small style="color: #666;">Antes del formulario "Añadir al carrito"</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_before_add_to_cart_form</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_add_to_cart" required style="margin-right: 8px;">
                            <strong>🛒 Después del botón de comprar</strong>
                            <br><small style="color: #666;">Después del formulario "Añadir al carrito"</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_after_add_to_cart_form</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_meta" required style="margin-right: 8px;">
                            <strong>🏷️ Después de categorías/etiquetas</strong>
                            <br><small style="color: #666;">Debajo de la información de categorías y etiquetas</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_template_single_meta</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_summary" required style="margin-right: 8px;">
                            <strong>⬇️ Después de todo el resumen</strong>
                            <br><small style="color: #666;">Debajo de toda la información del producto</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_after_single_product_summary</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_tabs" required style="margin-right: 8px;">
                            <strong>📋 Después de las pestañas</strong>
                            <br><small style="color: #666;">Debajo de las pestañas de descripción/reseñas</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_product_after_tabs</small>
                        </label>
                        
                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_product" required style="margin-right: 8px;">
                            <strong>🏁 Después de todo el producto</strong>
                            <br><small style="color: #666;">Al final de la página, después de todo el contenido</small>
                            <br><small style="color: #999; font-size: 11px;">Hook: woocommerce_after_single_product</small>
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
                            <input type="checkbox" id="select-all"> <strong>Seleccionar todos disponibles</strong>
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
                                <th>Producto WooCommerce</th>
                                <th style="width: 60px;">ID WC</th>
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
                                            <input type="checkbox" name="selected_products[]" value="<?php echo esc_attr($product['our_product']->id); ?>" class="product-checkbox">
                                        <?php else: ?>
                                            <span style="color: #46b450;" title="Ya insertado">✅</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($product['wc_product']->get_name()); ?></strong>
                                    </td>
                                    <td>
                                        <code style="font-size: 11px;"><?php echo $product['wc_product_id']; ?></code>
                                    </td>
                                    <td>
                                        <span style="background: #46b450; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                            <?php echo $product['review_count']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <code style="font-size: 11px;">[<?php echo esc_html($product['shortcode']); ?>]</code>
                                    </td>
                                    <td>
                                        <?php if ($product['already_inserted']): ?>
                                            <span style="color: #46b450; font-weight: bold; font-size: 12px;">✅ Insertado</span>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 12px;">➕ Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="insert_shortcodes" class="button button-primary" value="🎯 Insertar Shortcodes (Limpia Duplicados Automáticamente)" id="submit-btn" disabled>
                    </p>
                    
                </div>
            </div>
        </form>
    <?php endif; ?>
    
    <!-- Panel de información técnica -->
    <?php if (!empty($saved_hooks) && current_user_can('manage_options')): ?>
        <div class="postbox" style="margin-top: 20px;">
            <h3 style="padding: 15px; margin: 0; background: #f9f9f9;">🔧 Información Técnica (Debug)</h3>
            <div style="padding: 15px;">
                <p><strong>Hooks actualmente guardados:</strong></p>
                <table class="wp-list-table widefat fixed striped" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th>Shortcode</th>
                            <th>Producto ID</th>
                            <th>Hook</th>
                            <th>Prioridad</th>
                            <th>Posición</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saved_hooks as $hook): ?>
                            <tr>
                                <td><code><?php echo esc_html($hook['shortcode']); ?></code></td>
                                <td><?php echo esc_html($hook['wc_product_id']); ?></td>
                                <td><?php echo esc_html($hook['hook_name']); ?></td>
                                <td><?php echo esc_html($hook['priority']); ?></td>
                                <td><?php echo esc_html($hook['position']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const submitBtn = document.getElementById('submit-btn');
    const radioButtons = document.querySelectorAll('input[name="hook_position"]');
    
    function validateForm() {
        const hasCheckedProduct = Array.from(checkboxes).some(cb => cb.checked);
        const hasSelectedPosition = Array.from(radioButtons).some(rb => rb.checked);
        submitBtn.disabled = !(hasCheckedProduct && hasSelectedPosition);
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            validateForm();
        });
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateForm);
    });
    
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('label').forEach(label => {
                if (label.querySelector('input[name="hook_position"]')) {
                    label.style.borderColor = '#ddd';
                    label.style.backgroundColor = 'white';
                }
            });
            
            this.closest('label').style.borderColor = '#0073aa';
            this.closest('label').style.backgroundColor = '#f0f8ff';
            
            validateForm();
        });
    });
    
    validateForm();
});
</script>