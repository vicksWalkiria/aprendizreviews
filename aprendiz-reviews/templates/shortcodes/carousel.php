<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="swiper reviews-swiper">
    <div class="swiper-wrapper">
        <?php foreach ($resenas as $r): ?>
            <div class="swiper-slide">
                <div class="review-header">
                    <?php if ($r->avatar_url): ?>
                        <img class="avatar" src="<?php echo esc_url($r->avatar_url); ?>" alt="<?php echo esc_attr($r->nombre); ?>">
                    <?php endif; ?>
                    <div class="review-stars"><?php echo str_repeat('★', $r->valoracion); ?></div>
                </div>
                <div class="review-date"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($r->fecha))); ?></div>
                <p class="review-text">"<?php echo esc_html($r->texto); ?>"</p>
                <p class="review-author">— <?php echo esc_html($r->nombre); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
// Generar Schema JSON-LD
$total = 0;
$review_array = array();

foreach ($resenas as $r) {
    $total += $r->valoracion;

    // Crear array de reseñas para el schema
    $review_array[] = array(
        "@type" => "Review",
        "author" => array(
            "@type" => "Person",
            "name" => $r->nombre
        ),
        "datePublished" => date('Y-m-d', strtotime($r->fecha)),
        "reviewBody" => $r->texto,
        "reviewRating" => array(
            "@type" => "Rating",
            "ratingValue" => (string) $r->valoracion,
            "bestRating" => "5"
        )
    );
}

// Descripción predeterminada traducible
$default_description = esc_html__('Opiniones reales de clientes verificadas.', 'aprendiz-reviews');

// Schema único con todo integrado
$schema = array(
    "@context" => "https://schema.org",
    "@type" => $producto->tipo,
    "name" => $producto->nombre,
    "description" => $producto->descripcion ?: $default_description,
    "aggregateRating" => array(
        "@type" => "AggregateRating",
        "ratingValue" => round($total / count($resenas), 1),
        "bestRating" => "5",
        "ratingCount" => count($resenas)
    ),
    "review" => $review_array
);

// Añadir campos adicionales según tipo
if ($producto->url) {
    $schema['url'] = $producto->url;
}
if ($producto->imagen_url) {
    $schema['image'] = $producto->imagen_url;
}

if ($producto->tipo === 'LocalBusiness') {
    $direccion = get_option('cr_schema_address', '');
    if ($direccion) {
        $schema['address'] = array(
            "@type" => "PostalAddress",
            "streetAddress" => $direccion
        );
    }
}

// Mostrar schema JSON-LD
echo '<script type="application/ld+json">' .
    wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
    '</script>';
?>