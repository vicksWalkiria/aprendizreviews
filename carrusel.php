<?php
/*
Plugin Name: Aprendiz Reviews
Plugin URI: https://aprendizdeseo.top/plugin-reviews
Description: Plugin de carrusel de reseñas desarrollado por Aprendiz de SEO. Permite añadir, validar y mostrar reseñas con avatar y shortcode.
Version: 1.3
Author: Aprendiz de SEO
Author URI: https://aprendizdeseo.top
*/


register_activation_hook(__FILE__, 'cr_instalar_tabla');

function cr_instalar_tabla() {
    global $wpdb;
    // Crear tabla de productos/servicios (NUEVA)
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    $charset_collate = $wpdb->get_charset_collate();

    $sql_productos = "CREATE TABLE $tabla_productos (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        tipo ENUM('Product', 'LocalBusiness', 'Organization') NOT NULL DEFAULT 'Product',
        shortcode VARCHAR(50) UNIQUE NOT NULL,
        descripcion TEXT,
        url VARCHAR(255),
        imagen_url VARCHAR(255),
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        activo TINYINT(1) DEFAULT 1,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Crear/actualizar tabla de reseñas (MODIFICADA)
    $tabla_resenas = $wpdb->prefix . 'reseñas';

    $sql_resenas = "CREATE TABLE $tabla_resenas (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        texto TEXT NOT NULL,
        valoracion TINYINT(1) NOT NULL,
        fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
        validado TINYINT(1) DEFAULT 0,
        avatar_url VARCHAR(255) DEFAULT NULL,
        producto_servicio_id INT(11) DEFAULT 1,
        PRIMARY KEY (id),
        KEY idx_producto_servicio (producto_servicio_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql_productos);
    dbDelta($sql_resenas);

    // Insertar producto por defecto si no existe
    $existe_default = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_productos WHERE id = 1");
    if ($existe_default == 0) {
        $wpdb->insert($tabla_productos, [
            'id' => 1,
            'nombre' => 'General',
            'tipo' => 'Product',
            'shortcode' => 'reviews_general',
            'descripcion' => 'Reseñas generales del sitio web'
        ]);
    }
}

// Añadir página al admin
// Modificar la función add_action para el admin_menu
add_action('admin_menu', function () {
    // Menú principal
    add_menu_page(
        'Aprendiz Reviews',
        'Aprendiz Reviews', 
        'manage_options', 
        'aprendiz-reviews', 
        'cr_dashboard_admin',
        'dashicons-star-filled'
    );
    
    // Submenús
    add_submenu_page(
        'aprendiz-reviews',
        'Dashboard',
        'Dashboard', 
        'manage_options', 
        'aprendiz-reviews'
    );
    
    add_submenu_page(
        'aprendiz-reviews',
        'Productos/Servicios',
        'Productos/Servicios', 
        'manage_options', 
        'gestionar-productos', 
        'cr_gestionar_productos'
    );
    
    add_submenu_page(
        'aprendiz-reviews',
        'Añadir Producto/Servicio',
        'Añadir Producto/Servicio', 
        'manage_options', 
        'añadir-producto', 
        'cr_formulario_producto'
    );
    
    add_submenu_page(
        'aprendiz-reviews',
        'Gestionar Reseñas',
        'Gestionar Reseñas', 
        'manage_options', 
        'gestionar-resenas', 
        'cr_gestionar_resenas'
    );
    
    add_submenu_page(
        'aprendiz-reviews',
        'Añadir Reseña',
        'Añadir Reseña', 
        'manage_options', 
        'añadir-resena', 
        'cr_formulario_admin'
    );
});

