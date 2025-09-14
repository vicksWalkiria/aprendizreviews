<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1>🎯 Aprendiz Reviews - Dashboard</h1>
    
    <?php if (isset($migration_date) && $migration_date): ?>
        <div class="notice notice-info">
            <p><strong>ℹ️ Migración completada:</strong> <?php echo date('d/m/Y H:i', strtotime($migration_date)); ?></p>
            <p>Todas las reseñas anteriores están disponibles en el producto "General" con shortcode <code>[reviews_general]</code></p>
        </div>
    <?php endif; ?>
    
    <div class="dashboard-widgets-wrap">
        <div class="metabox-holder">
            <!-- Estadísticas -->
            <div class="postbox">
                <h2><span>📊 Estadísticas</span></h2>
                <div class="inside">
                    <p><strong>Productos activos:</strong> <?php echo $product_stats['active']; ?></p>
                    <p><strong>Total productos:</strong> <?php echo $product_stats['total']; ?></p>
                    <p><strong>Total reseñas:</strong> <?php echo $review_stats['total']; ?></p>
                    <p><strong>Reseñas validadas:</strong> <?php echo $review_stats['validated']; ?></p>
                    <p><strong>Reseñas pendientes:</strong> <?php echo $review_stats['pending']; ?></p>
                </div>
            </div>
            
            <!-- Shortcodes disponibles -->
            <div class="postbox">
                <h2><span>📋 Shortcodes Disponibles</span></h2>
                <div class="inside">
                    <?php foreach ($products as $producto): ?>
                        <p>
                            <code>[<?php echo $producto->shortcode; ?>]</code> - <?php echo esc_html($producto->nombre); ?>
                            <small style="color: #666;">(<?php echo Aprendiz_Reviews_Product::count_reviews($producto->id); ?> reseñas)</small>
                        </p>
                    <?php endforeach; ?>
                    
                    <hr style="margin: 15px 0;">
                    <p><code>[reviews_form]</code> - Formulario para capturar reseñas</p>
                    <p><code>[reviews_form titulo="Tu título personalizado"]</code> - Formulario con título personalizado</p>
                </div>
            </div>
            
            <!-- Acciones rápidas -->
            <div class="postbox">
                <h2><span>⚡ Acciones Rápidas</span></h2>
                <div class="inside">
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=añadir-producto'); ?>" class="button button-primary">
                            ➕ Crear Producto
                        </a>
                    </p>
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=añadir-resena'); ?>" class="button button-primary">
                            ✏️ Añadir Reseña
                        </a>
                    </p>
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=gestionar-resenas&validado=0'); ?>" class="button button-secondary">
                            ⏳ Ver Reseñas Pendientes (<?php echo $review_stats['pending']; ?>)
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
