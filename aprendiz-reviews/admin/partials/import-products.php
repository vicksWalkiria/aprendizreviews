<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html__('🛒 Importar desde WooCommerce', 'aprendiz-reviews'); ?></h1>

    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!class_exists('WooCommerce')): ?>
        <div class="notice notice-error">
            <p><strong><?php echo esc_html__('WooCommerce no encontrado:', 'aprendiz-reviews'); ?></strong> <?php echo esc_html__('Este importador requiere que WooCommerce esté instalado y activado.', 'aprendiz-reviews'); ?></p>
        </div>
        <p><a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'); ?>" class="button button-primary">
                <?php echo esc_html__('Instalar WooCommerce', 'aprendiz-reviews'); ?>
            </a></p>

    <?php elseif (empty($woocommerce_products)): ?>
        <div class="notice notice-info">
            <p><?php echo esc_html__('No se encontraron productos en WooCommerce.', 'aprendiz-reviews'); ?>
                <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>">
                    <?php echo esc_html__('Crear tu primer producto', 'aprendiz-reviews'); ?>
                </a>
            </p>
        </div>

    <?php else: ?>
        <p><?php echo esc_html__('Selecciona los productos de WooCommerce para crear automáticamente productos de reseñas con sus shortcodes.', 'aprendiz-reviews'); ?></p>

        <form method="post">
            <div style="margin: 20px 0;">
                <label>
                    <input type="checkbox" id="select-all"> <strong><?php echo esc_html__('Seleccionar todos', 'aprendiz-reviews'); ?></strong>
                </label>
                <span style="margin-left: 20px; color: #666;">
                    <?php echo sprintf(
                        esc_html__('(%d disponibles para importar)', 'aprendiz-reviews'),
                        count(array_filter($woocommerce_products, function ($p) {
                            return !$p['already_exists'];
                        }))
                    ); ?>
                </span>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;"><?php echo esc_html__('Importar', 'aprendiz-reviews'); ?></th>
                        <th style="width: 60px;"><?php echo esc_html__('Imagen', 'aprendiz-reviews'); ?></th>
                        <th><?php echo esc_html__('Producto', 'aprendiz-reviews'); ?></th>
                        <th style="width: 100px;"><?php echo esc_html__('Precio', 'aprendiz-reviews'); ?></th>
                        <th style="width: 200px;"><?php echo esc_html__('Shortcode', 'aprendiz-reviews'); ?></th>
                        <th style="width: 80px;"><?php echo esc_html__('Estado', 'aprendiz-reviews'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($woocommerce_products as $product): ?>
                        <tr <?php echo $product['already_exists'] ? 'style="opacity: 0.5;"' : ''; ?>>
                            <td>
                                <?php if (!$product['already_exists']): ?>
                                    <input type="checkbox" name="selected_products[]" value="<?php echo $product['id']; ?>">
                                <?php else: ?>
                                    <span style="color: #46b450;">✅</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($product['image']): ?>
                                    <img src="<?php echo esc_url($product['image']); ?>"
                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">📦</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo esc_html($product['name']); ?></strong>
                                <?php if ($product['description']): ?>
                                    <br><small style="color: #666;"><?php echo esc_html($product['description']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $product['price'] ? wc_price($product['price']) : esc_html__('Gratis', 'aprendiz-reviews'); ?>
                            </td>
                            <td>
                                <code>[<?php echo esc_html($product['suggested_shortcode']); ?>]</code>
                            </td>
                            <td>
                                <?php if ($product['already_exists']): ?>
                                    <span style="color: #46b450;"><?php echo esc_html__('✅ Existe', 'aprendiz-reviews'); ?></span>
                                <?php else: ?>
                                    <span style="color: #999;"><?php echo esc_html__('➕ Nuevo', 'aprendiz-reviews'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" name="import_selected" class="button button-primary" value="<?php echo esc_attr__('📥 Importar Seleccionados', 'aprendiz-reviews'); ?>">
                <span style="margin-left: 15px; color: #666;">
                    <?php echo esc_html__('Se crearán productos de reseñas con shortcodes únicos para cada uno.', 'aprendiz-reviews'); ?>
                </span>
            </p>
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
</script>