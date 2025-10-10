<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🎯 <?php echo esc_html__('Insertar Shortcodes Automáticamente', 'aprendiz-reviews'); ?></h1>

    <p><?php echo esc_html__('Inserta automáticamente shortcodes de reseñas en productos WooCommerce que ya tengan reseñas validadas.', 'aprendiz-reviews'); ?></p>

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
            <h3 style="margin-top: 0; color: #3582c4;">ℹ️ <?php echo esc_html__('Shortcodes ya insertados', 'aprendiz-reviews'); ?></h3>
            <p><strong><?php echo esc_html__('¿Quieres cambiar la posición de las reseñas?', 'aprendiz-reviews'); ?></strong></p>
            <p><?php echo esc_html__('Para mover los shortcodes a una nueva posición, primero debes', 'aprendiz-reviews'); ?> <strong><?php echo esc_html__('limpiar todos los hooks', 'aprendiz-reviews'); ?></strong> <?php echo esc_html__('y luego volver a insertarlos en la nueva posición deseada.', 'aprendiz-reviews'); ?></p>
            <p style="color: #666; font-size: 13px;">
                <strong><?php echo esc_html__('Posición actual:', 'aprendiz-reviews'); ?></strong>
                <?php
                $current_positions = array_unique(array_column($saved_hooks, 'position'));
                $position_labels = array(
                    'after_title' => esc_html__('Después del título', 'aprendiz-reviews'),
                    'after_price' => esc_html__('Después del precio', 'aprendiz-reviews'),
                    'after_description' => esc_html__('Después de la descripción', 'aprendiz-reviews'),
                    'after_add_to_cart' => esc_html__('Después del botón de comprar', 'aprendiz-reviews'),
                    'after_summary' => esc_html__('Después del resumen', 'aprendiz-reviews'),
                    // keep same keys that your plugin may use; others will fallback to the raw key
                    'before_product' => esc_html__('Antes de todo el producto', 'aprendiz-reviews'),
                    'before_add_to_cart' => esc_html__('Antes del botón de comprar', 'aprendiz-reviews'),
                    'after_meta' => esc_html__('Después de categorías/etiquetas', 'aprendiz-reviews'),
                    'after_tabs' => esc_html__('Después de las pestañas', 'aprendiz-reviews'),
                    'after_product' => esc_html__('Después de todo el producto', 'aprendiz-reviews'),
                );
                foreach ($current_positions as $pos) {
                    echo isset($position_labels[$pos]) ? $position_labels[$pos] : esc_html($pos);
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Botón para limpiar TODOS los hooks -->
    <div style="margin-bottom: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
        <h3 style="margin-top: 0;">🧹 <?php echo esc_html__('Limpieza de Hooks', 'aprendiz-reviews'); ?></h3>
        <p><?php echo esc_html__('Si ves carruseles duplicados o quieres cambiar la posición, usa este botón para limpiar completamente todos los hooks:', 'aprendiz-reviews'); ?></p>
        <form method="post" style="display: inline;">
            <input type="hidden" name="clean_all_hooks" value="1">
            <input
                type="submit"
                class="button button-secondary"
                value="<?php echo esc_attr__('🧹 Limpiar TODOS los Hooks', 'aprendiz-reviews'); ?>"
                onclick="return confirm('<?php echo esc_js(__("¿Estás seguro? Esto removerá todos los shortcodes insertados automáticamente. Tendrás que volver a insertarlos.", "aprendiz-reviews")); ?>');">
        </form>
        <?php if (!empty($saved_hooks)): ?>
            <p style="color: #856404; margin-top: 10px; font-size: 13px;">
                <strong><?php echo sprintf(esc_html__('Actualmente tienes %s shortcode(s) insertado(s).', 'aprendiz-reviews'), esc_html(count($saved_hooks))); ?></strong>
            </p>
        <?php endif; ?>
    </div>

    <?php if (!class_exists('WooCommerce')): ?>
        <div class="notice notice-error">
            <p><strong><?php echo esc_html__('WooCommerce no encontrado:', 'aprendiz-reviews'); ?></strong> <?php echo esc_html__('Esta funcionalidad requiere WooCommerce.', 'aprendiz-reviews'); ?></p>
        </div>

    <?php elseif (empty($products_with_reviews)): ?>
        <div class="notice notice-info">
            <p>
                <?php echo esc_html__('No se encontraron productos con reseñas validadas.', 'aprendiz-reviews'); ?>
                &nbsp;
                <a href="<?php echo esc_url(admin_url('admin.php?page=gestionar-resenas')); ?>"><?php echo esc_html__('Validar reseñas', 'aprendiz-reviews'); ?></a> <?php echo esc_html__('primero.', 'aprendiz-reviews'); ?>
            </p>
        </div>

    <?php else: ?>
        <form method="post" id="shortcode-form">
            <?php wp_nonce_field('insert_shortcodes_nonce'); ?>

            <!-- Selector de posición -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 style="padding: 15px;">📍 <?php echo esc_html__('¿Dónde quieres mostrar las reseñas?', 'aprendiz-reviews'); ?></h2>
                <div style="padding: 0 15px 15px;">
                    <?php if (!empty($saved_hooks)): ?>
                        <div style="background: #ffebee; border: 1px solid #f44336; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                            <strong>⚠️ <?php echo esc_html__('Nota:', 'aprendiz-reviews'); ?></strong> <?php echo esc_html__('Ya tienes shortcodes insertados. Si seleccionas una posición diferente, primero se limpiarán todos los hooks existentes.', 'aprendiz-reviews'); ?>
                        </div>
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="before_product" required style="margin-right: 8px;">
                            <strong>🏁 <?php echo esc_html__('Antes de todo el producto', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Al inicio de la página, antes de cualquier contenido', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_before_single_product', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_title" required style="margin-right: 8px;">
                            <strong>🏷️ <?php echo esc_html__('Después del título', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Justo debajo del nombre del producto', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_template_single_title', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_price" required style="margin-right: 8px;">
                            <strong>💰 <?php echo esc_html__('Después del precio', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Debajo del precio del producto', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_template_single_price', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_description" required style="margin-right: 8px;">
                            <strong>📄 <?php echo esc_html__('Después de la descripción corta', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Debajo de la descripción breve', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_template_single_excerpt', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="before_add_to_cart" required style="margin-right: 8px;">
                            <strong>🛒 <?php echo esc_html__('Antes del botón de comprar', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Antes del formulario "Añadir al carrito"', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_before_add_to_cart_form', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_add_to_cart" required style="margin-right: 8px;">
                            <strong>🛒 <?php echo esc_html__('Después del botón de comprar', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Después del formulario "Añadir al carrito"', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_after_add_to_cart_form', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_meta" required style="margin-right: 8px;">
                            <strong>🏷️ <?php echo esc_html__('Después de categorías/etiquetas', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Debajo de la información de categorías y etiquetas', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_template_single_meta', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_summary" required style="margin-right: 8px;">
                            <strong>⬇️ <?php echo esc_html__('Después de todo el resumen', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Debajo de toda la información del producto', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_after_single_product_summary', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_tabs" required style="margin-right: 8px;">
                            <strong>📋 <?php echo esc_html__('Después de las pestañas', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Debajo de las pestañas de descripción/reseñas', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_product_after_tabs', 'aprendiz-reviews'); ?></small>
                        </label>

                        <label style="border: 2px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; display: block;">
                            <input type="radio" name="hook_position" value="after_product" required style="margin-right: 8px;">
                            <strong>🏁 <?php echo esc_html__('Después de todo el producto', 'aprendiz-reviews'); ?></strong>
                            <br><small style="color: #666;"><?php echo esc_html__('Al final de la página, después de todo el contenido', 'aprendiz-reviews'); ?></small>
                            <br><small style="color: #999; font-size: 11px;"><?php echo esc_html__('Hook: woocommerce_after_single_product', 'aprendiz-reviews'); ?></small>
                        </label>

                    </div>


                </div>
            </div>

            <!-- Lista de productos -->
            <div class="postbox">
                <h2 style="padding: 15px;">📦 <?php echo sprintf(esc_html__('Productos con Reseñas (%s)', 'aprendiz-reviews'), esc_html(count($products_with_reviews))); ?></h2>
                <div style="padding: 0 15px 15px;">

                    <div style="margin-bottom: 15px;">
                        <label>
                            <input type="checkbox" id="select-all"> <strong><?php echo esc_html__('Seleccionar todos disponibles', 'aprendiz-reviews'); ?></strong>
                        </label>
                        <span style="margin-left: 20px; color: #666;">
                            <?php
                            $available = count(array_filter($products_with_reviews, function ($p) {
                                return !$p['already_inserted'];
                            }));
                            echo sprintf('(%s %s)', esc_html($available), esc_html__('disponibles para insertar', 'aprendiz-reviews'));
                            ?>
                        </span>
                    </div>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;"><?php echo esc_html__('Insertar', 'aprendiz-reviews'); ?></th>
                                <th><?php echo esc_html__('Producto WooCommerce', 'aprendiz-reviews'); ?></th>
                                <th style="width: 60px;"><?php echo esc_html__('ID WC', 'aprendiz-reviews'); ?></th>
                                <th style="width: 80px;"><?php echo esc_html__('Reseñas', 'aprendiz-reviews'); ?></th>
                                <th style="width: 180px;"><?php echo esc_html__('Shortcode', 'aprendiz-reviews'); ?></th>
                                <th style="width: 100px;"><?php echo esc_html__('Estado', 'aprendiz-reviews'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products_with_reviews as $product): ?>
                                <tr <?php echo $product['already_inserted'] ? 'style="opacity: 0.5;"' : ''; ?>>
                                    <td>
                                        <?php if (!$product['already_inserted']): ?>
                                            <input type="checkbox" name="selected_products[]" value="<?php echo esc_attr($product['our_product']->id); ?>" class="product-checkbox">
                                        <?php else: ?>
                                            <span style="color: #46b450;" title="<?php echo esc_attr__('Ya insertado', 'aprendiz-reviews'); ?>">✅</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($product['wc_product']->get_name()); ?></strong>
                                    </td>
                                    <td>
                                        <code style="font-size: 11px;"><?php echo esc_html($product['wc_product_id']); ?></code>
                                    </td>
                                    <td>
                                        <span style="background: #46b450; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                            <?php echo esc_html($product['review_count']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <code style="font-size: 11px;">[<?php echo esc_html($product['shortcode']); ?>]</code>
                                    </td>
                                    <td>
                                        <?php if ($product['already_inserted']): ?>
                                            <span style="color: #46b450; font-weight: bold; font-size: 12px;"><?php echo esc_html__('✅ Insertado', 'aprendiz-reviews'); ?></span>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 12px;"><?php echo esc_html__('➕ Pendiente', 'aprendiz-reviews'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p class="submit">
                        <input
                            type="submit"
                            name="insert_shortcodes"
                            class="button button-primary"
                            value="<?php echo esc_attr__('🎯 Insertar Shortcodes (Limpia Duplicados Automáticamente)', 'aprendiz-reviews'); ?>"
                            id="submit-btn"
                            disabled>
                    </p>

                </div>
            </div>
        </form>
    <?php endif; ?>

    <!-- Panel de información técnica -->
    <?php if (!empty($saved_hooks) && current_user_can('manage_options')): ?>
        <div class="postbox" style="margin-top: 20px;">
            <h3 style="padding: 15px; margin: 0; background: #f9f9f9;">🔧 <?php echo esc_html__('Información Técnica (Debug)', 'aprendiz-reviews'); ?></h3>
            <div style="padding: 15px;">
                <p><strong><?php echo esc_html__('Hooks actualmente guardados:', 'aprendiz-reviews'); ?></strong></p>
                <table class="wp-list-table widefat fixed striped" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Shortcode', 'aprendiz-reviews'); ?></th>
                            <th><?php echo esc_html__('Producto ID', 'aprendiz-reviews'); ?></th>
                            <th><?php echo esc_html__('Hook', 'aprendiz-reviews'); ?></th>
                            <th><?php echo esc_html__('Prioridad', 'aprendiz-reviews'); ?></th>
                            <th><?php echo esc_html__('Posición', 'aprendiz-reviews'); ?></th>
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