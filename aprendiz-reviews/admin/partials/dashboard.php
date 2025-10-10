<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🎯 <?php echo esc_html__('Aprendiz Reviews - Dashboard', 'aprendiz-reviews'); ?></h1>

    <?php if (isset($migration_date) && $migration_date): ?>
        <div class="notice notice-info">
            <p><strong>ℹ️ <?php echo esc_html__('Migración completada:', 'aprendiz-reviews'); ?></strong> <?php echo date('d/m/Y H:i', strtotime($migration_date)); ?></p>
            <p><?php echo sprintf(
                    esc_html__('Todas las reseñas anteriores están disponibles en el producto "General" con shortcode %s', 'aprendiz-reviews'),
                    '<code>[reviews_general]</code>'
                ); ?></p>
        </div>
    <?php endif; ?>

    <div class="dashboard-widgets-wrap">
        <div class="metabox-holder">
            <!-- Estadísticas -->
            <div class="postbox">
                <h2><span>📊 <?php echo esc_html__('Estadísticas', 'aprendiz-reviews'); ?></span></h2>
                <div class="inside">
                    <p><strong><?php echo esc_html__('Productos activos:', 'aprendiz-reviews'); ?></strong> <?php echo $product_stats['active']; ?></p>
                    <p><strong><?php echo esc_html__('Total productos:', 'aprendiz-reviews'); ?></strong> <?php echo $product_stats['total']; ?></p>
                    <p><strong><?php echo esc_html__('Total reseñas:', 'aprendiz-reviews'); ?></strong> <?php echo $review_stats['total']; ?></p>
                    <p><strong><?php echo esc_html__('Reseñas validadas:', 'aprendiz-reviews'); ?></strong> <?php echo $review_stats['validated']; ?></p>
                    <p><strong><?php echo esc_html__('Reseñas pendientes:', 'aprendiz-reviews'); ?></strong> <?php echo $review_stats['pending']; ?></p>
                </div>
            </div>

            <!-- Shortcodes disponibles -->
            <div class="postbox">
                <h2><span>📋 <?php echo esc_html__('Shortcodes Disponibles', 'aprendiz-reviews'); ?></span></h2>
                <div class="inside">
                    <?php foreach ($products as $producto): ?>
                        <p>
                            <code>[<?php echo $producto->shortcode; ?>]</code> - <?php echo esc_html($producto->nombre); ?>
                            <small style="color: #666;">(<?php echo sprintf(
                                                                esc_html(_n('%s reseña', '%s reseñas', Aprendiz_Reviews_Product::count_reviews($producto->id), 'aprendiz-reviews')),
                                                                Aprendiz_Reviews_Product::count_reviews($producto->id)
                                                            ); ?>)</small>
                        </p>
                    <?php endforeach; ?>

                    <hr style="margin: 15px 0;">
                    <p><code>[reviews_form]</code> - <?php echo esc_html__('Formulario para capturar reseñas', 'aprendiz-reviews'); ?></p>
                    <p><code>[reviews_form titulo="<?php echo esc_attr__('Tu título personalizado', 'aprendiz-reviews'); ?>"]</code> - <?php echo esc_html__('Formulario con título personalizado', 'aprendiz-reviews'); ?></p>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="postbox">
                <h2><span>⚡ <?php echo esc_html__('Acciones Rápidas', 'aprendiz-reviews'); ?></span></h2>
                <div class="inside">
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=añadir-producto'); ?>" class="button button-primary">
                            ➕ <?php echo esc_html__('Crear Producto', 'aprendiz-reviews'); ?>
                        </a>
                    </p>
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=añadir-resena'); ?>" class="button button-primary">
                            ✏️ <?php echo esc_html__('Añadir Reseña', 'aprendiz-reviews'); ?>
                        </a>
                    </p>
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=gestionar-resenas&validado=0'); ?>" class="button button-secondary">
                            ⏳ <?php echo sprintf(
                                    esc_html__('Ver Reseñas Pendientes (%s)', 'aprendiz-reviews'),
                                    $review_stats['pending']
                                ); ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>