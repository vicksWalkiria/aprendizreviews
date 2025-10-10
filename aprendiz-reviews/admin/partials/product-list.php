<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html__('📦 Gestionar Productos', 'aprendiz-reviews'); ?></h1>

    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <a href="<?php echo admin_url('admin.php?page=añadir-producto'); ?>" class="button button-primary">
        <?php echo esc_html__('➕ Añadir Nuevo', 'aprendiz-reviews'); ?>
    </a>
    <br><br>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 60px;"><?php echo esc_html__('ID', 'aprendiz-reviews'); ?></th>
                <th><?php echo esc_html__('Nombre', 'aprendiz-reviews'); ?></th>
                <th style="width: 120px;"><?php echo esc_html__('Tipo', 'aprendiz-reviews'); ?></th>
                <th style="width: 200px;"><?php echo esc_html__('Shortcode', 'aprendiz-reviews'); ?></th>
                <th style="width: 100px;"><?php echo esc_html__('Estado', 'aprendiz-reviews'); ?></th>
                <th style="width: 80px;"><?php echo esc_html__('Reseñas', 'aprendiz-reviews'); ?></th>
                <th style="width: 200px;"><?php echo esc_html__('Acciones', 'aprendiz-reviews'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $producto): ?>
                <?php
                $total_resenas = Aprendiz_Reviews_Product::count_reviews($producto->id);
                $estado = $producto->activo
                    ? esc_html__('✅ Activo', 'aprendiz-reviews')
                    : esc_html__('❌ Inactivo', 'aprendiz-reviews');
                $row_class = !$producto->activo ? 'style="opacity: 0.6;"' : '';
                ?>
                <tr <?php echo $row_class; ?>>
                    <td><?php echo esc_html($producto->id); ?></td>
                    <td>
                        <strong><?php echo esc_html($producto->nombre); ?></strong>
                        <?php if ($producto->descripcion): ?>
                            <br><small style="color: #666;"><?php echo esc_html(substr($producto->descripcion, 0, 100)); ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($producto->tipo); ?></td>
                    <td><code>[<?php echo esc_html($producto->shortcode); ?>]</code></td>
                    <td><?php echo $estado; ?></td>
                    <td>
                        <?php if ($total_resenas > 0): ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=gestionar-resenas&producto_id=' . $producto->id)); ?>">
                                <?php echo esc_html($total_resenas); ?>
                            </a>
                        <?php else: ?>
                            <?php echo esc_html($total_resenas); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=añadir-producto&editar_id=' . $producto->id)); ?>"
                            class="button button-small">
                            ✏️ <?php echo esc_html__('Editar', 'aprendiz-reviews'); ?>
                        </a>

                        <?php if ($producto->id !== 1): ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=gestionar-productos&eliminar_id=' . $producto->id)); ?>"
                                class="button button-small button-secondary"
                                onclick="return confirm('<?php echo esc_js(__('¿Seguro que quieres desactivar este producto?', 'aprendiz-reviews')); ?>')">
                                🗑️ <?php echo $producto->activo
                                        ? esc_html__('Desactivar', 'aprendiz-reviews')
                                        : esc_html__('Reactivar', 'aprendiz-reviews'); ?>
                            </a>
                        <?php else: ?>
                            <small style="color: #999;"><?php echo esc_html__('Producto por defecto', 'aprendiz-reviews'); ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($products)): ?>
        <div class="notice notice-info">
            <p>
                <?php echo esc_html__('No hay productos creados.', 'aprendiz-reviews'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=añadir-producto')); ?>">
                    <?php echo esc_html__('Crear el primero', 'aprendiz-reviews'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>
</div>