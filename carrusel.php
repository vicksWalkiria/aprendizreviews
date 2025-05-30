<?php
/*
Plugin Name: Aprendiz Reviews
Plugin URI: https://aprendizdeseo.top/plugin-reviews
Description: Plugin de carrusel de reseñas desarrollado por Aprendiz de SEO. Permite añadir, validar y mostrar reseñas con avatar y shortcode.
Version: 1.2
Author: Aprendiz de SEO
Author URI: https://aprendizdeseo.top
*/


register_activation_hook(__FILE__, 'cr_instalar_tabla');

function cr_instalar_tabla() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'reseñas';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        texto TEXT NOT NULL,
        valoracion TINYINT(1) NOT NULL,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
        validado TINYINT(1) DEFAULT 0,
        avatar_url VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// Añadir página al admin
add_action('admin_menu', function () {
    add_menu_page('Añadir Reseña', 'Añadir Reseña', 'manage_options', 'añadir-resena', 'cr_formulario_admin');
});

// Mostrar formulario
function cr_formulario_admin() {
    ?>
    <div class="wrap">
        <h1>Añadir Reseña</h1>
        <form method="post">
            <table class="form-table">
                <tr><th>Nombre</th><td><input type="text" name="nombre" required></td></tr>
                <tr><th>Valoración (1 a 5)</th><td><input type="number" name="valoracion" min="1" max="5" required></td></tr>
                <tr><th>Texto</th><td><textarea name="texto" rows="4" required></textarea></td></tr>
                <tr>
                <th>Avatar</th>
                    <td>
                        <input type="text" id="avatar_url" name="avatar_url" value="" style="width:60%" readonly>
                        <input type="button" id="upload_avatar_button" class="button" value="Elegir imagen">
                        <div id="avatar_preview" style="margin-top:10px;"></div>
                    </td>
                </tr>
            </table>
            <?php submit_button('Guardar reseña'); ?>
        </form>
    </div>
    <?php
    if ($_POST) {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'reseñas',
            [
                'nombre' => sanitize_text_field($_POST['nombre']),
                'valoracion' => intval($_POST['valoracion']),
                'texto' => sanitize_textarea_field($_POST['texto']),
                'avatar_url' => esc_url_raw($_POST['avatar_url']),
                'validado' => 1
            ]
        );
        echo '<div class="updated"><p>Reseña guardada.</p></div>';
    }
}

add_shortcode('reviews', 'cr_mostrar_reviews');
function cr_mostrar_reviews() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'reseñas';
    $resenas = $wpdb->get_results("SELECT * FROM $tabla WHERE validado = 1 ORDER BY fecha DESC LIMIT 10");

    ob_start(); ?>
    <div class="swiper reviews-swiper">
    <div class="swiper-wrapper">
        <?php foreach ($resenas as $r): ?>
        <div class="swiper-slide">
            <div class="review-header">
            <?php if ($r->avatar_url): ?>
                <img class="avatar" src="<?= esc_url($r->avatar_url) ?>" alt="<?= esc_attr($r->nombre) ?>">
            <?php endif; ?>
            <div class="review-stars"><?= str_repeat('★', $r->valoracion) ?></div>
            </div>
            <div class="review-date"><?= date('d-m-Y', strtotime($r->fecha)) ?></div>
            <p class="review-text">"<?= esc_html($r->texto) ?>"</p>
            <p class="review-author">— <?= esc_html($r->nombre) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    </div>

    <?php
    
    $type = get_option('cr_schema_type', 'Product');
    $name = get_option('cr_schema_name', 'Mi producto o servicio');

    $total = 0;
    $review_ld = [];
    foreach ($resenas as $r) {
        $total += $r->valoracion;
        $review_ld[] = [
            "@type" => "Review",
            "author" => [
                "@type" => "Person",
                "name" => $r->nombre
            ],
            "datePublished" => date('Y-m-d', strtotime($r->fecha)),
            "reviewBody" => $r->texto,
            "reviewRating" => [
                "@type" => "Rating",
                "ratingValue" => $r->valoracion,
                "bestRating" => "5"
            ]
        ];
    }

    $schema = [
        "@context" => "https://schema.org",
        "@type" => $type,
        "name" => $name,
        "description" => "Opiniones reales de clientes verificadas.",
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => round($total / count($resenas), 1),
            "bestRating" => "5",
            "ratingCount" => count($resenas)
        ],
        "review" => $review_ld
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    return ob_get_clean();


}

add_action('wp_enqueue_scripts', function () {
    // Estilos Swiper
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    // Script Swiper
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);

    // Obtener el valor de scroll_delay en milisegundos
    $delay_seconds = intval(get_option('cr_scroll_delay', 3));
    $delay_ms = $delay_seconds * 1000;

    wp_add_inline_style('swiper-css', '
        .reviews-swiper.swiper {
            width: 100%;
            padding: 20px 0;
        }
        .reviews-swiper .swiper-slide {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 1rem;
            width: 280px;
            display: flex;
            flex-direction: column;
            color: #000;
        }
        .reviews-swiper .review-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .reviews-swiper .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eee;
        }
        .reviews-swiper .review-stars {
            color: gold;
            font-size: 1.2em;
        }
        .reviews-swiper .review-date {
            font-size: 0.85em;
            color: #888;
        }
        .reviews-swiper .review-text {
            font-style: italic;
            margin: 0.5rem 0;
            color: #000;
        }
        .reviews-swiper .review-author {
            font-weight: bold;
            color: #000;
        }
    ');


    // JS para inicializar Swiper
    wp_add_inline_script('swiper-js', "
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.swiper', {
                slidesPerView: 'auto',
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: $delay_ms,
                    disableOnInteraction: false,
                },
                grabCursor: true,
                freeMode: true,
            });
        });
    ");
});



