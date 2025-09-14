<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🛒 Importar desde WooCommerce</h1>
    
    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!class_exists('WooCommerce')): ?>
        <div class="notice notice-error">
            <p><strong>WooCommerce no encontrado:</strong> Este importador requiere que WooCommerce esté instalado y activado.</p>
        </div>
        <p><a href="<?php echo admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'); ?>" class="button button-primary">Instalar WooCommerce</a></p>
    
    <?php elseif (empty($woocommerce_products)): ?>
        <div class="notice notice-info">
            <p>No se encontraron productos en WooCommerce. <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>">Crear tu primer producto</a></p>
        </div>
    
    <?php else: ?>
        <p>Selecciona los productos de WooCommerce para crear automáticamente productos de reseñas con sus shortcodes.</p>
        
        <form method="post">
            <div style="margin: 20px 0;">
                <label>
                    <input type="checkbox" id="select-all"> <strong>Seleccionar todos</strong>
                </label>
                <span style="margin-left: 20px; color: #666;">
                    (<?php echo count(array_filter($woocommerce_products, function($p) { return !$p['already_exists']; })); ?> disponibles para importar)
                </span>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;">Importar</th>
                        <th style="width: 60px;">Imagen</th>
                        <th>Producto</th>
                        <th style="width: 100px;">Precio</th>
                        <th style="width: 200px;">Shortcode</th>
                        <th style="width: 80px;">Estado</th>
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
                                <?php echo $product['price'] ? wc_price($product['price']) : 'Gratis'; ?>
                            </td>
                            <td>
                                <code>[<?php echo esc_html($product['suggested_shortcode']); ?>]</code>
                            </td>
                            <td>
                                <?php if ($product['already_exists']): ?>
                                    <span style="color: #46b450;">✅ Existe</span>
                                <?php else: ?>
                                    <span style="color: #999;">➕ Nuevo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <p class="submit">
                <input type="submit" name="import_selected" class="button button-primary" value="📥 Importar Seleccionados">
                <span style="margin-left: 15px; color: #666;">
                    Se crearán productos de reseñas con shortcodes únicos para cada uno.
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