/*function cr_dashboard_admin() {
    global $wpdb;
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    $tabla_resenas = $wpdb->prefix . 'reseñas';
    
    $total_productos = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_productos WHERE activo = 1");
    $total_resenas = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_resenas");
    $resenas_validadas = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_resenas WHERE validado = 1");
    
    echo '<div class="wrap">';
    echo '<h1>🎯 Aprendiz Reviews - Dashboard</h1>';
    echo '<div class="dashboard-widgets-wrap">';
    echo '<div class="metabox-holder">';
    echo '<div class="postbox">';
    echo '<h2><span>📊 Estadísticas</span></h2>';
    echo '<div class="inside">';
    echo "<p><strong>Productos/Servicios activos:</strong> $total_productos</p>";
    echo "<p><strong>Total reseñas:</strong> $total_resenas</p>";
    echo "<p><strong>Reseñas validadas:</strong> $resenas_validadas</p>";
    echo '</div></div>';
    
    // Mostrar shortcodes disponibles
    $productos = $wpdb->get_results("SELECT nombre, shortcode FROM $tabla_productos WHERE activo = 1");
    echo '<div class="postbox">';
    echo '<h2><span>📋 Shortcodes Disponibles</span></h2>';
    echo '<div class="inside">';
    foreach ($productos as $producto) {
        echo "<p><code>[{$producto->shortcode}]</code> - {$producto->nombre}</p>";
    }
    echo '</div></div>';
    echo '</div></div></div>';
}*/


