<?php
if (!defined('ABSPATH')) {
    exit;
}

$is_edit = !empty($product);
$page_title = $is_edit ? '✏️ Editar Producto' : '➕ Añadir Producto';
?>

<div class="wrap">
    <h1><?php echo $page_title; ?></h1>
    
    <?php if (isset($message) && !empty($message)): ?>
        <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <?php if ($is_edit): ?>
            <input type="hidden" name="editar_id" value="<?php echo $product->id; ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="nombre">Nombre *</label>
                </th>
                <td>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="<?php echo esc_attr($product->nombre ?? ''); ?>" 
                           required 
                           style="width: 100%;"
                           placeholder="Ej: Consultoría SEO">
                    <p class="description">Nombre del producto que se mostrará en las reseñas.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="shortcode">Shortcode *</label>
                </th>
                <td>
                    <input type="text" 
                           id="shortcode" 
                           name="shortcode" 
                           value="<?php echo esc_attr($product->shortcode ?? ''); ?>" 
                           required 
                           style="width: 100%;"
                           placeholder="reviews_mi_producto"
                           pattern="[a-zA-Z0-9_]+"
                           title="Solo letras, números y guiones bajos">
                    <p class="description">
                        Shortcode único para mostrar reseñas de este producto. 
                        Solo letras, números y guiones bajos. 
                        Ejemplo: <code>[reviews_mi_producto]</code>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="tipo">Tipo de Schema *</label>
                </th>
                <td>
                    <select id="tipo" name="tipo" required>
                        <?php 
                        $tipos = array('Product', 'LocalBusiness', 'Organization');
                        foreach ($tipos as $schema_tipo): 
                            $selected = ($product->tipo ?? 'Product') === $schema_tipo ? 'selected' : '';
                        ?>
                            <option value="<?php echo $schema_tipo; ?>" <?php echo $selected; ?>>
                                <?php echo $schema_tipo; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        Tipo de schema para SEO:
                        <br>• <strong>Product</strong>: Para productos físicos o digitales
                        <br>• <strong>LocalBusiness</strong>: Para negocios locales
                        <br>• <strong>Organization</strong>: Para organizaciones y empresas
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="descripcion">Descripción</label>
                </th>
                <td>
                    <textarea id="descripcion" 
                              name="descripcion" 
                              rows="3" 
                              style="width: 100%;"
                              placeholder="Descripción breve del producto"><?php echo esc_textarea($product->descripcion ?? ''); ?></textarea>
                    <p class="description">Descripción que aparecerá en el schema estructurado para SEO.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="url">URL del Producto</label>
                </th>
                <td>
                    <input type="url" 
                           id="url" 
                           name="url" 
                           value="<?php echo esc_attr($product->url ?? ''); ?>" 
                           style="width: 100%;"
                           placeholder="https://ejemplo.com/mi-producto">
                    <p class="description">URL oficial del producto (opcional).</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="imagen_url">URL de Imagen</label>
                </th>
                <td>
                    <input type="url" 
                           id="imagen_url" 
                           name="imagen_url" 
                           value="<?php echo esc_attr($product->imagen_url ?? ''); ?>" 
                           style="width: 100%;"
                           placeholder="https://ejemplo.com/imagen-producto.jpg">
                    <p class="description">URL de imagen
