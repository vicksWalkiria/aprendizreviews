<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="reviews-frontend-form" class="reviews-form-container">
    <h3><?php echo esc_html($atts['titulo']); ?></h3>

    <form id="review-form-frontend" method="post" style="display: block;">
        <div class="form-group">
            <label for="rf_nombre"><?php echo esc_html__('Nombre', 'aprendiz-reviews'); ?> *</label>
            <input type="text" id="rf_nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="rf_valoracion"><?php echo esc_html__('Valoración', 'aprendiz-reviews'); ?> *</label>
            <div class="rating-stars" id="rating-container">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star" data-rating="<?php echo esc_attr($i); ?>">★</span>
                <?php endfor; ?>
            </div>
            <input type="hidden" id="rf_valoracion" name="valoracion" value="5" required>
        </div>

        <div class="form-group">
            <label for="rf_producto"><?php echo esc_html__('Producto', 'aprendiz-reviews'); ?> *</label>
            <select id="rf_producto" name="producto_servicio_id" required>
                <option value=""><?php echo esc_html__('Selecciona una opción', 'aprendiz-reviews'); ?></option>
                <?php foreach ($products as $producto): ?>
                    <option value="<?php echo esc_attr($producto->id); ?>"><?php echo esc_html($producto->nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="rf_texto"><?php echo esc_html__('Cuéntanos tu experiencia', 'aprendiz-reviews'); ?> *</label>
            <textarea
                id="rf_texto"
                name="texto"
                rows="4"
                required
                placeholder="<?php echo esc_attr__('Comparte los detalles de tu experiencia...', 'aprendiz-reviews'); ?>">
            </textarea>
        </div>

        <div class="form-group">
            <button type="submit" id="submit-review">
                <?php echo esc_html__('Enviar Reseña', 'aprendiz-reviews'); ?>
            </button>
        </div>
    </form>

    <!-- Mensaje de agradecimiento (oculto inicialmente) -->
    <div id="thank-you-message" style="display: none;">
        <h3><?php echo esc_html__('¡Gracias por tu reseña! 🙏', 'aprendiz-reviews'); ?></h3>
        <p><?php echo esc_html__('Tu opinión es muy importante para nosotros. Hemos recibido tu reseña y la revisaremos pronto.', 'aprendiz-reviews'); ?></p>
    </div>

    <div id="form-response"></div>
</div>