// Mostrar formulario
function cr_formulario_admin() {
    global $wpdb;

    $id_editar = isset($_GET['editar_id']) ? intval($_GET['editar_id']) : 0;
    $tabla = $wpdb->prefix . 'reseñas';
    $resena_actual = $id_editar ? $wpdb->get_row("SELECT * FROM $tabla WHERE id = $id_editar") : null;

    if ($_POST) {
        $fecha_input = sanitize_text_field($_POST['fecha']);
        $fecha_final = date('Y-m-d H:i:s', strtotime($fecha_input . ' ' . date('H:i:s')));

        $data = [
            'nombre' => sanitize_text_field($_POST['nombre']),
            'valoracion' => intval($_POST['valoracion']),
            'texto' => sanitize_textarea_field($_POST['texto']),
            'avatar_url' => esc_url_raw($_POST['avatar_url']),
            'validado' => 1,
            'fecha' => $fecha_final,
            'producto_servicio_id' => intval($_POST['producto_servicio_id']) // NUEVA LÍNEA
        ];


        if (!empty($_POST['editar_id'])) {
            $wpdb->update($tabla, $data, ['id' => intval($_POST['editar_id'])]);
            echo '<div class="updated"><p>Reseña actualizada.</p></div>';
        } else {
            $wpdb->insert($tabla, $data);
            echo '<div class="updated"><p>Reseña guardada.</p></div>';
        }

        // Refrescar datos tras guardar
        if (!empty($_POST['editar_id'])) {
            $id_editar = intval($_POST['editar_id']);
            $resena_actual = $wpdb->get_row("SELECT * FROM $tabla WHERE id = $id_editar");
        }
    }
    ?>

    <div class="wrap">
        <h1><?= $id_editar ? 'Editar Reseña' : 'Añadir Reseña' ?></h1>
        <form method="post">
            <input type="hidden" name="editar_id" value="<?= esc_attr($id_editar) ?>">
            <table class="form-table">
                <tr>
                    <th>Nombre</th>
                    <td><input type="text" name="nombre" value="<?= esc_attr($resena_actual->nombre ?? '') ?>" required></td>
                </tr>
                <tr>
                    <th>Valoración (1 a 5)</th>
                    <td><input type="number" name="valoracion" min="1" max="5" value="<?= esc_attr($resena_actual->valoracion ?? 5) ?>" required></td>
                </tr>
                <tr>
                    <th>Texto</th>
                    <td><textarea name="texto" rows="4" required><?= esc_textarea($resena_actual->texto ?? '') ?></textarea></td>
                </tr>
                <tr>
                    <th>Fecha de reseña</th>
                    <td>
                        <input type="date" name="fecha" value="<?= esc_attr(isset($resena_actual->fecha) ? date('Y-m-d', strtotime($resena_actual->fecha)) : date('Y-m-d')) ?>" required>
                    </td>
                </tr>
                <tr>
                    <th>Producto/Servicio *</th>
                    <td>
                        <?php 
                        $tabla_productos = $wpdb->prefix . 'productos_servicios';
                        $productos = $wpdb->get_results("SELECT id, nombre FROM $tabla_productos WHERE activo = 1 ORDER BY nombre");
                        ?>
                        <select name="producto_servicio_id" required>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto->id ?>" <?= ($resena_actual->producto_servicio_id ?? 1) == $producto->id ? 'selected' : '' ?>>
                                    <?= esc_html($producto->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                
                <tr>
                    <th>Avatar</th>
                    <td>
                        <input type="text" id="avatar_url" name="avatar_url" value="<?= esc_attr($resena_actual->avatar_url ?? '') ?>" style="width:60%" readonly>
                        <input type="button" id="upload_avatar_button" class="button" value="Elegir imagen">
                        <div id="avatar_preview" style="margin-top:10px;">
                            <?php if (!empty($resena_actual->avatar_url)): ?>
                                <img src="<?= esc_url($resena_actual->avatar_url) ?>" style="max-width:80px; border-radius:50%;">
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
            <?php submit_button($id_editar ? 'Actualizar reseña' : 'Guardar reseña'); ?>
        </form>
    </div>
    <?php
}


function cr_gestionar_productos() {
    global $wpdb;
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    
    // Procesar acciones
    if (isset($_GET['eliminar_id'])) {
        $id = intval($_GET['eliminar_id']);
        if ($id !== 1) { // No permitir eliminar el producto por defecto
            $wpdb->update($tabla_productos, ['activo' => 0], ['id' => $id]);
            echo '<div class="notice notice-success"><p>Producto/Servicio desactivado.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>No se puede eliminar el producto por defecto.</p></div>';
        }
    }
    
    $productos = $wpdb->get_results("SELECT * FROM $tabla_productos ORDER BY fecha_creacion DESC");
    
    echo '<div class="wrap">';
    echo '<h1>📦 Gestionar Productos/Servicios</h1>';
    echo '<a href="?page=añadir-producto" class="button button-primary">➕ Añadir Nuevo</a>';
    echo '<br><br>';
    
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th>ID</th><th>Nombre</th><th>Tipo</th><th>Shortcode</th><th>Estado</th><th>Reseñas</th><th>Acciones</th>';
    echo '</tr></thead><tbody>';
    
    foreach ($productos as $producto) {
        $total_resenas = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}reseñas WHERE producto_servicio_id = {$producto->id}");
        $estado = $producto->activo ? '✅ Activo' : '❌ Inactivo';
        
        echo '<tr>';
        echo "<td>{$producto->id}</td>";
        echo "<td><strong>{$producto->nombre}</strong><br><small>{$producto->descripcion}</small></td>";
        echo "<td>{$producto->tipo}</td>";
        echo "<td><code>[{$producto->shortcode}]</code></td>";
        echo "<td>{$estado}</td>";
        echo "<td>{$total_resenas}</td>";
        echo "<td>";
        echo "<a href='?page=añadir-producto&editar_id={$producto->id}' class='button'>✏️ Editar</a> ";
        if ($producto->id !== 1) {
            echo "<a href='?page=gestionar-productos&eliminar_id={$producto->id}' class='button button-secondary' onclick='return confirm(\"¿Seguro?\")'>🗑️ Desactivar</a>";
        }
        echo "</td>";
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '</div>';
}


function cr_gestionar_resenas() {
    global $wpdb;
    $tabla_resenas = $wpdb->prefix . 'reseñas';
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    
    // Procesar validaciones masivas
    if (isset($_POST['validar_resenas']) && !empty($_POST['resenas_validar'])) {
        $ids = array_map('intval', $_POST['resenas_validar']);
        $ids_str = implode(',', $ids);
        $wpdb->query("UPDATE $tabla_resenas SET validado = 1 WHERE id IN ($ids_str)");
        echo '<div class="notice notice-success"><p>' . count($ids) . ' reseñas validadas.</p></div>';
    }
    
    // Eliminar reseña
    if (isset($_GET['eliminar_id'])) {
        $id = intval($_GET['eliminar_id']);
        $wpdb->delete($tabla_resenas, ['id' => $id]);
        echo '<div class="notice notice-success"><p>Reseña eliminada.</p></div>';
    }
    
    // Filtros
    $filtro_producto = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;
    $filtro_validado = isset($_GET['validado']) ? intval($_GET['validado']) : -1;
    
    $where = "WHERE 1=1";
    if ($filtro_producto > 0) $where .= " AND r.producto_servicio_id = $filtro_producto";
    if ($filtro_validado >= 0) $where .= " AND r.validado = $filtro_validado";
    
    $resenas = $wpdb->get_results("
        SELECT r.*, p.nombre as producto_nombre 
        FROM $tabla_resenas r 
        LEFT JOIN $tabla_productos p ON r.producto_servicio_id = p.id 
        $where 
        ORDER BY r.fecha DESC
    ");
    
    $productos = $wpdb->get_results("SELECT id, nombre FROM $tabla_productos WHERE activo = 1");
    
    echo '<div class="wrap">';
    echo '<h1>📝 Gestionar Reseñas</h1>';
    echo '<a href="?page=añadir-resena" class="button button-primary">➕ Añadir Nueva</a>';
    echo '<br><br>';
    
    // Filtros
    echo '<div class="tablenav top">';
    echo '<form method="get" style="float:left;margin-right:10px;">';
    echo '<input type="hidden" name="page" value="gestionar-resenas">';
    echo '<select name="producto_id">';
    echo '<option value="0">Todos los productos</option>';
    foreach ($productos as $producto) {
        $selected = $filtro_producto == $producto->id ? 'selected' : '';
        echo "<option value='{$producto->id}' $selected>{$producto->nombre}</option>";
    }
    echo '</select>';
    echo '<select name="validado">';
    echo '<option value="-1">Todas las reseñas</option>';
    echo '<option value="1"' . ($filtro_validado == 1 ? ' selected' : '') . '>Solo validadas</option>';
    echo '<option value="0"' . ($filtro_validado == 0 ? ' selected' : '') . '>Solo pendientes</option>';
    echo '</select>';
    echo '<input type="submit" class="button" value="Filtrar">';
    echo '</form>';
    echo '</div>';
    echo '<div class="clear"></div>';
    
    echo '<form method="post">';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th class="check-column"><input type="checkbox" id="select-all"></th>';
    echo '<th>Reseña</th><th>Producto</th><th>Valoración</th><th>Fecha</th><th>Estado</th><th>Acciones</th>';
    echo '</tr></thead><tbody>';
    
    foreach ($resenas as $resena) {
        $estado = $resena->validado ? '✅ Validada' : '⏳ Pendiente';
        $estrellas = str_repeat('⭐', $resena->valoracion);
        
        echo '<tr>';
        echo "<td><input type='checkbox' name='resenas_validar[]' value='{$resena->id}'></td>";
        echo "<td><strong>{$resena->nombre}</strong><br>" . substr($resena->texto, 0, 100) . "...</td>";
        echo "<td>{$resena->producto_nombre}</td>";
        echo "<td>$estrellas ({$resena->valoracion}/5)</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($resena->fecha)) . "</td>";
        echo "<td>$estado</td>";
        echo "<td>";
        echo "<a href='?page=añadir-resena&editar_id={$resena->id}' class='button'>✏️</a> ";
        echo "<a href='?page=gestionar-resenas&eliminar_id={$resena->id}' class='button button-secondary' onclick='return confirm(\"¿Eliminar?\")'>🗑️</a>";
        echo "</td>";
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '<p class="submit">';
    echo '<input type="submit" name="validar_resenas" class="button button-primary" value="✅ Validar Seleccionadas">';
    echo '</p>';
    echo '</form>';
    
    echo '<script>';
    echo 'document.getElementById("select-all").addEventListener("change", function() {';
    echo 'var checkboxes = document.querySelectorAll("input[name=\'resenas_validar[]\']");';
    echo 'for (var i = 0; i < checkboxes.length; i++) checkboxes[i].checked = this.checked;';
    echo '});';
    echo '</script>';
    
    echo '</div>';
}


function cr_formulario_producto() {
    global $wpdb;
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    
    $id_editar = isset($_GET['editar_id']) ? intval($_GET['editar_id']) : 0;
    $producto_actual = $id_editar ? $wpdb->get_row("SELECT * FROM $tabla_productos WHERE id = $id_editar") : null;
    
    if ($_POST) {
        $nombre = sanitize_text_field($_POST['nombre']);
        $shortcode = sanitize_text_field($_POST['shortcode']);
        $tipo = sanitize_text_field($_POST['tipo']);
        $descripcion = sanitize_textarea_field($_POST['descripcion']);
        $url = esc_url_raw($_POST['url']);
        $imagen_url = esc_url_raw($_POST['imagen_url']);
        
        $data = [
            'nombre' => $nombre,
            'shortcode' => $shortcode,
            'tipo' => $tipo,
            'descripcion' => $descripcion,
            'url' => $url,
            'imagen_url' => $imagen_url,
            'activo' => 1
        ];
        
        if ($id_editar) {
            $wpdb->update($tabla_productos, $data, ['id' => $id_editar]);
            echo '<div class="notice notice-success"><p>Producto/Servicio actualizado.</p></div>';
        } else {
            $wpdb->insert($tabla_productos, $data);
            echo '<div class="notice notice-success"><p>Producto/Servicio creado.</p></div>';
        }
    }
    
    echo '<div class="wrap">';
    echo '<h1>' . ($id_editar ? '✏️ Editar' : '➕ Añadir') . ' Producto/Servicio</h1>';
    
    echo '<form method="post">';
    if ($id_editar) echo '<input type="hidden" name="editar_id" value="' . $id_editar . '">';
    
    echo '<table class="form-table">';
    
    echo '<tr><th>Nombre *</th><td>';
    echo '<input type="text" name="nombre" value="' . esc_attr($producto_actual->nombre ?? '') . '" required style="width:100%">';
    echo '</td></tr>';
    
    echo '<tr><th>Shortcode *</th><td>';
    echo '<input type="text" name="shortcode" value="' . esc_attr($producto_actual->shortcode ?? '') . '" required style="width:100%" placeholder="reviews_mi_producto">';
    echo '<p class="description">Ejemplo: reviews_mi_producto (solo letras, números y guiones bajos)</p>';
    echo '</td></tr>';
    
    echo '<tr><th>Tipo de Schema *</th><td>';
    $tipos = ['Product', 'LocalBusiness', 'Organization'];
    echo '<select name="tipo" required>';
        foreach ($tipos as $schema_tipo) {       // ← ANTES ponías $tipo
            $selected = ($producto_actual->tipo ?? 'Product') === $schema_tipo ? 'selected' : '';
            echo "<option value='$schema_tipo' $selected>$schema_tipo</option>";
        }
        echo '</select>';
    echo '</td></tr>';
    
    echo '<tr><th>Descripción</th><td>';
    echo '<textarea name="descripcion" rows="3" style="width:100%">' . esc_textarea($producto_actual->descripcion ?? '') . '</textarea>';
    echo '</td></tr>';
    
    echo '<tr><th>URL</th><td>';
    echo '<input type="url" name="url" value="' . esc_attr($producto_actual->url ?? '') . '" style="width:100%">';
    echo '</td></tr>';
    
    echo '<tr><th>Imagen URL</th><td>';
    echo '<input type="url" name="imagen_url" value="' . esc_attr($producto_actual->imagen_url ?? '') . '" style="width:100%">';
    echo '</td></tr>';
    
    echo '</table>';
    
    echo '<p class="submit">';
    echo '<input type="submit" class="button-primary" value="' . ($id_editar ? 'Actualizar' : 'Crear') . ' Producto/Servicio">';
    echo '</p>';
    
    echo '</form>';
    echo '</div>';
}



// Eliminar el shortcode fijo
// add_shortcode('reviews', 'cr_mostrar_reviews'); <- BORRA ESTA LÍNEA

// Añadir sistema dinámico de shortcodes
add_action('init', 'cr_registrar_shortcodes_dinamicos');
function cr_registrar_shortcodes_dinamicos() {
    global $wpdb;
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    $productos = $wpdb->get_results("SELECT shortcode FROM $tabla_productos WHERE activo = 1");
    
    foreach ($productos as $producto) {
        add_shortcode($producto->shortcode, 'cr_mostrar_reviews_dinamico');
    }
}

function cr_mostrar_reviews_dinamico($atts, $content, $tag) {
    global $wpdb;
    
    // Obtener el producto por shortcode
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    $producto = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $tabla_productos WHERE shortcode = %s AND activo = 1", 
        $tag
    ));
    
    if (!$producto) return '<p>Producto no encontrado.</p>';
    
    // Obtener reseñas de este producto específico
    $tabla_resenas = $wpdb->prefix . 'reseñas';
    $resenas = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $tabla_resenas WHERE validado = 1 AND producto_servicio_id = %d ORDER BY fecha DESC LIMIT 10",
        $producto->id
    ));
    
    if (empty($resenas)) return '<p>No hay reseñas para este producto.</p>';

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
    // Calcular total para rating promedio
    $total = 0;
    $review_array = [];
    
    foreach ($resenas as $r) {
        $total += $r->valoracion;
        
        // Crear array de reseñas para el schema
        $review_array[] = [
            "@type" => "Review",
            "author" => [
                "@type" => "Person",
                "name" => $r->nombre
            ],
            "datePublished" => date('Y-m-d', strtotime($r->fecha)),
            "reviewBody" => $r->texto,
            "reviewRating" => [
                "@type" => "Rating",
                "ratingValue" => (string)$r->valoracion,
                "bestRating" => "5"
            ]
        ];
    }

    // Schema único con todo integrado
    $schema = [
        "@context" => "https://schema.org",
        "@type" => $producto->tipo,
        "name" => $producto->nombre,
        "description" => $producto->descripcion ?: "Opiniones reales de clientes verificadas.",
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => round($total / count($resenas), 1),
            "bestRating" => "5",
            "ratingCount" => count($resenas)
        ],
        "review" => $review_array
    ];

    // Añadir campos adicionales
    if ($producto->url) $schema['url'] = $producto->url;
    if ($producto->imagen_url) $schema['image'] = $producto->imagen_url;

    if ($producto->tipo === 'LocalBusiness') {
        $direccion = get_option('cr_schema_address', '');
        if ($direccion) {
            $schema['address'] = [
                "@type" => "PostalAddress",
                "streetAddress" => $direccion
            ];
        }
    }

    // Un solo script JSON-LD
    echo '<script type="application/ld+json">' .
        wp_json_encode($schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) .
        '</script>';
    
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
                        <th>Acciones</th>
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
                                <a href="<?= admin_url('admin.php?page=añadir-resena&editar_id=' . $r->id) ?>" class="button">✏️ Editar</a>
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
    // Páginas donde necesitamos el media uploader
    $paginas_permitidas = [
        'aprendiz-reviews_page_añadir-resena',     // Añadir reseña
        'toplevel_page_aprendiz-reviews',         // Dashboard
        'aprendiz-reviews_page_añadir-producto'   // Añadir producto (por si usas media ahí)
    ];
    
    if (!in_array($hook, $paginas_permitidas)) return;
    
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
        update_option('cr_schema_address', sanitize_text_field($_POST['schema_address']));

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
                        </select>
                    </td>
                </tr>
                <tr id="direccion_row">
                    <th scope="row">Dirección del negocio</th>
                    <td>
                        <input type="text" name="schema_address" value="<?= esc_attr(get_option('cr_schema_address', '')) ?>" style="width: 300px;">
                        <p class="description">Solo se usará si el tipo es "Negocio local".</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Nombre del producto/negocio</th>
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoSelect = document.querySelector('select[name="schema_type"]');
        const filaDireccion = document.getElementById('direccion_row');

        function toggleDireccion() {
            if (tipoSelect.value === 'LocalBusiness') {
                filaDireccion.style.display = '';
            } else {
                filaDireccion.style.display = 'none';
            }
        }

        tipoSelect.addEventListener('change', toggleDireccion);
        toggleDireccion();
    });
    </script>

    <?php
}