add_action('admin_menu', function () {
    add_submenu_page('añadir-resena', 'Gestionar Reseñas', 'Gestionar Reseñas', 'manage_options', 'gestionar-resenas', 'cr_listar_resenas');
    add_submenu_page('añadir-resena', 'Ajustes de Reseñas', 'Ajustes', 'manage_options', 'ajustes-resenas', 'cr_ajustes_resenas');

});

function cr_listar_resenas() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'reseñas';

    // Proceso de borrado
    if (isset($_POST['eliminar_id'])) {
        $wpdb->delete($tabla, ['id' => intval($_POST['eliminar_id'])]);
        echo '<div class="updated"><p>Reseña eliminada.</p></div>';
    }

    // Proceso de validación
    if (isset($_POST['guardar_validaciones'])) {
        foreach ($_POST['validado'] as $id => $valorado) {
            $wpdb->update($tabla, ['validado' => intval($valorado)], ['id' => intval($id)]);
        }
        echo '<div class="updated"><p>Validaciones actualizadas.</p></div>';
    }

    $resenas = $wpdb->get_results("SELECT * FROM $tabla ORDER BY fecha DESC");
    ?>
    <div class="wrap">
        <h1>Gestionar Reseñas</h1>
        <form method="post">
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Texto</th>
                        <th>Valoración</th>
                        <th>Avatar</th>
                        <th>Fecha</th>
                        <th>¿Válida?</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resenas as $r): ?>
                        <tr>
                            <td><?= $r->id ?></td>
                            <td><?= esc_html($r->nombre) ?></td>
                            <td><?= esc_html($r->texto) ?></td>
                            <td><?= str_repeat('★', $r->valoracion) ?></td>
                            <td>
                                <?php if ($r->avatar_url): ?>
                                    <img src="<?= esc_url($r->avatar_url) ?>" style="width:40px; height:40px; border-radius:50%;">
                                <?php endif; ?>
                            </td>
                            <td><?= date('d-m-Y H:i', strtotime($r->fecha)) ?></td>
                            <td>
                                <label>
                                    <input type="radio" name="validado[<?= $r->id ?>]" value="1" <?= $r->validado ? 'checked' : '' ?>> Sí
                                </label><br>
                                <label>
                                    <input type="radio" name="validado[<?= $r->id ?>]" value="0" <?= !$r->validado ? 'checked' : '' ?>> No
                                </label>
                            </td>
                            <td>
                                <button type="submit" name="eliminar_id" value="<?= $r->id ?>" class="button button-danger" onclick="return confirm('¿Seguro que quieres eliminar esta reseña?');">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="submit" name="guardar_validaciones" class="button button-primary">Guardar cambios</button></p>
        </form>
    </div>
    <?php
}

add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_añadir-resena') return;
    wp_enqueue_media();
    wp_add_inline_script('jquery-core', "
        jQuery(document).ready(function($) {
            let frame;
            $('#upload_avatar_button').on('click', function(e) {
                e.preventDefault();
                if (frame) {
                    frame.open();
                    return;
                }
                frame = wp.media({
                    title: 'Seleccionar avatar',
                    button: { text: 'Usar esta imagen' },
                    multiple: false
                });
                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    $('#avatar_url').val(attachment.url);
                    $('#avatar_preview').html('<img src=\"' + attachment.url + '\" style=\"max-width:80px; border-radius:50%;\">');
                });
                frame.open();
            });
        });
    ");
});

function cr_ajustes_resenas() {
    if ($_POST) {
        update_option('cr_schema_type', sanitize_text_field($_POST['schema_type']));
        update_option('cr_schema_name', sanitize_text_field($_POST['schema_name']));
        update_option('cr_scroll_delay', intval($_POST['scroll_delay']));
        echo '<div class="updated"><p>Ajustes guardados.</p></div>';
    }

    $type = get_option('cr_schema_type', 'Product');
    $name = get_option('cr_schema_name', '');
    $scroll_delay = get_option('cr_scroll_delay', 3); // valor por defecto: 3 segundos


    ?>
    <div class="wrap">
        <h1>Ajustes de Schema de Reseñas</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row">Tipo de entidad reseñada</th>
                    <td>
                        <select name="schema_type">
                            <option value="Product" <?= selected($type, 'Product') ?>>Producto</option>
                            <option value="LocalBusiness" <?= selected($type, 'LocalBusiness') ?>>Negocio local</option>
                            <option value="Service" <?= selected($type, 'Service') ?>>Servicio</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Nombre del producto/servicio/negocio</th>
                    <td><input type="text" name="schema_name" value="<?= esc_attr($name) ?>" style="width: 300px;"></td>
                </tr>
                <tr>
                    <th scope="row">Segundos entre scrolls</th>
                    <td>
                        <input type="number" name="scroll_delay" value="<?= esc_attr($scroll_delay) ?>" min="1" step="1"> segundos
                    </td>
                </tr>
            </table>
            <?php submit_button('Guardar ajustes'); ?>
        </form>
    </div>
    <?php
}


