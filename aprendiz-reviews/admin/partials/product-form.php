<?php
if (!defined('ABSPATH')) {
    exit;
}

$is_edit = !empty($product);
$page_title = $is_edit ? __('✏️ Editar Producto', 'aprendiz-reviews') : __('➕ Añadir Producto', 'aprendiz-reviews');
?>

<div class="wrap">
    <h1><?php echo esc_html($page_title); ?></h1>

    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <form method="post">
        <?php if ($is_edit): ?>
            <input type="hidden" name="editar_id" value="<?php echo esc_attr($product->id); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="nombre"><?php echo esc_html__('Nombre *', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="nombre"
                        name="nombre"
                        value="<?php echo esc_attr($product->nombre ?? ''); ?>"
                        required
                        style="width: 100%;"
                        placeholder="<?php echo esc_attr__('Ej: Consultoría SEO', 'aprendiz-reviews'); ?>">
                    <p class="description"><?php echo esc_html__('Nombre del producto que se mostrará en las reseñas.', 'aprendiz-reviews'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="shortcode"><?php echo esc_html__('Shortcode *', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="shortcode"
                        name="shortcode"
                        value="<?php echo esc_attr($product->shortcode ?? ''); ?>"
                        required
                        style="width: 100%;"
                        placeholder="<?php echo esc_attr__('reviews_mi_producto', 'aprendiz-reviews'); ?>"
                        pattern="[a-zA-Z0-9_]+"
                        title="<?php echo esc_attr__('Solo letras, números y guiones bajos', 'aprendiz-reviews'); ?>">
                    <p class="description">
                        <?php echo esc_html__('Shortcode único para mostrar reseñas de este producto. Solo letras, números y guiones bajos. Ejemplo:', 'aprendiz-reviews'); ?>
                        <code>[reviews_mi_producto]</code>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="tipo"><?php echo esc_html__('Tipo de Schema *', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <select id="tipo" name="tipo" required>
                        <?php
                        $tipos = array('Product', 'LocalBusiness', 'Organization');
                        foreach ($tipos as $schema_tipo):
                            $selected = ($product->tipo ?? 'Product') === $schema_tipo ? 'selected' : '';
                        ?>
                            <option value="<?php echo esc_attr($schema_tipo); ?>" <?php echo esc_attr($selected); ?>>
                                <?php echo esc_html($schema_tipo); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php echo esc_html__('Tipo de schema para SEO:', 'aprendiz-reviews'); ?><br>
                        • <strong>Product</strong>: <?php echo esc_html__('Para productos físicos o digitales', 'aprendiz-reviews'); ?><br>
                        • <strong>LocalBusiness</strong>: <?php echo esc_html__('Para negocios locales', 'aprendiz-reviews'); ?><br>
                        • <strong>Organization</strong>: <?php echo esc_html__('Para organizaciones y empresas', 'aprendiz-reviews'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="descripcion"><?php echo esc_html__('Descripción', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <textarea id="descripcion"
                        name="descripcion"
                        rows="3"
                        style="width: 100%;"
                        placeholder="<?php echo esc_attr__('Descripción breve del producto', 'aprendiz-reviews'); ?>"><?php echo esc_textarea($product->descripcion ?? ''); ?></textarea>
                    <p class="description"><?php echo esc_html__('Descripción que aparecerá en el schema estructurado para SEO.', 'aprendiz-reviews'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="url"><?php echo esc_html__('URL del Producto', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <input type="url"
                        id="url"
                        name="url"
                        value="<?php echo esc_attr($product->url ?? ''); ?>"
                        style="width: 100%;"
                        placeholder="<?php echo esc_attr__('https://ejemplo.com/mi-producto', 'aprendiz-reviews'); ?>">
                    <p class="description"><?php echo esc_html__('URL oficial del producto (opcional).', 'aprendiz-reviews'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="imagen_url"><?php echo esc_html__('URL de Imagen', 'aprendiz-reviews'); ?></label>
                </th>
                <td>
                    <input type="url"
                        id="imagen_url"
                        name="imagen_url"
                        value="<?php echo esc_attr($product->imagen_url ?? ''); ?>"
                        style="width: 100%;"
                        placeholder="<?php echo esc_attr__('https://ejemplo.com/imagen-producto.jpg', 'aprendiz-reviews'); ?>">
                    <p class="description"><?php echo esc_html__('URL de imagen del producto para SEO.', 'aprendiz-reviews'); ?></p>
                </td>
            </tr>
        </table>

        <?php submit_button(esc_html__('Guardar producto', 'aprendiz-reviews')); ?>
    </form>
</div>