// Ejecutará la migración automáticamente una sola vez

add_action('admin_init', 'cr_migrar_datos_antiguos');

function cr_migrar_datos_antiguos() {
    global $wpdb;
    
    // Verificar si ya se ejecutó la migración
    if (get_option('cr_migracion_completada', false)) {
        return;
    }
    
    $tabla_resenas = $wpdb->prefix . 'reseñas';
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    
    // 1. Verificar si existe la tabla de productos
    $tabla_productos_existe = $wpdb->get_var("SHOW TABLES LIKE '$tabla_productos'") == $tabla_productos;
    if (!$tabla_productos_existe) {
        // Ejecutar instalación si no existe
        cr_instalar_tabla();
    }
    
    // 2. Verificar si la columna producto_servicio_id existe en reseñas
    $columna_existe = $wpdb->get_results("SHOW COLUMNS FROM $tabla_resenas LIKE 'producto_servicio_id'");
    
    if (empty($columna_existe)) {
        // Añadir la columna si no existe
        $wpdb->query("ALTER TABLE $tabla_resenas ADD COLUMN producto_servicio_id INT(11) DEFAULT 1");
        $wpdb->query("ALTER TABLE $tabla_resenas ADD KEY idx_producto_servicio (producto_servicio_id)");
    }
    
    // 3. Verificar que existe el producto por defecto
    $producto_default = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_productos WHERE id = 1");
    if ($producto_default == 0) {
        $wpdb->insert($tabla_productos, [
            'id' => 1,
            'nombre' => 'General',
            'tipo' => 'Service',
            'shortcode' => 'reviews_general',
            'descripcion' => 'Reseñas generales del sitio web (migradas automáticamente)'
        ]);
    }
    
    // 4. Asignar todas las reseñas existentes al producto por defecto
    $wpdb->query("UPDATE $tabla_resenas SET producto_servicio_id = 1 WHERE producto_servicio_id IS NULL OR producto_servicio_id = 0");
    
    // 5. Marcar migración como completada
    update_option('cr_migracion_completada', true);
    update_option('cr_fecha_migracion', current_time('mysql'));
    
    // 6. Mostrar mensaje de éxito
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>Aprendiz Reviews:</strong> Migración de datos completada exitosamente. Todas tus reseñas existentes están ahora bajo el producto "General" con shortcode [reviews_general]</p>';
        echo '</div>';
    });
}

