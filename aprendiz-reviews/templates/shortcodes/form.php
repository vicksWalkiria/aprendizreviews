<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="reviews-frontend-form" class="reviews-form-container">
    <h3><?php echo esc_html($atts['titulo']); ?></h3>
    
    <form id="review-form-frontend" method="post" style="display: block;">
        <div class="form-group">
            <label for="rf_nombre">Nombre *</label>
            <input type="text" id="rf_nombre" name="nombre" required>
        </div>
        
        <div class="form-group">
            <label for="rf_valoracion">Valoración *</label>
            <div class="rating-stars" id="rating-container">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <span class="star" data-rating="<?php echo $i; ?>">★</span>
                <?php endfor; ?>
            </div>
            <input type="hidden" id="rf_valoracion" name="valoracion" value="5" required>
        </div>
        
        <div class="form-group">
            <label for="rf_producto">Producto/Servicio *</label>
            <select id="rf_producto" name="producto_servicio_id" required>
                <option value="">Selecciona una opción</option>
                <?php foreach($products as $producto): ?>
                    <option value="<?php echo $producto->id; ?>"><?php echo esc_html($producto->nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="rf_texto">Cuéntanos tu experiencia *</label>
            <textarea id="rf_texto" name="texto" rows="4" required placeholder="Comparte los detalles de tu experiencia..."></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" id="submit-review">Enviar Reseña</button>
        </div>
    </form>
    
    <!-- Mensaje de agradecimiento (oculto inicialmente) -->
    <div id="thank-you-message" style="display: none;">
        <h3>¡Gracias por tu reseña! 🙏</h3>
        <p>Tu opinión es muy importante para nosotros. Hemos recibido tu reseña y la revisaremos pronto.</p>
    </div>
    
    <div id="form-response"></div>
</div>
