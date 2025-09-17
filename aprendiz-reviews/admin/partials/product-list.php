<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>📦 Gestionar Productos</h1>
    
    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>
    
    <a href="<?php echo admin_url('admin.php?page=añadir-producto'); ?>" class="button button-primary">➕ Añadir Nuevo</a>
    <br><br>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Nombre</th>
                <th style="width: 120px;">Tipo</th>
                <th style="width: 200px;">Shortcode</th>
                <th style="width: 100px;">Estado</th>
                <th style="width: 80px;">Reseñas</th>
                <th style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $producto): ?>
                <?php 
                $total_resenas = Aprendiz_Reviews_Product::count_reviews($producto->id);
                $estado = $producto->activo ? '✅ Activo' : '❌ Inactivo';
                $row_class = !$producto->activo ? 'style="opacity: 0.6;"' : '';
                ?>
                <tr <?php echo $row_class; ?>>
                    <td><?php echo $producto->id; ?></td>
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
                            <a href="<?php echo admin_url('admin.php?page=gestionar-resenas&producto_id=' . $producto->id); ?>">
                                <?php echo $total_resenas; ?>
                            </a>
                        <?php else: ?>
                            <?php echo $total_resenas; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=añadir-producto&editar_id=' . $producto->id); ?>" 
                           class="button button-small">✏️ Editar</a>
                        
                        <?php if ($producto->id !== 1): ?>
                            <a href="<?php echo admin_url('admin.php?page=gestionar-productos&eliminar_id=' . $producto->id); ?>" 
                               class="button button-small button-secondary" 
                               onclick="return confirm('¿Seguro que quieres desactivar este producto?')">
                               🗑️ <?php echo $producto->activo ? 'Desactivar' : 'Reactivar'; ?>
                            </a>
                        <?php else: ?>
                            <small style="color: #999;">Producto por defecto</small>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if (empty($products)): ?>
        <div class="notice notice-info">
            <p>No hay productos creados. <a href="<?php echo admin_url('admin.php?page=añadir-producto'); ?>">Crear el primero</a></p>
        </div>
    <?php endif; ?>
</div>