// Función para resetear migración (solo para debug)
function cr_resetear_migracion() {
    delete_option('cr_migracion_completada');
    delete_option('cr_fecha_migracion');
}

// Añadir información de migración al dashboard
function cr_dashboard_admin() {
    global $wpdb;
    $tabla_productos = $wpdb->prefix . 'productos_servicios';
    $tabla_resenas = $wpdb->prefix . 'reseñas';
    
    $total_productos = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_productos WHERE activo = 1");
    $total_resenas = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_resenas");
    $resenas_validadas = $wpdb->get_var("SELECT COUNT(*) FROM $tabla_resenas WHERE validado = 1");
    $fecha_migracion = get_option('cr_fecha_migracion', false);
    
    echo '<div class="wrap">';
    echo '<h1>🎯 Aprendiz Reviews - Dashboard</h1>';
    
    // Mostrar información de migración si existe
    if ($fecha_migracion) {
        echo '<div class="notice notice-info">';
        echo '<p><strong>ℹ️ Migración completada:</strong> ' . date('d/m/Y H:i', strtotime($fecha_migracion)) . '</p>';
        echo '<p>Todas las reseñas anteriores están disponibles en el producto "General" con shortcode <code>[reviews_general]</code></p>';
        echo '</div>';
    }
    
    echo '<div class="dashboard-widgets-wrap">';
    echo '<div class="metabox-holder">';
    echo '<div class="postbox">';
    echo '<h2><span>📊 Estadísticas</span></h2>';
    echo '<div class="inside">';
    echo "<p><strong>Productos/Servicios activos:</strong> $total_productos</p>";
    echo "<p><strong>Total reseñas:</strong> $total_resenas</p>";
    echo "<p><strong>Reseñas validadas:</strong> $resenas_validadas</p>";
    echo '</div></div>';
    
    // Mostrar shortcodes disponibles
    $productos = $wpdb->get_results("SELECT nombre, shortcode FROM $tabla_productos WHERE activo = 1");
    echo '<div class="postbox">';
    echo '<h2><span>📋 Shortcodes Disponibles</span></h2>';
    echo '<div class="inside">';
    foreach ($productos as $producto) {
        echo "<p><code>[{$producto->shortcode}]</code> - {$producto->nombre}</p>";
    }
    echo '</div></div>';
    echo '</div></div></div>';
}

